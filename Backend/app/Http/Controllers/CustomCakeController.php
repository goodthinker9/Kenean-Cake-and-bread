<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomCakeRequest;
use App\Models\CustomCakeImage;
use App\Models\Branch;
use App\Models\AuditLog;
use App\Models\Notification;

class CustomCakeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = CustomCakeRequest::with(['branch', 'user', 'images'])->latest('created_at');

        if ($user && in_array($user->role, ['Branch Admin', 'Branch Staff']) && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->has('branch_id') && $request->branch_id && $request->branch_id !== 'all') {
            $query->where('branch_id', $request->branch_id);
        }

        if ($user && $user->role === 'Customer') {
            $query->where('user_id', $user->user_id);
        }

        $requests = $query->get();

        // Calculate card metrics
        $allForMetrics = CustomCakeRequest::query();
        if ($user && in_array($user->role, ['Branch Admin', 'Branch Staff']) && $user->branch_id) {
            $allForMetrics->where('branch_id', $user->branch_id);
        }

        $submittedCount = (clone $allForMetrics)->where('status', 'Submitted')->count();
        $quotedCount = (clone $allForMetrics)->where('status', 'Quoted')->count();
        $confirmedCount = (clone $allForMetrics)->where('status', 'Confirmed')->count();
        $readyThisWeekCount = (clone $allForMetrics)->where('status', 'Ready')->count();

        return response()->json([
            'requests' => $requests,
            'summary' => [
                'submitted' => $submittedCount,
                'quoted' => $quotedCount,
                'confirmed' => $confirmedCount,
                'ready_this_week' => $readyThisWeekCount,
            ]
        ]);
    }

    public function show(Request $request, $id)
    {
        $cakeRequest = CustomCakeRequest::with(['branch', 'user', 'images'])->findOrFail($id);
        $user = $request->user();
        if ($user && in_array($user->role, ['Branch Admin', 'Branch Staff']) && $user->branch_id && $cakeRequest->branch_id != $user->branch_id) {
            return response()->json(['message' => 'Access denied: You can only view custom cake requests for your assigned branch.'], 403);
        }
        return response()->json($cakeRequest);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Harmonize field aliases from frontend
        if (!$request->has('cake_size') && $request->has('size')) {
            $request->merge(['cake_size' => $request->size]);
        }
        if (!$request->has('needed_by') && $request->has('needed_by_date')) {
            $request->merge(['needed_by' => $request->needed_by_date]);
        }
        if (!$request->has('design') && $request->has('description')) {
            $desc = $request->description;
            if ($request->has('color_scheme')) {
                $desc .= ' [Colors: ' . $request->color_scheme . ']';
            }
            $request->merge(['design' => $desc]);
        }
        if (!$request->has('message') && $request->has('message_on_cake')) {
            $request->merge(['message' => $request->message_on_cake]);
        }

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,branch_id',
            'cake_size' => 'required|string',
            'flavor' => 'required|string',
            'design' => 'nullable|string',
            'message' => 'nullable|string',
            'needed_by' => 'required|date|after_or_equal:today',
            'image_urls' => 'nullable|array',
            'image_urls.*' => 'nullable|string',
        ]);

        $rand = mt_rand(100, 999);
        $reqNum = "CC-0{$rand}";
        while (CustomCakeRequest::where('request_number', $reqNum)->exists()) {
            $reqNum = "CC-0" . mt_rand(100, 999);
        }

        $cakeReq = CustomCakeRequest::create([
            'request_number' => $reqNum,
            'user_id' => $user?->user_id ?? 1,
            'branch_id' => $validated['branch_id'],
            'cake_size' => $validated['cake_size'],
            'flavor' => $validated['flavor'],
            'design' => $validated['design'] ?? null,
            'message' => $validated['message'] ?? null,
            'needed_by' => $validated['needed_by'],
            'deposit_required_percentage' => 50.00,
            'status' => 'Submitted',
        ]);

        if (!empty($validated['image_urls'])) {
            foreach ($validated['image_urls'] as $url) {
                if ($url) {
                    CustomCakeImage::create([
                        'request_id' => $cakeReq->request_id,
                        'image_url' => $url,
                    ]);
                }
            }
        }

        AuditLog::create([
            'user_id' => $user?->user_id,
            'user_name' => $user ? ($user->full_name . ' (' . $user->role . ')') : 'Customer',
            'action' => 'Custom cake submitted',
            'record' => $cakeReq->request_number,
            'entity_type' => 'CustomCakeRequest',
            'entity_id' => (string) $cakeReq->request_id,
            'old_value' => '—',
            'new_value' => 'Submitted',
            'created_at' => now(),
        ]);

        Notification::create([
            'user_id' => null,
            'category' => 'Orders',
            'title' => 'Custom cake request',
            'message' => "{$cakeReq->request_number} at {$cakeReq->branch?->branch_name} is waiting for a quote.",
            'link' => '/admin/custom-cakes',
        ]);

        return response()->json($cakeReq->load(['branch', 'images']), 201);
    }

    public function quote(Request $request, $id)
    {
        $cakeReq = CustomCakeRequest::findOrFail($id);
        $user = $request->user();

        if ($user && $user->role === 'Branch Staff') {
            return response()->json(['message' => 'Branch staff are not permitted to quote custom cakes.'], 403);
        }
        if ($user && $user->role === 'Branch Admin' && $user->branch_id != $cakeReq->branch_id) {
            return response()->json(['message' => 'You can only quote custom cakes for your assigned branch.'], 403);
        }

        if (!$request->has('deposit_required_percentage') && $request->has('deposit_percentage')) {
            $request->merge(['deposit_required_percentage' => $request->deposit_percentage]);
        }
        if (!$request->has('deposit_required_percentage')) {
            $request->merge(['deposit_required_percentage' => 50]);
        }

        $validated = $request->validate([
            'quoted_price' => 'required|numeric|min:1',
            'deposit_required_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $cakeReq->quoted_price = $validated['quoted_price'];
        $cakeReq->deposit_required_percentage = $validated['deposit_required_percentage'];
        $cakeReq->status = 'Quoted';
        $cakeReq->save();

        AuditLog::create([
            'user_id' => $request->user()?->user_id,
            'user_name' => $request->user()?->full_name ?? 'Branch Admin',
            'action' => 'Custom cake quoted',
            'record' => $cakeReq->request_number,
            'entity_type' => 'CustomCakeRequest',
            'entity_id' => (string) $cakeReq->request_id,
            'old_value' => 'Submitted',
            'new_value' => 'Quoted (ETB ' . number_format($validated['quoted_price'], 0) . ')',
            'created_at' => now(),
        ]);

        return response()->json($cakeReq);
    }

    public function updateStatus(Request $request, $id)
    {
        $cakeReq = CustomCakeRequest::findOrFail($id);
        $user = $request->user();

        if ($user && in_array($user->role, ['Branch Admin', 'Branch Staff']) && $user->branch_id && $cakeReq->branch_id != $user->branch_id) {
            return response()->json(['message' => 'You can only manage custom cakes for your assigned branch.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:Confirmed,Preparing,Ready,Picked up,Delivered,Rejected,Cancelled',
            'rejection_reason' => 'nullable|string',
        ]);

        $oldStatus = $cakeReq->status;
        $cakeReq->status = $validated['status'];
        if (isset($validated['rejection_reason'])) {
            $cakeReq->rejection_reason = $validated['rejection_reason'];
        }
        $cakeReq->save();

        AuditLog::create([
            'user_id' => $request->user()?->user_id,
            'user_name' => $request->user()?->full_name ?? 'Branch Admin',
            'action' => 'Custom cake status updated',
            'record' => $cakeReq->request_number,
            'entity_type' => 'CustomCakeRequest',
            'entity_id' => (string) $cakeReq->request_id,
            'old_value' => $oldStatus,
            'new_value' => $validated['status'],
            'created_at' => now(),
        ]);

        return response()->json($cakeReq);
    }

    public function acceptQuote(Request $request, $id)
    {
        $cakeReq = CustomCakeRequest::findOrFail($id);
        $user = $request->user();

        if ($user && $cakeReq->user_id && $cakeReq->user_id != $user->user_id && !in_array($user->role, ['Super Admin', 'Main Branch Manager'])) {
            return response()->json(['message' => 'Unauthorized: You can only accept quotes for your own custom cake orders.'], 403);
        }

        if ($cakeReq->status !== 'Quoted') {
            return response()->json(['message' => "Only custom cakes in 'Quoted' status can be accepted (current: {$cakeReq->status})."], 422);
        }

        $validated = $request->validate([
            'payment_choice' => 'nullable|in:deposit,full',
            'payment_method' => 'nullable|string',
        ]);

        $paymentChoice = $validated['payment_choice'] ?? 'deposit';
        $depositPct = (float) ($cakeReq->deposit_required_percentage ?? 50.0);
        $amountPaid = $paymentChoice === 'full' ? (float) $cakeReq->quoted_price : ((float) $cakeReq->quoted_price * ($depositPct / 100.0));

        $cakeReq->status = 'Confirmed';
        $cakeReq->save();

        AuditLog::create([
            'user_id' => $user?->user_id,
            'user_name' => $user ? ($user->full_name . ' (' . $user->role . ')') : 'Customer',
            'action' => 'Custom cake quote accepted',
            'record' => $cakeReq->request_number,
            'entity_type' => 'CustomCakeRequest',
            'entity_id' => (string) $cakeReq->request_id,
            'old_value' => 'Quoted',
            'new_value' => 'Confirmed (ETB ' . number_format($amountPaid, 0) . ' paid)',
            'created_at' => now(),
        ]);

        Notification::create([
            'user_id' => null,
            'category' => 'Orders',
            'title' => 'Custom cake quote accepted',
            'message' => "Customer accepted quote for {$cakeReq->request_number} at {$cakeReq->branch?->branch_name}. Ready for preparation.",
            'link' => '/admin/custom-cakes',
        ]);

        return response()->json([
            'message' => 'Custom cake quote accepted and status confirmed.',
            'cake_request' => $cakeReq->load(['branch', 'images']),
            'amount_paid' => $amountPaid,
            'payment_choice' => $paymentChoice,
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $cakeReq = CustomCakeRequest::findOrFail($id);
        $user = $request->user();

        if ($user && $cakeReq->user_id && $cakeReq->user_id != $user->user_id && !in_array($user->role, ['Super Admin', 'Main Branch Manager'])) {
            return response()->json(['message' => 'Unauthorized: You can only cancel your own custom cake orders.'], 403);
        }

        if (in_array($cakeReq->status, ['Cancelled', 'Rejected', 'Delivered', 'Picked up'])) {
            return response()->json(['message' => "Cannot cancel cake request in '{$cakeReq->status}' status."], 422);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string',
        ]);

        // Evaluate refund rules based on needed_by date
        $neededDate = \Carbon\Carbon::parse($cakeReq->needed_by);
        $hoursRemaining = now()->diffInHours($neededDate, false);

        $refundEligible = 'None';
        if ($cakeReq->status === 'Submitted' || $cakeReq->status === 'Quoted') {
            $refundEligible = 'N/A (No payment was made)';
        } elseif ($hoursRemaining >= 48 && $cakeReq->status !== 'Preparing') {
            $refundEligible = '100% full deposit refund';
        } elseif ($hoursRemaining >= 24 && $cakeReq->status !== 'Preparing') {
            $refundEligible = '50% deposit refund';
        } else {
            $refundEligible = '0% (Non-refundable: less than 24 hours before needed date or baking has started)';
        }

        $oldStatus = $cakeReq->status;
        $cakeReq->status = 'Cancelled';
        $cakeReq->rejection_reason = $validated['reason'] ?? 'Cancelled by customer';
        $cakeReq->save();

        AuditLog::create([
            'user_id' => $user?->user_id,
            'user_name' => $user ? ($user->full_name . ' (' . $user->role . ')') : 'Customer',
            'action' => 'Custom cake cancelled',
            'record' => $cakeReq->request_number,
            'entity_type' => 'CustomCakeRequest',
            'entity_id' => (string) $cakeReq->request_id,
            'old_value' => $oldStatus,
            'new_value' => "Cancelled (Refund rule: {$refundEligible})",
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Custom cake request cancelled.',
            'refund_policy_applied' => $refundEligible,
            'cake_request' => $cakeReq->load(['branch', 'images']),
        ]);
    }
}
