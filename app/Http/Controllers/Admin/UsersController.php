<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest; 
use App\Http\Requests\PasswordUpdateRequest;
use Illuminate\Support\Facades\Gate;

class UsersController extends Controller
{
    /**
     * Display a listing of users or users of specific type is type is present in the query
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\UserCollection
     */
    public function index(Request $request)
    {
        Gate::authorize('manage-app');

        if($request->has('type')){
            $users = User::getByType($request->query('type'));
        }else{
            $users = User::all();
        }
        return new UserCollection($users);
    }

    /**
     * Create a new user of type SYSTEM_ADMIN, i.e. user not associated with an account
     * @param  \App\Http\Requests\UserCreateRequest $request
     * @return \App\Http\Resources\UserResource
     */
    public function createSystemAdmin(UserCreateRequest $request)
    {
        $user = User::createSystemAdminUser($request->all());
        return new UserResource($user, true, "Admin User Created");
    }

    /**
     * Display the specified resource.
     * @param  \App\Models\User $user
     * @return \App\Http\Resources\UserResource
     */
    public function show(User $user)
    {
        if (Gate::none(['manage-app', 'manage-own-data'], $user)) {
            abort(403);
        }
        return new UserResource($user);
    }

    /**
     * Update authenticated users info except password and type
     * @param  \App\Http\Requests\UserUpdateRequest $request
     * @param  \App\Models\User $user
     * @return \App\Http\Resources\UserResource
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $user->updateInfo();
        return new UserResource($user, true, "User Data Updated");
    }

    /**
     * Update the authenticated users password
     * @param  \App\Http\Requests\PasswordUpdateRequest $request
     * @param  \App\Models\User $user
     * @return \App\Http\Resources\UserResource
     */
    public function updatePassword(PasswordUpdateRequest $request, User $user)
    {
        $user->updatePassword($request->password);
        return new UserResource($user, true, 'Password Updated');
    }

    // SOFT DELETES
    /**
     * Deactivate the specified user.
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function deactivate(User $user)
    {
        if (Gate::none(['manage-app', 'manage-own-data'], $user)) {
            abort(403);
        }
        if(!$user->isClient()){
            $user->delete();
            return response()->json(['message' => 'User Deactivated'], 200);
        }else{
            return response()->json(['message' => 'Users associated with a client account cannot be deactvated. Please deactivate the account if required,'], 400);
        }
    }

    /**
     * Get deleted user list or a single deleted user based on param passed
     * @param Int $id optional
     * @return Mixed
     */
    public function inactive(Int $id = null)
    {
        Gate::authorize('manage-app');
        if($id !== null){
            $user = User::onlyTrashed()->findOrFail($id);
            return new UserResource($user);
        }
        return new UserCollection(User::onlyTrashed()->get());
    }

    /**
     * Restore A Deleted User
     * @param Int $id optional
     * @return \App\Http\Resources\UserResource
     */
    public function activate(Int $id)
    {
        Gate::authorize('manage-app');
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return new UserResource($user, false, "User has been activated");
    }

    /**
     * Remove the user from db
     * Only users that have been deactivated can be permanently deleted
     * @param Int $id optional
     * @return \Illuminate\Http\Response
     */
    public function destroy(Int $id)
    {
        Gate::authorize('manage-app');

        $user = User::onlyTrashed()->findOrFail($id);

        if(!$user->isClient()){
            $user->forceDelete();
            return response()->json(['message' => 'User has been deleted permanently'], 200);
        }else{
            return response()->json(['message' => 'Users associated with a client account cannot be deleted. Please delete the account if required,'], 400);
        }
    }
}
