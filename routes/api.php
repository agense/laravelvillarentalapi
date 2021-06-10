<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\ApiRouteDisplayService;
use Illuminate\Support\Facades\Password;
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
Route::prefix('auth')->namespace('Auth')->group(function(){
    Route::post('/login', 'AuthenticationController@login')->name('login');
    Route::post('/logout', 'AuthenticationController@logout')->middleware('auth:sanctum')->name('logout');
    Route::post('/regenerate-password', 'ForgotPasswordController@sendResetLinkEmail')->name('password.forgot');
    Route::post('/reset-password', 'ResetPasswordController@resetPassword')->name('password.reset');
});

//Client Account Application
Route::post('accounts/application', 'AccountApplicationController@create')->name('accounts.applications.create');

// ADMIN SYSTEM ROUTES
Route::prefix('admin')->namespace('Admin')->middleware('auth:sanctum')->group(function(){

    //Client Account Applications
    Route::prefix('applications')->name('applications.')->group(function(){
        Route::get('/rejected/{application}', 'ApplicationsController@showRejected')->name('rejected.show');
        Route::delete('/rejected/{application}', 'ApplicationsController@deleteRejected')->name('rejected.delete');
        Route::get('/rejected', 'ApplicationsController@rejected')->name('rejected.index');
        
        Route::get('/', 'ApplicationsController@index')->name('index');
        Route::get('/{application}', 'ApplicationsController@show')->name('show');
        Route::put('/{application}/accept', 'ApplicationsController@accept')->name('confirm');
        Route::put('/{application}/reject', 'ApplicationsController@reject')->name('reject');
       
    });

    //Client Accounts
    Route::prefix('accounts')->name('accounts.')->group(function(){
        Route::delete('/{account}/deactivate', 'AccountsController@deactivate')->name('deactivate')->where('account', '[0-9]+');
        Route::put('/{account}/activate', 'AccountsController@activate')->name('activate')->where('account', '[0-9]+');
        Route::get('/inactive/{account?}', 'AccountsController@inactive')->name('inactive');
        Route::delete('/{account}', 'AccountsController@destroy')->name('destroy')->where('account', '[0-9]+');
        Route::get('/', 'AccountsController@index')->name('index');
        Route::get('/{account}', 'AccountsController@show')->name('show')->where('account', '[0-9]+');
        Route::put('/{account}', 'AccountsController@update')->name('update')->where('account', '[0-9]+');
        Route::get('/{account}/villas', 'VillasController@showOwned')->name('villas')->where('account', '[0-9]+');
    });

    //Users
    Route::prefix('users')->name('users.')->group(function(){
        Route::post('/admin', 'UsersController@createSystemAdmin')->name('create.admin');
        Route::get('/{user}', 'UsersController@show')->name('show')->where('user', '[0-9]+');
        Route::get('/', 'UsersController@index')->name('index')->name('index');
        Route::put('/{user}/password', 'UsersController@updatePassword')->name('passwords.update')->where('user', '[0-9]+');
        Route::put('/{user}', 'UsersController@update')->name('update')->where('user', '[0-9]+');
        Route::delete('/{user}/deactivate', 'UsersController@deactivate')->name('deactivate')->where('user', '[0-9]+');
        Route::delete('/{user}', 'UsersController@destroy')->name('destroy')->where('user', '[0-9]+');
        Route::put('/{user}/activate', 'UsersController@activate')->name('activate')->where('user', '[0-9]+');
        Route::get('/inactive/{user?}', 'UsersController@inactive')->name('inactive');
    });

    //Locations
    Route::resource('regions', 'RegionsController')->except(['create', 'edit']);

    Route::resource('cities', 'CitiesController')->except(['show','create', 'edit']);

    //Categories
    Route::resource('categories', 'CategoriesController')->except(['show','create','edit']);
    
    //Facilities
    Route::resource('facilities', 'FacilitiesController')->except(['show','create', 'edit']);
    
    //Villas And Relations
    Route::prefix('villas')->name('villas.')->group(function(){
        Route::put('/{villa}/facilities/add', 'VillasController@addFacilities')->name('facilities.add');
        Route::delete('/{villa}/facilities/remove', 'VillasController@removeFacilities')->name('facilities.remove');

        Route::put('/{villa}/categories/add', 'VillasController@addCategories')->name('categories.add');
        Route::delete('/{villa}/categories/remove', 'VillasController@removeCategories')->name('categories.remove');
        
        Route::put('/{villa}/images/upload', 'VillasController@uploadImages')->name('images.upload');
        Route::delete('/{villa}/images/delete', 'VillasController@deleteImages')->name('images.delete');

        // Availabilities
        Route::get('/{villa}/availability', 'AvailabilitiesController@getAvailability')->name('availability.show');
        Route::put('/{villa}/availability', 'AvailabilitiesController@updateAvailability')->name('availability.show');

        //Prices
        Route::get('/{villa}/prices', 'PricesController@getPrices')->name('prices.show');
        Route::put('/{villa}/prices', 'PricesController@updatePrices')->name('prices.update');
        
        Route::put('/{villa}/deactivate', 'VillasController@deactivate')->name('deactivate');
        Route::put('/{villa}/activate', 'VillasController@activate')->name('activate');
        Route::get('/inactive/{villa?}', 'VillasController@inactive')->name('inactive');
    });
    Route::resource('villas', 'VillasController')->except(['create', 'edit']);
});

//DISTRIBUTION ROUTES
Route::prefix('distribution')->namespace('Distribution')->name('distribution.')->middleware(['auth:sanctum', 'can:distribute-content'])->group(function(){
    Route::get('/categories','CategoriesController@index')->name('categories.index');
    Route::get('/facilities','FacilitiesController@index')->name('facilities.index');
    
    Route::get('/regions','LocationsController@getRegions')->name('regions.index');
    Route::get('/regions/{region}','LocationsController@getRegion')->name('regions.show');
    Route::get('/cities','LocationsController@getCities')->name('cities.index');

    Route::get('/villas','VillasController@index')->name('villas.index');
    Route::get('/villas/{id}','VillasController@show')->name('villas.show');
});