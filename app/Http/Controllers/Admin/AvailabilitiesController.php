<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Villa;
use App\Services\DateService;
use App\Http\Resources\VillaAvailabilityResource;

class AvailabilitiesController extends Controller
{
    /**
     * Get Availabilities for specific period per specific villa
     * @param Request $request - can have start date and end date, otherwise period is from current date till the end of the following month
     * @param App\Models\Villa $villa
     * @return \Illuminate\Http\Response
     */
    public function getAvailability(Request $request, Villa $villa){
        $request->validate([
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
        ]);

        $start = $request->query('start_date') ?? DateService::defaultPeriodStartDate();
        $end = $request->query('end_date') ?? DateService::defaultPeriodEndDate();
        
        $data = $villa->getPeriodAvailability($start, $end);

        return new VillaAvailabilityResource($villa, $data);
    }

    /**
     * Update Availabilities for specific period per specific villa
     * @param Request $request - must have start date, end date and availability fields
     * @param App\Models\Villa $villa
     * @return \Illuminate\Http\Response
     */
    public function updateAvailability(Request $request, Villa $villa){
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
