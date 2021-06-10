<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Abstracts\ClientAccount;
use App\Models\AccountApplication;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RejectedApplication extends ClientAccount 
{
    use HasFactory;

    /**
     * Disable Timestamps
     */
    public $timestamps = false;

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
        'reason',
    ];

    /**
     * The attributes that should be cast.
     * @var array
     */
    protected $casts = [
        'applied_at' => 'datetime:Y-m-d h:s',
        'rejected_at' => 'datetime:Y-m-d h:s',
    ];

    public function __construct(){
        if($this->rejected_at == null){
            $this->rejected_at = Carbon::now();
        }
    }

    // MODEL METHODS
    /**
     * Create an instance of rejected application
     * @param AccountApplication $application
     * @param String $reason
     * @return self
     */
    public static function createRejected(AccountApplication $application, String $reason){

        $rejected = new self();
        $rejected->fill($application->toArray());
        $rejected->reason = $reason;
        $rejected->applied_at = $application->created_at;

        DB::transaction(function() use($rejected, $application){
            $rejected->save();
            $application->delete();
        });
        return $rejected;
    }

}
