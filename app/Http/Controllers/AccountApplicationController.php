<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\AccountApplication;
use App\Http\Requests\ApplicationRequest;
use App\Http\Resources\ApplicationResource;

class AccountApplicationController extends Controller
{
    /**
     * Submit an account request
     * @param ApplicationRequest $request
     * @return Response
     */
    public function create(ApplicationRequest $request){
        $application = new AccountApplication();
        $application->fill($request->all());
        $application->save();
        return new ApplicationResource($application, true, "Application Submitted");
    }
}
