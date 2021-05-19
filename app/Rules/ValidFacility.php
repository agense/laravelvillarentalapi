<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidFacility implements Rule
{
    private $existing;
    private $message;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Array $existing)
    {
        $this->existing = $existing;
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
        if(! in_array($value, $this->existing)){
            $this->message = "Facility with id $value does not exist.";
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
        return $this->message;
    }
}
