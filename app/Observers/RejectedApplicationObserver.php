<?php

namespace App\Observers;

use App\Mail\ApplicationRejected;
use App\Models\RejectedApplication;
use Illuminate\Support\Facades\Mail;

class RejectedApplicationObserver
{
    /**
     * Handle the rejected application "created" event.
     * @param  \App\Models\RejectedApplication  $rejected
     * @return void
     */
    public function created(RejectedApplication $rejected)
    {
        Mail::to($rejected->company_email)->send(new ApplicationRejected($rejected));
    }
}
