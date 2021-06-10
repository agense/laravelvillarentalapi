<?php

namespace App\Observers;

use App\Models\User;
use App\Mail\ApplicationConfirmed;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    /**
     * Handle the user "created" event.
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        if($user->isClient()){
            $user->loadMissing('account');
            Mail::to($user->email)->send(new ApplicationConfirmed($user->account));
        } 
    }
}
