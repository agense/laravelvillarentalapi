<?php

namespace App\Http\Controllers\Admin;

use App\Models\Facility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\FacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Http\Resources\FacilityCollection;

class FacilitiesController extends Controller
{
    public function __construct(){
        $this->middleware('can:manage-app')->except('index');
    }
    /**
     * Display a listing of facilities
     * @return \App\Http\Resources\FacilityCollection
     */
    public function index()
    {
        Gate::authorize('access-admin');
        $facilities = Facility::orderBy('name')->get();
        return new FacilityCollection($facilities);
    }

    /**
     * Store a newly created facility in storage
     * @param  \App\Http\Requests\FacilityRequest $request
     * @return \App\Http\Resources\FacilityResource
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
     * @return \App\Http\Resources\FacilityResource
     */
    public function update(FacilityRequest $request, Facility $facility)
    {
        $facility->fill($request->only('name','type'));
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
