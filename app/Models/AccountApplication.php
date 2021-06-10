<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Abstracts\ClientAccount;

class AccountApplication extends ClientAccount 
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'account_type',
        'company_name', 
        'company_registration_number', 
        'company_owner_name',
        'company_email',
        'company_phone',
        'company_website',
        'company_address',
        'company_city',
        'company_country',
    ];

    /**
     * The attributes that should be cast.
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:s',
    ];

    protected $hidden = ['updated_at'];

}
