<?php

namespace App\Rules;

use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Validation\Rule;

class ValidPasswordResetToken implements Rule
{
    private $broker;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->broker = Password::broker();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $credentials =  request()->only('email', 'token');
        if (is_null($user = $this->broker->getUser($credentials)) || !$this->broker->tokenExists($user, $credentials['token'])) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid password reset token';
    }
}
