<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Services\ImageStorageService;

class ValidFileSize implements Rule
{
    private $maxFileSize;

    /**
     * Create a new rule instance.
     * @return void
     */
    public function __construct()
    {
        $this->maxFileSize = config('filesystems.images.max_file_size');
    }

    private function get_file_size($img){
        if(ImageStorageService::is_file($img)){
            return filesize($img);

        }elseif(ImageStorageService::is_base64($img)){
            //Get extension
            $extension = explode('/', mime_content_type ($img))[1];
            //Remove add on string parts
            $imageStr = ltrim(rtrim($img, '='), "data:image/$extension;base64,");
            //Get base 64 image size in bytes
            $filesize = (int) (strlen($imageStr) * 0.75);
            return $filesize;
        }
    }

    /**
     * Determine if the validation rule passes.
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //Get size in bytes and convert to kilobytes
        $size = self::get_file_size($value);

        if($size < $this->maxFileSize){
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'File is too big. Maximum file size is ' . ($this->maxFileSize/1000 ) . ' KB';
    }
}
