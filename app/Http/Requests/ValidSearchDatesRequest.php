<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidSearchDatesRequest extends FormRequest
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
     * Can have start date and end date, otherwise default dates are used
     * @return array
     */
    public function rules()
    {
        return [
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
        ];
    }
}
