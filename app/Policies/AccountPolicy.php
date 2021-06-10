<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Account;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization;

     /**
     * Determine whether the user can view a list of accounts
     * Only system admins can view account lists
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isSystemAdmin();
    }

    /**
     * Determine whether the user can view the model.
     * Only account owners and system admins can view account details
     * @param  \App\Models\User  $user
     * @param  \App\Models\Account $account
     * @return mixed
     */
    public function view(User $user, Account $account)
    {
        return $user->isSystemAdmin() || $user->isAccountOwner($account);
    }

    /**
     * Determine whether the user can update the model.
     * Only users who own the account can update account data
     * @param  \App\Models\User  $user
     * @param  \App\Models\Account $account
     * @return mixed
     */
    public function update(User $user, Account $account)
    {
       return $user->isAccountOwner($account);
    }

    /**
     * Determine whether the user can delete the model.
     * Only account owners and system admins can delete an account
     * @param  \App\Models\User  $user
     * @param  \App\Models\Account $account
     * @return mixed
     */
    public function delete(User $user, Account $account)
    { 
        return $user->isSystemAdmin() || $user->isAccountOwner($account);
    }

     /**
     * Determine whether the user can view a list of villas owned by an account
     * Only system admins and account owners can view the list of villas owned by the account
     * Also only supplier accounts can have owned villas
     * @param  \App\Models\User  $user
     * @param  \App\Models\Account  (!!! Note, account model passed is not required to be associated with user model passed, for example when user is a system admin)
     * @return mixed
     */
    public function viewOwnedVillas(User $user, Account $account)
    {
        if(!$account->isSupplier()){
            return false;
        }
        return $user->isSystemAdmin() || $user->isAccountOwner($account);
    }

    
    /**
     * Determine whether the user can restore the model.
     * Only system admin users can restore an account. 
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function restore(User $user)
    {
        return $user->isSystemAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Only system admin users can permanently delete an account. 
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function forceDelete(User $user)
    {
        return $user->isSystemAdmin();
    }
}
