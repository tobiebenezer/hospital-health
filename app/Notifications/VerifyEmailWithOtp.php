<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailWithOtp extends Notification
{
    use Queueable;

    /**
     * The One-Time Password.
     *
     * @var string
     */
    public string $otp;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $otp)
    {

        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Verify Your Email Address')
                    ->line('Thank you for registering! Please use the following One-Time Password (OTP) to verify your email address.')
                    ->line('Your OTP is: ' . $this->otp)
                    ->line('This OTP will expire in 10 minutes.')
                    ->line('If you did not create an account, no further action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
