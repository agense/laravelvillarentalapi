<?php

namespace App\Exceptions;

use Exception;

class ImageDeleteFailedException extends Exception
{
    protected $file;

    public function __construct(String $message, String $file = '', Array $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
        $this->file = $file;
        
    }
     /**
     * Handle image deletion failure
     * @return Response
     */
    public function render($request)
    {
        $response = [
            'message' => $this->getMessage(), 
            'exception' => get_class($this),
            'status_code' => 500 
        ];

        if($this->file){
            $response['file_name'] = $this->file;
        }

        if($this->errors){
            $response['errors'] = $this->errors;
        }

        return response()->json($response, 500);
    }
}
