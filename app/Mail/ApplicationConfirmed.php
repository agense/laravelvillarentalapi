<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Account;

class ApplicationConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    private $account;

    /**
     * Create a new message instance.
     * @return void
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * Build the message.
     *  This email is sent to the applicant to inform that the application has been confirmed
     * @return $this
     */
    public function build()
    {
        return $this->subject('Account Application Confirmed')
            ->markdown('emails.applications.confirmed')
            ->with([
                'account' => $this->account,
                'reset_password_url' => config('default_settings.app_frontend_url') ? config('default_settings.app_frontend_url').'regenerate-password' : null
            ]);
    }
}

