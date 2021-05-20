<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\ApiRouteDisplayService;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Display all existing routes in json format
Route::get('routes', function () {
    return response()->json((new ApiRouteDisplayService)->readData());
});

// Authentication routes
Route::prefix('auth')->group(function(){
    Route::post('login', 'AuthenticationController@login');
    Route::post('logout', 'AuthenticationController@logout')->middleware('auth:sanctum');
    Route::post('register', 'AuthenticationController@register');
});

// ADMIN SYSTEM ROUTES
Route::prefix('admin')->namespace('Admin')->middleware('auth:sanctum')->group(function(){

    Route::resource('regions', 'RegionsController')->except(['show','create', 'edit']);

    Route::get('regions/{region}/cities', 'RegionsController@getCities')->name('region.cities');

    Route::resource('cities', 'CitiesController')->except(['show','create', 'edit']);

    Route::resource('categories', 'CategoriesController')->except(['show','create','edit']);
    
    Route::resource('facilities', 'FacilitiesController')->except(['show','create', 'edit']);
    
    //Villas And Relations
    Route::put('villas/{villa}/facilities/add', 'VillasController@addFacilities');
    Route::delete('villas/{villa}/facilities/remove', 'VillasController@removeFacilities');

    Route::put('villas/{villa}/categories/add', 'VillasController@addCategories');
    Route::delete('villas/{villa}/categories/remove', 'VillasController@removeCategories');
    
    Route::put('villas/{villa}/images/upload', 'VillasController@uploadImages');
    Route::delete('villas/{villa}/images/delete', 'VillasController@deleteImages');

    // Availabilities
    Route::get('villas/{villa}/availability', 'AvailabilitiesController@getAvailability');
    Route::put('villas/{villa}/availability', 'AvailabilitiesController@updateAvailability');

    //Prices
    Route::get('villas/{villa}/prices', 'PricesController@getPrices');
    Route::put('villas/{villa}/prices', 'PricesController@updatePrices');
    
    Route::put('villas/{villa}/deactivate', 'VillasController@deactivate');
    Route::put('villas/{villa}/activate', 'VillasController@activate');
    Route::get('villas/inactive/{villa?}', 'VillasController@inactive');
    Route::resource('villas', 'VillasController')->except(['create', 'edit']);
});