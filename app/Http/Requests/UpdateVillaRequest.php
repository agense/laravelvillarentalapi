<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVillaRequest extends FormRequest
{
    private $villa;

    public function __construct(){
        $this->villa = request()->villa;
    }
    /**
     * Determine if the user is authorized to make this request.
     *
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
            'name' => ['bail','required','string', 'min:2', 'max:100', Rule::unique('villas','name')->ignore($this->villa->id)],
            'area' => 'required|numeric',
            'capacity' => 'required|numeric',
            'bedrooms' => 'required|numeric',
            'bathrooms' => 'required|numeric',
            'description' => 'required|max:1000',
            'city_id' => 'bail|required|numeric|exists:cities,id',
            'address' => 'required|string|max:191',
        ];
    }
}
