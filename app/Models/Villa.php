<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\ImageStorageService;
use App\Models\VillaImage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use App\Traits\Calendar;

class Villa extends Model
{
    use HasFactory, SoftDeletes, Calendar;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name', 
        'slug', 
        'area', 
        'capacity', 
        'bedrooms', 
        'bathrooms', 
        'description', 
        'city_id', 
        'address'
    ];
    
    /**
     * The hidden attributes
     * @var array
     */
    protected $hidden = ['pivot'];
    
    /**
     * The relationships that should always be loaded.
     * @var array
     */
    protected $with = ['city'];

    /**
     * Custom Attributes
     */
    private const AREA_MEASUREMENT_UNIT = 'm2';

    //SETTERS
    /**
     * Set the slug from name.
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst($value);
        $this->attributes['slug'] = Str::slug($value, '-');
    }
    
    
    // RELATIONS
    /**
     * Relationship with City Model
    */
    public function city(){
        return $this->belongsTo('App\Models\City');
    }

    /**
     * Relationship with Villa_Images Model
    */
    public function images(){
        return $this->hasMany('App\Models\VillaImage');
    }

    /**
     * Relationship with Facility Model - Many to Many
    */
    public function facilities(){
       return $this->belongsToMany('App\Models\Facility');
    }
    /**
     * Relationship with Category Model - Many to Many
    */
    public function categories(){
        return $this->belongsToMany('App\Models\Category');
    }

    /**
     * Relationship with Availability
     */
    public function availabilities()
    {
        return $this->hasMany('App\Models\Availability');
    }

    /**
     * Relationship with Price model
     */
    public function prices()
    {
        return $this->hasMany('App\Models\Price');
    }

    // GETTERS

    public function getFillable()
    {
        return array_diff($this->fillable, ['slug'] ); 
    }

    // MODEL SCOPES
    public function scopeWithFullData($query)
    {
        return $query->with('facilities')->with('categories')->with('images')->withCount('facilities');
    }

    // MODEL GETTERS
    public static function getAreaMeasurementUnit()
    {
        return self::AREA_MEASUREMENT_UNIT;
    }

    // MODEL METHODS
    /**
     * Creates new villa and attaches relational data
     */
    public function createNew()
    {
        $this->fill( request()->only($this->getFillable()));
        $this->save();
        
        if(request()->has('facilities')){
            $this->addFacilities(request()->facilities);
        }

        if(request()->has('categories')){
            $this->addCategories(request()->categories);
        }
        
        //upload images
        if(request()->has('images')){
            $this->uploadImages(request()->images);
        }
        return $this;
    }

    /**
     * Attach facilities to villa
     * @param Array $facilities
     * @return Void
     */
    public function addFacilities(Array $facilities)
    {
        $this->facilities()->syncWithoutDetaching($facilities);
    }

    /**
     * Detach facilities from villa
     * @param Array $facilities
     * @return Void
     */
    public function removeFacilities(Array $facilities){
        $this->facilities()->detach($facilities);
    }

    /**
     * Attach categories to villa
     * @param Array $cateories
     * @return Void
     */
    public function addCategories(Array $categories){
        $this->categories()->syncWithoutDetaching($categories);
    }
    
    /**
     * Detach categories from villa
     * @param Array $categories
     * @return Void
     */
    public function removeCategories(Array $categories){
        $this->categories()->detach($categories);
    }

    /**
     * Upload images to storage and save in DB
     * @param Array $images ($_FILES array or array of base64 encoded images)
     * @return Collection
     */
    public function uploadImages(Array $images){

        $uploader = new ImageStorageService();

        try{
            //Upload to storage
            $uploader->upload_images($images, $this->slug);
            $paths = $uploader->get_uploaded_files();

            // Prepend each uploaded image name with key 'image' to prepare an arry for db insertion
            $images = $paths->map(function ($item, $key) {
                return ['image' => $item];
            })->toArray();

            //Save images in db
            $this->images()->createMany($images);

            //Return collection of uploaded images
            return $this->images->filter(function($item, $key) use ($paths){
                return $paths->contains($item->image);
            });

        }catch(\Exception $e){
            //remove all images if any error occurs
            $uploader->delete_images($uploader->get_uploaded_files()->toArray());
            throw $e;
        }
    }

    /**
     * Delete images from storage and DB
     * @param Array $images (id's)
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function deleteImages(Array $images){

        $uploader = new ImageStorageService();
        $images = collect($images);

        //Get deletebale image names from ids
        $toDelete = $this->images->filter(function($item, $key) use ($images){
            return $images->contains($item->id);
        })->pluck('image')->toArray();
        
        //Delete images from storage and get deleted and failed to delete files (if exist)
        $uploader->delete_images($toDelete);
        $deleted = $uploader->get_deleted_files();

        // If not all images were deleted from storage, filter out only delete ones
        $deletedFiles = $this->images->filter(function ($value, $key) use($deleted){
            return  $deleted->contains($value->image);
        })->flatten();
        $ids = $deletedFiles->pluck('id')->toArray();

        //Delete images from db
        VillaImage::where('villa_id', $this->id)->whereIn('id', $ids)->delete();

        return $deletedFiles;
    }

    /**
     * Get villa availabilities per period
     * @param String $start - period start date
     * @param String $end - period end date
     * @return Array
     */
    public function getPeriodAvailability(String $start, String $end)
    {
        return self::getPeriodData('availability', $start, $end);
    }

    /**
     * Update villa availabilities per period
     * @param String $start - period start date
     * @param String $end - period end date
     * @param Int $availability
     * @return Array (updated data)
     */
    public function updateAvailabilities(String $start, String $end, Int $availability)
    {   
        return self::updatePeriod('availability', $start, $end, $availability);
    }

    /**
     * Get villa prices per period
     * @param String $start - period start date
     * @param String $end - period end date
     * @return Array 
     */
    public function getPeriodPrices(String $start, String $end)
    {
        return self::getPeriodData('price', $start, $end);
    }

    /**
     * Update villa prices per period
     * @param String $start - period start date
     * @param String $end - period end date
     * @param Int $price
     * @return Array (updated data)
     */
    public function updatePrices(String $start, String $end, Int $price)
    {
        return self::updatePeriod('price', $start, $end, $price);
    }

}

