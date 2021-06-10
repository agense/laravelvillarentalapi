<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidAccountType;

class ApplicationRequest extends FormRequest
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
        return [
            'account_type' => ['required','string', new ValidAccountType],
            'company_name' => 'required|string|max:191|regex:/(^[A-Za-z0-9.\- ]+$)+/', 
            'company_registration_number' => 'required|string|max:191|regex:/(^[A-Za-z0-9 ]+$)+/', 
            'company_owner_name' => 'required|string|max:120|regex:/(^[A-Za-z ]+$)+/',
            'company_email' => 'required|email|max:191|unique:users,email|unique:account_applications,company_email',
            'company_phone' => 'required|string|max:191|digits_between:5,10',
            'company_website' => 'nullable|sometimes|string|max:191|url',
            'company_address' => 'required|string|max:191|regex:/(^[A-Za-z0-9.\- ]+$)+/',
            'company_city' => 'required|string|max:100|regex:/(^[A-Za-z ]+$)+/',
            'company_country' => 'required|string|max:100|regex:/(^[A-Za-z ]+$)+/',
        ];
    }
}
