<?php

namespace App\Observers;

use App\Models\AccountApplication;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountApplicationReceived;
use App\Mail\NewApplicationReceived;

class AccountApplicationObserver
{
    /**
     * Handle the account application "created" event.
     * @param  \App\Models\AccountApplication  $application
     * @return void
     */
    public function created(AccountApplication $application)
    {
        Mail::to($application->company_email)->send(new AccountApplicationReceived($application));
        
        //Send mail to default admin if exists
        $defaultAdminEmail = config('default_settings.main_admin_email');
        if($defaultAdminEmail){
            Mail::to($defaultAdminEmail)->send(new NewApplicationReceived($application));
        }

    }
}
