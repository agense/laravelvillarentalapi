<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Services\ImageStorageService;

class ValidFileExtension implements Rule
{
    private $allowedExtensions;
    private $extension;
    private $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->allowedExtensions = config('filesystems.images.allowed_extensions');
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->extension = ImageStorageService::get_file_extension($value);
        if($this->extension == null){
            $this->message = "Unrecognizable file type.";
            return false;
        }

        if(!in_array($this->extension, $this->allowedExtensions)){
            $this->message =  'Files of type '. $this->extension . ' are not allowed. Allowed file extensions are: '. implode(',', $this->allowedExtensions);
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
