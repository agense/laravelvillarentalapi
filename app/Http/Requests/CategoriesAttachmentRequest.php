<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\ValidCategory;
use App\Models\Category;

class CategoriesAttachmentRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $categories = Category::get('id')->pluck('id')->toArray();
        $applied = $this->villa->categories->pluck('id')->toArray();

        $rules =  [
            'categories' => ['required', 'array'],
            'categories.*' => ['bail', 'numeric', new ValidCategory($categories),]
        ];

        // If request is put (to attach categories), ensure they are not yet attached
        //If request is delete (detach categories), ensure they are attached
        if(request()->isMethod('PUT')){
            array_push($rules['categories.*'], Rule::notIn($applied));

        }elseif(request()->isMethod('DELETE')){
            array_push($rules['categories.*'], Rule::in($applied));
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     * @return array
     */
    public function messages()
    {
        return [
            'categories.*.numeric' => 'Category id must be a number',
            'categories.*.not_in' => 'Category already applied',
            'categories.*.in' => 'Category is not applied',
        ];
    }
}
