<?php

namespace App\Http\Requests;

use App\Rules\ValidCurrentPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::authorize('manage-own-data', request()->user);
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'current_password' => ['required', new ValidCurrentPassword(request()->user)],
            'password' => 'required|string|min:6|max:20|confirmed',
        ];
    }
}
