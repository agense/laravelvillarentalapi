<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\FacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Http\Resources\FacilityCollection;
use App\Models\Facility;

class FacilitiesController extends Controller
{
    /**
     * Display a listing of facilities
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $facilities = Facility::orderBy('name')->get();
         return new FacilityCollection($facilities);
    }

    /**
     * Store a newly created facility in storage
     * @param  \App\Http\Requests\FacilityRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(FacilityRequest $request)
    {
        $facility = Facility::create($request->only('name', 'type'));
        return new FacilityResource($facility, "Facility created");
    }

    /**
     * Update the specified facility in storage
     * @param  \App\Http\Requests\FacilityRequest $request
     * @param  \App\Models\Facility $facility
     * @return \Illuminate\Http\Response
     */
    public function update(FacilityRequest $request, Facility $facility)
    {
        $facility->name = $request->name;
        $facility->type = $request->type;
        $facility->save();
        return new FacilityResource($facility, "Facility updated");
    }

    /**
     * Remove the specified facility from storage
     * @param  \App\Models\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function destroy(Facility $facility)
    {
        $facility->villas()->detach();
        $facility->delete();
        return response()->json(['message' => 'Facility deleted.']);
    }
}
