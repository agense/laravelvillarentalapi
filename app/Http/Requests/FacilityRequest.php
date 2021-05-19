<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Facility;

class FacilityRequest extends FormRequest
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
        $existing = $this->facility ? $this->facility->id : "";
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('facilities','name')->ignore($existing)
            ],
            'type' => [
                'required',
                'string',
                'max:100',
                Rule::in(Facility::get_types())
            ],
        ];
    }
}
