<?php

namespace App\Http\Controllers\Admin;

use App\Models\Villa;
use Illuminate\Http\Request;
use App\Services\DateService;
use App\Http\Controllers\Controller;
use App\Http\Resources\VillaPriceResource;
use App\Http\Requests\ValidSearchDatesRequest;

class PricesController extends Controller
{
    
    /**
     * Get prices for specific period per speific villa
     * @param  \App\Http\Requests\ValidSearchDatesRequest $request 
     * @param  \App\Models\Villa $villa
     * @return \App\Http\Resources\VillaPriceResource
     */
    public function getPrices(ValidSearchDatesRequest $request, Villa $villa){

        $this->authorize('view', $villa);

        $start = $request->query('start_date') ?? DateService::defaultPeriodStartDate();
        $end = $request->query('end_date') ?? DateService::defaultPeriodEndDate();
        
        $data = $villa->getPeriodPrices($start, $end);

        return new VillaPriceResource($villa, $data);
    }

    /**
     * Update Prices for specific period per specific villa
     * @param  \Illuminate\Http\Request $request - must have start date, end date and price fields
     * @param  \App\Models\Villa $villa
     * @return \App\Http\Resources\VillaPriceResource
     */
    public function updatePrices(Request $request, Villa $villa){

        $this->authorize('update', $villa);

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
