<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;
use App\Models\AuditLog;

class SettingController extends Controller
{
    public function index()
    {
        $settings = AppSetting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            AppSetting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : $value]
            );
        }

        AuditLog::create([
            'user_id' => $request->user()?->user_id,
            'user_name' => $request->user()?->full_name ?? 'Super Admin',
            'action' => 'Settings updated',
            'record' => 'System Settings',
            'entity_type' => 'AppSetting',
            'entity_id' => 'all',
            'old_value' => '—',
            'new_value' => 'Updated by Super Admin',
            'created_at' => now(),
        ]);

        return response()->json(['message' => 'Settings saved successfully', 'settings' => AppSetting::all()->pluck('value', 'key')]);
    }
}
