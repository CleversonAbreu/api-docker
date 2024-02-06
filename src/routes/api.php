<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->namespace('App\Http\Controllers\Api')->group(function () {

    Route::post('/login','Auth\LoginJwtController@login');
    Route::get('/logout','Auth\LoginJwtController@logout');
    Route::get('/refresh','Auth\LoginJwtController@refresh');

    //new user
    Route::post('/users','UserController@store');

    //protected routes
    Route::group(['middleware'=>['jwt.auth']], function(){

        //users
        Route::prefix('users')->group(function(){
            Route::get('/','UserController@index');
            Route::get('/{id}','UserController@show');
            Route::post('/','UserController@store');
            Route::put('/{id}','UserController@update');
            Route::delete('/{id}','UserController@destroy');
        });

        //products
        Route::prefix('products')->group(function(){
            Route::get('/','ProductController@index');
            Route::get('/{id}','ProductController@show');
            Route::post('/','ProductController@store');
            Route::put('/{id}','ProductController@update');
            Route::delete('/{id}','ProductController@destroy');
        });

    });

    // Route::group(['middleware' => ['jwt.auth']],function(){

    // });

    // //products
    // Route::prefix('products')->group(function () {
    //     Route::get('/', 'ProductController@index');
    //     Route::get('/{id}', 'ProductController@show');
    //     Route::post('/', 'ProductController@store');
    //     Route::put('/{id}', 'ProductController@update');
    //     Route::delete('/{id}', 'ProductController@destroy');
    // });

    // //users
    // Route::prefix('users')->group(function () {
    //     Route::get('/', 'UserController@index');
    //     Route::get('/{id}', 'UserController@show');
    //     Route::post('/', 'UserController@store');
    //     Route::put('/{id}', 'UserController@update');
    //     Route::delete('/{id}', 'UserController@destroy');
    // });
});