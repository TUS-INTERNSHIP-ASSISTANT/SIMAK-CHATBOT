<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt login via web session guard (untuk proteksi web route dashboard)
        if (! Auth::guard('web')->attempt($credentials, remember: false)) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.'
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::guard('web')->user();

        // Buat Sanctum API token (untuk kebutuhan API call di dalam dashboard)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Login successful',
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

    /**
     * Web-based login untuk form di browser.
     *
     * Berbeda dengan /api/login (stateless), method ini berjalan
     * di atas web middleware stack yang sudah include StartSession,
     * sehingga session benar-benar tersimpan dan middleware
     * 'dashboard.auth' dapat memvalidasinya.
     *
     * Juga tetap mengembalikan Sanctum token untuk kebutuhan
     * API call dari dalam halaman dashboard (Axios request).
     */
    public function webLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Login via web guard — session tersimpan di sini
        if (! Auth::guard('web')->attempt($credentials, remember: false)) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.'
            ], 401);
        }

        // Regenerasi session ID untuk mencegah session fixation attack
        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::guard('web')->user();

        // Buat Sanctum token untuk API call di dalam dashboard
        $token = $user->createToken('dashboard_token')->plainTextToken;

        return response()->json([
            'message'      => 'Login successful',
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        // Hapus Sanctum token saja (untuk API client)
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Web-based logout: hapus session + redirect ke login.
     * Dipanggil oleh form logout di halaman dashboard.
     */
    public function webLogout(Request $request)
    {
        // Hapus semua Sanctum token milik user
        if ($user = Auth::guard('web')->user()) {
            $user->tokens()->delete();
        }

        // Hapus session web guard
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $token = \Illuminate\Support\Str::random(64);
        
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => \Illuminate\Support\Facades\Hash::make($token), 'created_at' => now()]
        );

        // In a real app, send an email. For testing, we return the token directly.
        return response()->json([
            'message' => 'Reset password token generated successfully',
            'token' => $token // ONLY for testing purposes
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !\Illuminate\Support\Facades\Hash::check($request->token, $resetRecord->token)) {
            return response()->json(['message' => 'Invalid or expired password reset token'], 400);
        }

        $user = \App\Models\User::where('email', $request->email)->first();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Password reset successfully'
        ]);
    }
}
