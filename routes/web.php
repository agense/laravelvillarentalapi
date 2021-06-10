<?php
use Illuminate\Support\Facades\Route;
use App\Services\ApiRouteDisplayService;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $routeTable = (new ApiRouteDisplayService)->displayDataAsHtml();
    return view('welcome', compact('routeTable'));
});