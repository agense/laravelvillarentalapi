<?php

namespace App\Services;

use Image; //intervention image class
use Intervention\Image\Image as ImageInstance;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\ImageDeleteFailedException;
use App\Exceptions\ImageUploadFailedException;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageStorageService{

    private $uploadPath;
    private $uploadDir;
    private $imageMaxWidth;
    private $imageMaxHeight;

    private $fileName = '';
    private $uploadedFiles = [];
    private $deletedFiles = [];

    public function __construct(String $uploadDir = null) {

        $defaultDir = strtolower(Str::replaceFirst('Controller', '', class_basename(\Route::current()->controller)));
    
        $storagePath = config('filesystems.images.upload_location');
        $this->uploadDir = $uploadDir ? $uploadDir : $defaultDir;

        $this->uploadPath = $storagePath.$this->uploadDir.'/';

        $this->imageMaxWidth =  config('filesystems.images.max_width');
        $this->imageMaxHeight = config('filesystems.images.max_height');
    }

    /**
     * Uploads images from $_FILES[] array and base64 encoded images
     * @param $img String/Uploadable File in $_FILES[]
     * @param String $prefix
     * @return Void
     */
    public function upload_image($img, String $prefix = '')
    {
        //Get Extension
        $ext = self::get_file_extension($img);

        if($ext == null){
            throw new ImageUploadFailedException('Unrecognized file type.');
        }

        //Set file name and upload path
        $this->fileName = $prefix ? $prefix."_".uniqid().'.'.$ext : uniqid().'.'.$ext;
        $filepath = $this->uploadPath.$this->fileName;

        //Create and resize image using InterventionImage
        $image = Image::make($img); 
        $image = $this->resize($image, $this->imageMaxWidth, $this->imageMaxHeight);
        $encodedImage = $image->encode($ext);
       
        //Save Image in Storage
        if(! Storage::put($filepath, $encodedImage)) {
            throw new ImageUploadFailedException('File saving in storage failed.');
        };

        //Add to uploaded files array
        array_push($this->uploadedFiles,  $this->fileName);
    }

    /**
     * Upload multiple images
     * @param Array $images;
     * @param String $prefix
     * @return Void
     */
    public function upload_images(Array $images, String $prefix = '')
    {
        $this->uploadedFiles = [];
        try{
            foreach($images as $key => $img){
                self::upload_image($img, $prefix);
            }
        }catch(\Exception $e){
            throw new ImageUploadFailedException($e->getMessage(), $key);
        }
    }

    // HELPERS
    /**
     * Resize image to specified dimentions
     * @param ImageInstance $image
     * @param Int $width
     * @param Int $height
     * @return ImageInstance $image
     */
    private function resize(ImageInstance $image, Int $width, Int $height)
    {
        if($image->width() > $width){
            $image->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }elseif($image->height() > $height){
            $image->resize(null, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        return $image;
    }

    // GETTERS
    /**
     * Get file extension
     * @param $img 
     * @return String
     */
    public static function get_file_extension($img)
    {
        //Get extension for base 64 images
        if(self::is_file($img)){
            return $img->getClientOriginalExtension();
        }
        elseif(self::is_base64($img)){  
            $mimetype  = mime_content_type ($img);
            return explode('/', $mimetype)[1];
        }else{
            return null;
        }
    }

    /**
     * Return uploaded file name
     * @return String
     */
    public function get_uploaded_filename(){
        return $this->fileName;
    }

    /**
     * Return all uploaded file names
     * @return Array
     */
    public function get_uploaded_files(){
        return collect($this->uploadedFiles);
    }

    /**
     * Return names of deleted images
     * @return Array
     */
    public function get_deleted_files(){
        return collect($this->deletedFiles);
    }

    // CHECKS
    /**
     * Check if image is an uploadable file in $_FILES[] array
     * @param $img
     * @return Bool
     */
    public static function is_file($img){
        return is_uploaded_file($img);
    }

    /**
     * Check if image is in base64 format
     * @param $img
     * @return Bool
     */
    public static function is_base64($img){
        return is_string($img) && preg_match('/data:image/', $img);
    }


    // SETTERS 

    /**
     * Set upload dimensions via setter
     * @param Int $imageWidth
     * @param Int $imageHeight
     * @return Void
     */
    public function set_upload_dimensions(Int $imageWidth, Int $imageHeight)
    {
      $this->imageMaxWidth = $imageWidth;
      $this->imageMaxHeight = $imageHeight;
    }

    // DELETES
    
     /**
     * Delete Image from storage
     * @param  String $img 
     * @return Void
     */
    public function delete_image(String $img){
        $this->deletedFiles = [];
        $filepath = $this->uploadPath.$img;

        if( Storage::exists($filepath)){
            if(Storage::delete($filepath)) {
                array_push($this->deletedFiles, $name);
            }else{
                throw new ImageDeleteFailedException('Delete from storage failed', $img);
            };  
        }else{
            throw new ImageDeleteFailedException('Image not found in storage', $img);
        }
    }

    /**
     * Delete multiple images from storage
     * @param  Array $images
     * @return Void
     */
    public function delete_images(Array $images){

        $this->deletedFiles = [];

        foreach($images as $name){
            $filepath = $this->uploadPath.$name;

            if( Storage::exists($filepath)){
                if(Storage::delete($filepath)) {
                    array_push($this->deletedFiles, $name);
                }
            }
        }
    }
}