<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Villa;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\HandlesAuthorization;

class VillaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user is a system admin
     * Only system admins can see all villas in admin routes
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isSystemAdmin();
    }

    /**
     * Determine whether the user can view the model.
     * System admins can see all villas. Suppliers can see only their own villas.
     * @param  \App\Models\User  $user
     * @param  \App\Models\Villa  $villa
     * @return mixed
     */
    public function view(User $user, Villa $villa)
    {
       return $user->isSystemAdmin() || ($user->isSupplier() && $villa->isOwnedBy($user->account));
    }

    /**
     * Determine whether the user can create models.
     * Only users that have a supplier account can create a villa
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isSupplier();
    }

    /**
     * Determine whether the user can update the model.
     * Only users related to the supplier account to which the villa belongs can update a villa.
     * @param  \App\Models\User  $user
     * @param  \App\Models\Villa  $villa
     * @return mixed
     */
    public function update(User $user, Villa $villa)
    {
       return $user->isSupplier() && $villa->isOwnedBy($user->account);
    }

    /**
     * Determine whether the user can delete the model.
     * Only users related to the supplier account to which the villa belongs and system admins can delete a villa.
     * @param  \App\Models\User  $user
     * @param  \App\Models\Villa  $villa
     * @return mixed
     */
    public function delete(User $user, Villa $villa)
    {
        return $user->isSystemAdmin() || ( $user->isSupplier() && $villa->isOwnedBy($user->account) );
    }

    /**
     * Determine whether the user can restore the model.
     * Only system admin users can restore a villa. 
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function restore(User $user)
    {
        return $user->isSystemAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Only system admin users can permanently delete a villa. 
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function forceDelete(User $user)
    {
        return $user->isSystemAdmin();
    }

}
