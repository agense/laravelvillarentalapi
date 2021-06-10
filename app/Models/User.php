<?php

namespace App\Models;

use App\Models\Account;
use App\Traits\UserTypes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword, UserTypes, SoftDeletes;

    //User types
    private const SYSTEM_ADMIN = 1;
    private const CLIENT = 2;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The relationships that should always be loaded.
     * @var array
     */
    protected $with = ['account'];

    // MODEL RELATIONS
    /**
     * Relationship with Account Model
    */
    public function account(){
        return $this->belongsTo('App\Models\Account');
    }

    //QUERY SCOPES
    /**
     * Scope a query to only include users of type client.
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClients($query)
    {
        return $query->where('type', self::CLIENT);
    }

    /**
     * Scope a query to only include users of type system admin.
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSystemAdmins($query)
    {
        return $query->where('type', self::SYSTEM_ADMIN);
    }


    // MODEL METHODS
    /**
     * Issues an access token for the user
     * @return String 
     */
    public function getAccessToken(){
        return $this->createToken('AccessToken')->plainTextToken;
    }

    /**
     * Updates password on current user instance
     * @param String $password
     * @return void
     */
    public function updatePassword(String $password){
        $this->password = Hash::make($password);
        $this->save();
    }

    /**
     * Updates user info on current user instance
     * @param Array $data
     * @return void
     */
    public function updateInfo(){
        $this->fill(request()->only('name', 'email'));
        $this->save();
    }

    /**
     * Creates a system admin type user, i.e. user without client account that can access all features of the system
     * @param Array $data
     * @return self
     */
    public static function createSystemAdminUser(Array $data){
        $user = new self();
        $user->fill($data);
        $user->password = Hash::make($data['password']);
        $user->type = self::SYSTEM_ADMIN;
        $user->save();
        return $user;
    }

    /**
     * Creates a user for a specified account
     * @param Account $account
     * @return self
     */
    public static function createAccountUser(Account $account){
        $user = new self();
        $user->name = $account->company_owner_name;
        $user->email = $account->company_email;
        $user->password = Hash::make(uniqid());
        $user->setType('CLIENT');
        $user->account()->associate($account);
        $user->save();
        return $user;
    }

    /**
     * Check if a user is of type client
     * @return Bool
     */
    public function isClient(){
        if(!$this->account || $this->account->id == null ){
            return false;
        }
        return $this->type == self::typeToString(self::CLIENT);
    }

    /**
     * Check if a user is of type system admin
     * @return Bool
     */
    public function isSystemAdmin(){
        return $this->type == self::typeToString(self::SYSTEM_ADMIN);
    }

    /**
     * Check if a user has a supplier account
     * @return Bool
     */
    public function isSupplier(){
        if(!$this->isClient()){
            return false;
        }
        $accountType = is_int($this->account->account_type) ? Account::stringifyType($this->account->account_type) : $this->account->account_type;
        return $accountType == 'SUPPLIER';
    }

    /**
     * Check if a user has a distributor account
     * @return Bool
     */
    public function isDistributor(){
        if(!$this->isClient()){
            return false;
        }
        $accountType = is_int($this->account->account_type) ? Account::stringifyType($this->account->account_type) : $this->account->account_type;
        return $accountType == 'DISTRIBUTOR';
    }

    /**
     * Check if a user owns the account passed as argument
     * @param Account $account
     * @return Bool
     */
    public function isAccountOwner(Account $account){
        return $this->account->id == $account->id;
    }

    /**
     * Get users by user type
     * @param String $type
     * @return Collection
     */
    public static function getByType(String $type){
        if(!self::isValidType($type)){
            abort(422, 'Invalid User Type');
        }
        return self::where('type', self::typeToValue($type))->get();
    }

}
