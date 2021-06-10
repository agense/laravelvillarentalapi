<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\AccountApplication;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccountApplicationReceived extends Mailable
{
    use Queueable, SerializesModels;

    private $application;

    /**
     * Create a new message instance.
     * @return void
     */
    public function __construct(AccountApplication $application)
    {
        $this->application = $application;
    }


    /**
     * Build the message.
     * This message is sent to the applicant confirming that his application has been received.
     * @return $this
     */
    public function build()
    {
        return $this->subject('Account Application Received')
            ->markdown('emails.applications.received')
            ->with([
                'application' => $this->application,
            ]);
    }
}
