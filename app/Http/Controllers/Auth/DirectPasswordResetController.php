<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DirectPasswordResetController extends Controller
{
    /**
     * Handle direct password reset without email verification
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password baru harus diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.min' => 'Password minimal :min karakter',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak terdaftar dalam sistem.',
            ])->withInput($request->only('email'));
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Redirect to login with success message
        return redirect()->route('login')->with('status', 'Password berhasil direset! Silakan login dengan password baru Anda.');
    }
}
