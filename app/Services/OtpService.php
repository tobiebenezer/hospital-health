<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class OtpService
{
    /**
     * Generate a new OTP for the given identifier and store it in the cache.
     *
     * @param string $identifier
     * @return string
     */
    public function generateOtp(string $identifier): string
    {
        $otp = random_int(100000, 999999);
        Cache::put($this->getCacheKey($identifier), $otp, now()->addMinutes(5));

        return (string) $otp;
    }

    /**
     * Validate the given OTP against the stored OTP for the identifier.
     *
     * @param string $identifier
     * @param string $otp
     * @return bool
     */
    public function validateOtp(string $identifier, string $otp): bool
    {
        return Cache::get($this->getCacheKey($identifier)) === $otp;
    }

    /**
     * Remove the OTP from the cache for the given identifier.
     *
     * @param string $identifier
     */
    public function expireOtp(string $identifier): void
    {
        Cache::forget($this->getCacheKey($identifier));
    }

    /**
     * Get the cache key for the given identifier.
     *
     * @param string $identifier
     * @return string
     */
    protected function getCacheKey(string $identifier): string
    {
        return 'otp:' . $identifier;
    }
}
