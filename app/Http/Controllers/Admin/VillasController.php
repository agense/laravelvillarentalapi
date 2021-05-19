<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

use App\Http\Requests\CreateVillaRequest;
use App\Http\Requests\FacilitiesAttachmentRequest;
use App\Http\Requests\CategoriesAttachmentRequest;
use App\Http\Requests\ImagesUploadRequest;
use App\Http\Requests\ImagesDeleteRequest;

use App\Http\Resources\VillaResource;
use App\Http\Resources\VillaCollection;
use App\Http\Resources\VillaRelationUpdateResource as VillaRelationResource;

use App\Models\Villa;

class VillasController extends Controller
{
    /**
     * Display a listing of all villas
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $villas = Villa::all();
        return new VillaCollection($villas);
    }
  
    /**
     * Store a newly created villa in storage
     * @param  \App\Http\Requests\CreateVillaRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateVillaRequest $request)
    {
        $villa = new Villa();
        $villa = $villa->createNew();
        
        return new VillaResource($villa, "New villa created");
    }

    /**
     * Display the specified villa
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $villa = Villa::WithFullData()->findOrFail($id);
        return new VillaResource($villa);
    }

    /**
     * Update the specified vila in storage
     * @param  \App\Http\Requests\CreateVillaRequest  $request
     * @param  \App\Villa  $villa
     * @return \Illuminate\Http\Response
     */
    public function update(CreateVillaRequest $request, Villa $villa)
    {
        $villa->fill( $request->only($villa->getFillable()));
        $villa->save();

        return new VillaResource($villa, "Villa updated");
    }

    /**
     * Attach facilities to villa
     * @param \App\Http\Requests\FacilitiesAttachmentRequest $request
     * @param App\Models\Villa $villa
     * @return \Illuminate\Http\Response
     */
    public function addFacilities(FacilitiesAttachmentRequest $request, Villa $villa)
    {
        $villa->addFacilities($request->facilities);
        //Filter out pre-existing facilities
        $villa->refresh();
        $new = findMatches($villa->facilities, $request->facilities);
        return new VillaRelationResource($villa, $new, 'facilities', "New Facilities Added");
    }

    /**
     * Detach facilities from villa
     * @param \App\Http\Requests\FacilitiesAttachmentRequest $request
     * @param App\Models\Villa $villa
     * @return \Illuminate\Http\Response
     */
    public function removeFacilities(FacilitiesAttachmentRequest $request, Villa $villa)
    {
        $villa->removeFacilities($request->facilities);
        //Filter out remaining facilities
        $removed = findMatches($villa->facilities, $request->facilities);
        return new VillaRelationResource($villa, $removed, 'facilities', "Requested facilities removed");
    }

    /**
     * Attach categories to villa 
     * @param \App\Http\Requests\CategoriesAttachmentRequest $request
     * @param App\Models\Villa
     * @return \Illuminate\Http\Response
     */
    public function addCategories(CategoriesAttachmentRequest $request, Villa $villa)
    {
        $villa->addCategories($request->categories);
        //Filter out pre-existing categories
        $villa->refresh();
        $new = findMatches($villa->categories, $request->categories);
        return new VillaRelationResource($villa, $new, 'categories', "New Categories Added");
    }
    
    /**
     * Detach categories from villa 
     * @param \App\Http\Requests\CategoriesAttachmentRequest $request
     * @param App\Models\Villa
     * @return \Illuminate\Http\Response
     */
    public function removeCategories(CategoriesAttachmentRequest $request, Villa $villa)
    {  
        $villa->removeCategories($request->categories);
        //Filter out remaining categories
        $removed = findMatches($villa->categories, $request->categories);
        return new VillaRelationResource($villa, $removed, 'categories', "Requested Categories Removed");
    }

    /**
     * Upload images and attach to villa
     * @param \App\Http\Requests\ImagesUploadRequest $request
     * @param App\Models\Villa $villa
     * @return \Illuminate\Http\Response
     */
    public function uploadImages(ImagesUploadRequest $request, Villa $villa)
    {
        $uploaded = $villa->uploadImages($request->images);
        return new VillaRelationResource($villa, $uploaded, 'images', "Image upload successful");
    }
        
    /**
     * Delete Images
     * @param \App\Http\Requests\ImagesDeleteRequest $request
     * @param App\Models\Villa $villa
     * @return \Illuminate\Http\Response
     */
    public function deleteImages(ImagesDeleteRequest $request, Villa $villa)
    {
          $deleted = $villa->deleteImages($request->images);
          //Check for failed to delete images
          $failed = collect(collect($request->images)->diff($deleted->pluck('id'))->all())->values();

          // Format Response
          if($deleted->isEmpty()){
            $response = ["message" => "Delete failed for all images", "status_code"  => 500];
          }else{
            $response = [
                "message" => "All images were deleted successfuly.",
                "deleted_image_count" => $deleted->count(),
                "deleted_images" => $deleted->toArray(),
            ];
            if($failed->isNotEmpty()){
                $response['message'] = "Notice: delete failed for some of the images.";
                $response['failed_to_delete'] = $failed->toArray();
            }
          }
          return response()->json($response, $response['status_code'] ?? 200);
    }


    // DELETES
    /**
     * Get Deleted Villa List or a single deleted villa with full data based on param passed
     * @param Int $id optional
     * @return \Illuminate\Http\Response
     */
    public function inactive(Int $id = null)
    {
        if($id !== null){
            $villa = Villa::onlyTrashed()->WithFullData()->findOrFail($id);
            return new VillaResource($villa);
        }
        return new VillaCollection(Villa::onlyTrashed()->get());
    }

    /**
     * Deactivate specified villa
     * @param  \App\Villa  $villa
     * @return \Illuminate\Http\Response
     */
    public function deactivate(Villa $villa)
    {
        $villa->delete();
        return new VillaResource($villa, "Villa has been deactivated");
    }

    /**
     * Restore A Deleted Villa
     * @param Int $id optional
     * @return \Illuminate\Http\Response
     */
    public function activate(Int $id)
    {
        $villa = Villa::onlyTrashed()->WithFullData()->findOrFail($id);
        $villa->restore();
        return new VillaResource($villa, "Villa has been activated");
    }

    /**
     * Remove the villa and associated data from db
     * @param Int $id optional
     * @return \Illuminate\Http\Response
     */
    public function destroy(Int $id)
    {
        $villa = Villa::withTrashed()->with('images')->findOrFail($id);
        $villa->forceDelete();
        return response()->json(['message' => 'Villa and all associated data has been deleted permanently.']);
    }


}

