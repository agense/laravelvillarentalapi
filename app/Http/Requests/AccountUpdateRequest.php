<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountUpdateRequest extends FormRequest
{
    private $account;

    public function __construct(){
        $this->account = request()->account;
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
       return auth()->user()->can('update', $this->account);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_owner_name' => 'required|string|max:120|regex:/(^[A-Za-z ]+$)+/',
            'company_email' => [
                'required',
                'email',
                'max:191',
                Rule::unique('users', 'email')->ignore($this->account->user->id),
                Rule::unique('account_applications', 'company_email'),
                Rule::unique('accounts', 'company_email')->ignore($this->account->id ),
            ],
            'company_phone' => 'required|string|max:191|digits_between:5,10',
            'company_website' => 'nullable|sometimes|string|max:191|url',
            'company_address' => 'required|string|max:191|regex:/(^[A-Za-z0-9.\- ]+$)+/',
            'company_city' => 'required|string|max:100|regex:/(^[A-Za-z ]+$)+/',
            'company_country' => 'required|string|max:100|regex:/(^[A-Za-z ]+$)+/',
        ];
    }
}

