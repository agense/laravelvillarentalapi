<?php

namespace App\Http\Controllers\Admin;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use App\Http\Resources\AccountCollection;
use App\Http\Requests\AccountUpdateRequest;

class AccountsController extends Controller
{

    /**
     * Get all accounts
     * Can only be done by admin user
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\AccountCollection
     */
    public function index(Request $request){
        $this->authorize('viewAny', Account::class);

        if($request->has('type')){
            $accounts = Account::getByType($request->query('type'));
        }else{
            $accounts = Account::all();
        }
        return new AccountCollection($accounts);
    }

    /**
     * Get specific account
     * Can only be done by admin user or an account owner 
     * @param \App\Models\Account $account
     * @return \App\Http\Resources\AccountResource
     */
    public function show(Account $account){
        $this->authorize('view', $account);
        $account->load('user');
        return new AccountResource($account);
    }

    /**
     * Update specific account
     * Can only be done by an account owner 
     * @param \App\Models\Account $account
     * @param \App\Http\Requests\AccountUpdateRequest $request
     * @return \App\Http\Resources\AccountResource
     */
    public function update(Account $account, AccountUpdateRequest $request){
        $account->updateAccount();
        return new AccountResource($account, true, 'Account Updated');
    }

    // DELETES
    /**
     * Deactivate specific account
     * @param  \App\Models\Account $account
     * @return \Illuminate\Http\Response
     */
    public function deactivate(Account $account){

        $this->authorize('delete', $account);
        try{
            $account->deactivate();
            return response()->json(['message' => 'Account Deactivated']);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Get deleted account list or a single deleted account based on param passed
     * @param Int $id optional
     * @return Mixed
     */
    public function inactive(Int $id = null)
    {
        $this->authorize('restore', Account::class);

        if($id !== null){
            $account = Account::onlyTrashed()->findOrFail($id);
            return new AccountResource($account);
        }
        return new AccountCollection(Account::onlyTrashed()->get());
    }

    /**
     * Restore A Deleted Account
     * @param Int $id optional
     * @return \App\Http\Resources\AccountResource
     */
    public function activate(Int $id)
    {
        $this->authorize('restore', Account::class);

        $account = Account::onlyTrashed()->findOrFail($id);
        $account->activate();
        return new AccountResource($account, true, "Account has been activated");
    }

    /**
     * Delete specific account permanently
     * Only accounts that have been deactivated can be permanently deleted
     * @param Int $id optional
     * @return \Illuminate\Http\Response
     */
    public function destroy(Int $id){

        $this->authorize('forceDelete', Account::class);

        $account = Account::onlyTrashed()->findOrFail($id);
        try{
            $account->deletePermanently();
            return response()->json(['message' => 'Account Deleted Permanently']);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
