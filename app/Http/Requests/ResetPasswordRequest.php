<?php

namespace App\Http\Requests;

use App\Rules\ValidPasswordResetToken;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'token' => ['required', new ValidPasswordResetToken],
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:6|max:20',
        ];
    }
}
