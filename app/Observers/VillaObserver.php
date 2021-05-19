<?php

namespace App\Observers;

use App\Models\Villa;
use Illuminate\Support\Facades\Log;

class VillaObserver
{
    /**
     * Handle the villa "force deleted" event.
     *
     * @param  \App\Models\Villa  $villa
     * @return void
     */
    public function forceDeleted(Villa $villa)
    {
        $villa->facilities()->detach();
        $villa->categories()->detach();
        $villa->deleteImages($villa->images->pluck('id')->toArray());
    }

}
