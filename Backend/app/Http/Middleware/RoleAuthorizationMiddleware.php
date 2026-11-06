<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class RoleAuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$allowedRoles): Response
    {
        // 1. Resolve user via Sanctum bearer token or auth guard
        $user = $request->user();

        if (!$user) {
            $tokenString = $request->bearerToken();
            if ($tokenString) {
                $token = PersonalAccessToken::findToken($tokenString);
                if ($token) {
                    $user = $token->tokenable;
                    $request->setUserResolver(fn () => $user);
                }
            }
        }

        // Fallback for development if no token provided: Super Admin
        if (!$user && app()->environment('local', 'testing')) {
            $user = User::where('role', 'Super Admin')->first();
            if ($user) {
                $request->setUserResolver(fn () => $user);
            }
        }

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Check if customer is attempting to access admin endpoints
        if ($user->role === 'Customer' && $request->is('api/admin*')) {
            return response()->json([
                'message' => 'Access denied: Customers are not permitted to access bakery administration.'
            ], 403);
        }

        // Check specific allowed roles if specified on route
        if (!empty($allowedRoles) && !in_array($user->role, $allowedRoles)) {
            return response()->json([
                'message' => "Access denied: This action requires one of the following roles: " . implode(', ', $allowedRoles) . ". Your role is: {$user->role}."
            ], 403);
        }

        return $next($request);
    }
}
