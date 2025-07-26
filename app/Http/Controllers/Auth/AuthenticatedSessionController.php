<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();

            $user = Auth::user();

            if (!$user) {
                Log::error('User not found after authentication.');
                return response()->json([
                    'message' => 'Authentication failed. User not found.'
                ], 401);
            }

            if (!method_exists($user, 'createToken')) {
                Log::error('createToken method not available on user model.');
                return response()->json([
                    'message' => 'Token creation failed. Sanctum might not be configured properly.'
                ], 500);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user
            ]);
        } catch (\Throwable $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Something went wrong during login.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'message' => 'User is not authenticated.'
                ], 401);
            }

            $user = Auth::user();
            $user->tokens()->delete();

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Logout successful'
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Logout error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Something went wrong during logout.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
