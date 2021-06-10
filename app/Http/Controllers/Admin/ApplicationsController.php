<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\AccountApplication;
use App\Models\RejectedApplication;
use App\Http\Resources\AccountResource;
use App\Http\Resources\ApplicationResource;
use App\Http\Resources\ApplicationCollection;
use App\Http\Resources\RejectedApplicationResource;
use App\Http\Resources\RejectedApplicationCollection;

class ApplicationsController extends Controller
{
    public function __construct(){
        $this->middleware('can:manage-app');
    }
    /**
     * Get all account applications
     * @return \App\Http\Resources\ApplicationCollection
     */
    public function index(){
        return new ApplicationCollection(AccountApplication::all());
    }

    /**
     * Get specific account application
     * @param  \App\Models\AccountApplication $application
     * @return \App\Http\Resources\ApplicationResource
     */
    public function show(AccountApplication $application){
        return new ApplicationResource($application);
    }

    /**
     * Accept an account application: create an account and an associated user 
     * @param \App\Models\AccountApplication $application
     * @return Mixed
     */
    public function accept(AccountApplication $application){
        try{
            $account = Account::createAccountWithUser($application);
            return new AccountResource($account);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Reject an account application
     * @param \App\Models\AccountApplication $application
     * @param \Illuminate\Http\Request $request
     * @return Mixed
     */
    public function reject(AccountApplication $application, Request $request){

        $request->validate([
            'reason' => 'required|string|max:191',
        ]);
        try{
            $rejected = RejectedApplication::createRejected($application, $request->reason);
            return new RejectedApplicationResource($rejected, true, "Application Rejected");
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

     /**
     * Get rejected account applications
     * @return \App\Http\Resources\RejectedApplicationCollection
     */
    public function rejected(){
        return new RejectedApplicationCollection(RejectedApplication::all());
    }

    /**
     * Get single rejected account application
     * @param \App\Models\RejectedApplication $application
     * @return \App\Http\Resources\RejectedApplicationResource
     */
    public function showRejected(RejectedApplication $application){
        return new RejectedApplicationResource($application);
    }

     /**
     * Delete a rejected account application
     * @param \App\Models\RejectedApplication $application
     * @return \App\Http\Resources\RejectedApplicationResource
     */
    public function deleteRejected(RejectedApplication $application){
        $application->delete();
        return new RejectedApplicationResource($application, true, "Application Deleted");
    }

}
