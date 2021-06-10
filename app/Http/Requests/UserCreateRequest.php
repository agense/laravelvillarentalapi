<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::authorize('manage-app');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:120|regex:/(^[A-Za-z0-9 ]+$)+/',
            'email' => [
                'required',
                'max:191',
                'email',
                Rule::unique('users', 'email'),
                Rule::unique('account_applications', 'company_email'),
            ],
            'password' => 'required|string|min:6|max:20|confirmed',
        ];
    }
}
