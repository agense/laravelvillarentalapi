<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidFacility;
use App\Rules\ValidCategory;
use App\Rules\ValidFileExtension;
use App\Rules\ValidFileSize;
use App\Models\Facility;
use App\Models\Category;

use Illuminate\Validation\Rule;

class CreateVillaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $existing = $this->villa ? $this->villa->id : "";

        $facilities = request()->has('facilities') ? Facility::get('id')->pluck('id')->toArray() : [];
        $categories = request()->has('categories') ? Category::get('id')->pluck('id')->toArray() : [];

        return [
            'name' => ['bail','required','string', 'min:2', 'max:100', Rule::unique('villas','name')->ignore($existing)],
            'area' => 'required|numeric',
            'capacity' => 'required|numeric',
            'bedrooms' => 'required|numeric',
            'bathrooms' => 'required|numeric',
            'description' => 'required|max:1000',
            'city_id' => 'bail|required|numeric|exists:cities,id',
            'address' => 'required|string|max:191',
            'facilities' => ['sometimes', 'array'],
            'facilities.*' => ['required_with:facilities','bail','numeric', new ValidFacility($facilities) ],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['required_with:categories', 'bail', 'numeric', new ValidCategory($categories)],
            'images' => 'sometimes|array',
            'images.*' => [ 'required_with:images','bail', new ValidFileExtension, new ValidFileSize ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * @return array
     */
    public function messages()
    {
        return [
            'facilities.*.numeric' => 'Facility id must be a number',
            'categories.*.numeric' => 'Category id must be a number',
        ];
    }
}
