<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\ValidFacility;
use App\Models\Facility;

class FacilitiesAttachmentRequest extends FormRequest
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
        $facilities = Facility::get('id')->pluck('id')->toArray();
        $applied = request()->villa->facilities->pluck('id')->toArray();

        $rules = [
            'facilities' => ['required', 'array'],
            'facilities.*' => [ 'bail','numeric', new ValidFacility($facilities) ]
        ];

        // If request is put (to attach facilities), ensure they are not yet attached
        //If request is delete (detach facilities), ensure they are attached
        if(request()->isMethod('PUT')){
            array_push($rules['facilities.*'], Rule::notIn($applied));

        }elseif(request()->isMethod('DELETE')){
            array_push($rules['facilities.*'], Rule::in($applied));
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
            'facilities.*.numeric' => 'Facility id must be a numeric',
            'facilities.*.not_in' => 'Facility already applied',
            'facilities.*.in' => 'Facility is not applied',
        ];
    }

}
