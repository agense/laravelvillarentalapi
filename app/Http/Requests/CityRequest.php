<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityRequest extends FormRequest
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
        $existing = $this->city ? $this->city->id : "";
        return [
            'name' => [
                'required',
                'string',
                'max:191',
                'regex:/(^[A-Za-z0-9 ]+$)+/',
                Rule::unique('cities','name')->ignore($existing)
            ],
            'region_id' => 'required|int|exists:regions,id',
        ];
    }
}
