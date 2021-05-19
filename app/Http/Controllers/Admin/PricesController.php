<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Villa;
use App\Services\DateService;
use App\Http\Resources\VillaPriceResource;

class PricesController extends Controller
{
    
    /**
     * Get prices for specific period per speific villa
     * @param Request $request - can have start date and end date, otherwise period is from current date till the end of the following month
     * @param App\Models\Villa $villa
     * @return \Illuminate\Http\Response
     */
    public function getPrices(Request $request, Villa $villa){
        $request->validate([
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
        ]);

        $start = $request->query('start_date') ?? DateService::defaultPeriodStartDate();
        $end = $request->query('end_date') ?? DateService::defaultPeriodEndDate();
        
        $data = $villa->getPeriodPrices($start, $end);

        return new VillaPriceResource($villa, $data);
    }

    /**
     * Update Prices for specific period per specific villa
     * @param Request $request - must have start date, end date and price fields
     * @param App\Models\Villa $villa
     * @return \Illuminate\Http\Response
     */
    public function updatePrices(Request $request, Villa $villa){
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'price' => 'required|int|min:1'
        ]);
        
        $data = array_values($request->only('start_date','end_date','price'));
        $updated = $villa->updatePrices(...$data);

        return new VillaPriceResource($villa, $updated, "Prices updated for all requested dates.");
    }
}
