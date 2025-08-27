<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CustomerPasswordController extends Controller
{
    /**
     * Tampilkan form request reset password.
     */
    public function showForgotForm()
    {
        return view('web.customer.forgot-password');
    }

    /**
     * Proses kirim link reset ke email.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('successMessage', 'Link reset password telah dikirim ke email Anda.')
            : back()->withErrors(['email' => 'Gagal mengirim link reset. Pastikan email terdaftar.']);
    }

    /**
     * Tampilkan form reset (token).
     */
    public function showResetForm($token, Request $request)
    {
        $email = $request->query('email');

        return view('web.customer.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Proses reset password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($customer, $password) {
                $customer->password = Hash::make($password);
                $customer->setRememberToken(Str::random(60));
                $customer->save();

                // Auto-login setelah reset (opsional)
                Auth::guard('customer')->login($customer);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('homepage')->with('successMessage', 'Password berhasil diubah. Anda sudah login.')
            : back()->withErrors(['email' => 'Gagal mereset password — token kadaluwarsa atau tidak valid.']);
    }
}
