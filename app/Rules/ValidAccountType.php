<?php

namespace App\Rules;

use App\Models\Account;
use App\Models\AccountApplication;
use Illuminate\Contracts\Validation\Rule;

class ValidAccountType implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Account::isValidType($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid account type.';
    }
}
