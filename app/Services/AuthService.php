<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\VerifyEmailWithOtp;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Throwable;

class AuthService
{
    public OtpService $otpService;

    public function __construct(OtpService $otpService) {
        $this->otpService = $otpService;
    }

    /**
     * Register a new user, create their shopping cart, and send a verification email.
     *
     * @param array $data The user registration data.
     * @return User The newly created user instance.
     * @throws Throwable
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Create the user
            $user = User::create([
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email'      => $data['email'],
                'password'   => $data['password'],
            ]);

            $otp = $this->otpService->generateOtp($user->email);
            $user->sendEmailVerificationNotificationWithOtp($otp);

            // Return the newly created user.
            return $user;
        });
    }

    public function forgotPassword(string $email): void
    {
        $user = User::where('email', $email)->firstOrFail();

        $otp = $this->otpService->generateOtp($email);
        
        $user->sendEmailVerificationNotificationWithOtp($otp);
    }

    /**
     * Reset the user's password if the provided OTP is valid.
     *
     * @param string $email The user's email address.
     * @param string $password The new password.
     * @param string $token The OTP token.
     * @throws AuthenticationException If the OTP is invalid or expired.
     */
    public function resetPassword(string $email, string $password, string $token): void
    {
        if (!$this->otpService->validateOtp($email, $token)) {
            throw new AuthenticationException('Invalid or expired OTP.');
        }

        $user = User::where('email', $email)->firstOrFail();
        $user->forceFill(['password' => $password])->save();
    }

    /**
     * Verify the user's email address if the provided OTP is valid.
     *
     * @param string $email The user's email address.
     * @param string $token The OTP token.
     * @throws AuthenticationException If the OTP is invalid or expired.
     */
    public function verifyEmail(string $email, string $token): void
    {
        if (!$this->otpService->validateOtp($email, $token)) {
            throw new AuthenticationException('Invalid or expired OTP.');
        }

        $user = User::where('email', $email)->firstOrFail();
        $user->markEmailAsVerified();
    }

    public function resendVerification(string $email): void
    {
        $user = User::where('email', $email)->firstOrFail();

        $otp = $this->otpService->generateOtp($email);
        $user->notify(new VerifyEmailWithOtp($otp));
    }

}

