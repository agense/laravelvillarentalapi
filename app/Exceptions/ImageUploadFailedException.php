<?php

namespace App\Exceptions;

use Exception;

class ImageUploadFailedException extends Exception
{
    protected $fileName;
    protected $fileIndex;
    protected $field;
    protected $errors;

    public function __construct(String $message, Int $fileIndex = null, String $field = 'images')
    {
        parent::__construct($message);
        $this->fileIndex = $fileIndex;
        $this->field = $field;
    }
    /**
     * Handle image upload failure
     * @return Response
     */
    public function render($request)
    {
        if($this->fileIndex !== null){
            $this->field .= '.'.$this->fileIndex;
        }
        
        $response = [
            'message' => $this->getMessage(), 
            'exception' => get_class($this),
            'status_code' => 500,
            'error_field' => $this->field
        ];
        
        return response()->json($response, 500);     
    }
}
