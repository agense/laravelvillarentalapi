<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Str;
use App\Abstracts\ClientAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends ClientAccount 
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'company_name', 
        'company_registration_number', 
        'company_owner_name',
        'company_email',
        'company_phone',
        'company_website',
        'company_address',
        'company_city',
        'company_country',
        'account_type',
    ];

    /**
     * The attributes that can be updated
     * @var array
     */
    protected $updatable = [
        'company_owner_name',
        'company_email',
        'company_phone',
        'company_website',
        'company_address',
        'company_city',
        'company_country',
    ];


    /**
     * Relationship with Admin Model
    */
    public function user(){
        return $this->hasOne('App\Models\User');
    }

    /**
     * Relationship with Villa Model
    */
    public function villas(){
        return $this->hasMany('App\Models\Villa');
    }

    // Model Methods

    /**
     * Assigns account number to current account instance
     * @return void
     */
    public function assignNumber(){
        $this->number = Str::orderedUuid();
    }

    /**
     * Creates a new account and a new user for that account
     * @param AccountApplication $application
     * @return self
     */
    public static function createAccountWithUser(AccountApplication $application){
        $account = new self();
        $account->fill($application->toArray());
        $account->assignNumber();

        DB::transaction(function() use ($account, $application){
            $account->save();
            User::createAccountUser($account);
            $application->delete();
        });
        $account->loadMissing('user');
        return $account;
    }

    /**
     * Update account data
     * @param Array $data
     * @return void
     */
    public function updateAccount(){
        $this->fill(request()->only($this->updatable));
        $this->save();
    }

    /**
     * Delete Account And Associated User
     * @return void
     */
    public function deletePermanently(){
        DB::transaction(function(){
            $this->loadMissing([
                'villas'=> function ($query) {
                    $query->onlyTrashed();
                },
            ]);
            foreach($this->villas as $villa){
                $villa->forceDelete();
            }
            $this->user()->forceDelete();
            $this->forceDelete();
        });
    }

    /**
     * Deactivate Account, Associated User and Belonging Villas
     * @return void
     */
    public function deactivate(){
        $this->loadMissing('villas');
        DB::transaction(function(){
            foreach($this->villas as $villa){
                $villa->delete();
            }
            $this->user->delete();
            $this->delete();
        });
    }

    /**
     * Restore Account, Associated User and Belonging Villas
     * @return void
     */
    public function activate(){
        DB::transaction(function(){
            $this->restore();
            $this->user()->restore();
            $this->loadMissing([
                'villas'=> function ($query) {
                    $query->onlyTrashed();
                },
            ]);
            foreach($this->villas as $villa){
                $villa->restore();
            }
        });
    }

    /**
     * Check if an account is of type supplier
     * @return Bool
     */
    public function isSupplier(){
        $accountType = is_int($this->account_type) ? self::stringifyType($this->account_type) : $this->account_type;
        return $accountType == 'SUPPLIER';
    }

    /**
     * Return all accounts of specified type
     * @param String $type
     * @return Collection
     */
    public static function getByType(String $type){
        if(!self::isValidType($type)){
            abort(422, 'Invalid Account Type');
        }
        return self::where('account_type', self::getTypeValue($type))->get();
    }

}
