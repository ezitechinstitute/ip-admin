<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Mail\ForgetPasswordMail;

class OTPVerifyController extends Controller
{
    public function sendOtp(Request $request)
    {
        // English comments: Validate that the email exists in the admin_accounts table
        $request->validate([
            'email' => 'required|email|exists:admin_accounts,email',
        ], [
            'email.exists' => 'We could not find an account with that email address.'
        ]);

        $email = $request->email;
        $otp = rand(100000, 999999);

        try {
            DB::table('password_otp_resets')->where('email', $email)->delete();

            DB::table('password_otp_resets')->insert([
                'email' => $email,
                'otp' => $otp,
                'created_at' => now()
            ]);

            Mail::to($email)->send(new ForgetPasswordMail($otp));

            return redirect()->route('auth.otp.verify', ['email' => $email])
                             ->with('success', 'A 6-digit verification code has been sent to your email.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send OTP. Error: ' . $e->getMessage());
        }
    }

    public function showVerifyForm(Request $request)
    {
        $email = $request->email;
        if (!$email) {
            return redirect()->route('auth.forgot-password.page');
        }
        return view('content.authentications.otp-verify-page', compact('email'));
    }

    public function verifyOtp(Request $request)
    {
        // Combine the 6 OTP input boxes into a single string
        $inputOtp = is_array($request->otp) ? implode('', $request->otp) : $request->otp;

        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $otpRecord = DB::table('password_otp_resets')
            ->where('email', $request->email)
            ->where('otp', $inputOtp)
            ->first();

        if (!$otpRecord) {
            return back()->with('error', 'The OTP you entered is incorrect.');
        }

        $isExpired = Carbon::parse($otpRecord->created_at)->addMinutes(10)->isPast();
        if ($isExpired) {
            DB::table('password_otp_resets')->where('email', $request->email)->delete();
            return back()->with('error', 'This OTP has expired. Please request a new one.');
        }

        // English comments: Store verified email in session for the next step
        session(['reset_password_email' => $request->email]);

        return redirect()->route('auth.password.reset.page')
                         ->with('success', 'OTP Verified! Please set your new password.');
    }

    /**
     * Step 4: Show New Password Form
     */
    public function showNewPasswordForm()
    {
        if (!session()->has('reset_password_email')) {
            return redirect()->route('auth.forgot-password.page')->with('error', 'Verification required.');
        }
        $email = session('reset_password_email');
        return view('content.authentications.auth-reset-password-cover', compact('email'));
    }

    /**
     * Step 5: Update the actual password in database
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $email = session('reset_password_email');

        if (!$email) {
            return redirect()->route('auth.forgot-password.page')->with('error', 'Session expired.');
        }

        try {
            DB::table('admin_accounts')->where('email', $email)->update([
                'password' => $request->password
            ]);

            DB::table('password_otp_resets')->where('email', $email)->delete();
            session()->forget('reset_password_email');

            return redirect()->route('login')->with('success', 'Password reset successful! Please login.');

        } catch (\Exception $e) {
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }




    public function resendOtp(Request $request)
{
    $email = $request->email;

    // English comments: Check if email is provided
    if (!$email) {
        return response()->json(['success' => false, 'message' => 'Email is required.'], 400);
    }

    $otp = rand(100000, 999999);

    try {
        // English comments: Update the existing OTP or create a new one
        DB::table('password_otp_resets')->updateOrInsert(
            ['email' => $email],
            ['otp' => $otp, 'created_at' => now()]
        );

        Mail::to($email)->send(new ForgetPasswordMail($otp));

        return response()->json(['success' => true, 'message' => 'A new OTP has been sent to your email.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Failed to send OTP.'], 500);
    }
}
}