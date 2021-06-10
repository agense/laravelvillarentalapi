<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\RejectedApplication;

class ApplicationRejected extends Mailable
{
    use Queueable, SerializesModels;

    private  $application;
    /**
     * Create a new message instance.
     * @return void
     */
    public function __construct(RejectedApplication $application)
    {
        $this->application = $application;
    }

    /**
     * Build the message.
     *  This email is sent to the applicant to inform that the application has been rejected
     * @return $this
     */
    public function build()
    {
        return $this->subject('Account Application Rejected')
            ->markdown('emails.applications.rejected')
            ->with(['application' => $this->application]);
    }
}
