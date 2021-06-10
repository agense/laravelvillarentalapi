<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\AccountApplication;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewApplicationReceived extends Mailable
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
     * This email is sent to main administrator to inform about a new application in need of reviewing
     * @return $this
     */
    public function build()
    {  
        if(!config('default_settings.main_admin_email')){
            return false;
        }
        return $this->from($this->application->company_email)
            ->subject('New Account Application')
            ->markdown('emails.applications.new')
            ->with([
                'application' => $this->application,
                'url' => config('default_settings.app_frontend_url') ?? null,
            ]);
    }
}
