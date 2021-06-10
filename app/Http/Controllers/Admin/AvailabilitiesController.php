<?php

namespace App\Http\Controllers\Admin;

use App\Models\Villa;
use Illuminate\Http\Request;
use App\Services\DateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidSearchDatesRequest;
use App\Http\Resources\VillaAvailabilityResource;

class AvailabilitiesController extends Controller
{
    /**
     * Get Availabilities for specific period per specific villa
     * @param App\Http\Requests\ValidSearchDatesRequest $request
     * @param App\Models\Villa $villa
     * @return App\Http\Resources\VillaAvailabilityResource
     */
    public function getAvailability(ValidSearchDatesRequest $request, Villa $villa){
        
        $this->authorize('view', $villa);

        $start = $request->query('start_date') ?? DateService::defaultPeriodStartDate();
        $end = $request->query('end_date') ?? DateService::defaultPeriodEndDate();
        
        $data = $villa->getPeriodAvailability($start, $end);

        return new VillaAvailabilityResource($villa, $data);
    }

    /**
     * Update Availabilities for specific period per specific villa
     * @param  \Illuminate\Http\Request $request - must have start date, end date and availability fields
     * @param  \App\Models\Villa $villa
     * @return \App\Http\Resources\VillaAvailabilityResource
     */
    public function updateAvailability(Request $request, Villa $villa){
        
        $this->authorize('update', $villa);

        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'availability' => 'required|int|min:0|max:1'
        ]);
        
        $data = array_values($request->only('start_date','end_date','availability'));
        $updated = $villa->updateAvailabilities(...$data);

        return new VillaAvailabilityResource($villa, $updated, "Availability updated for all requested dates.");
    }

}
