<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegionRequest extends FormRequest
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
        $existing = $this->region ? $this->region->id : "";
        return [
            'name' => [
                'required',
                'string',
                'max:191',
                'regex:/(^[A-Za-z0-9 ]+$)+/',
                Rule::unique('regions','name')->ignore($existing)
            ],
        ];
    }
}
