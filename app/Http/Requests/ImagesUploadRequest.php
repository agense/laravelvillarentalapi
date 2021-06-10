<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidFileExtension;
use App\Rules\ValidFileSize;

class ImagesUploadRequest extends FormRequest
{
    private $villa;

    public function __construct(){
        $this->villa = request()->villa;
    }
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
       return auth()->user()->can('update', $this->villa);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'images' => 'required|array',
            'images.*' => [ new ValidFileExtension, new ValidFileSize ]
        ];
    }
}
