<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/password_reset', [ UserController::class, 'forget_password']);
Route::post('/auth/password_reset/{token}', [ UserController::class, 'reset_password']);
Route::get('/users', [UserController::class, 'list_users']);
Route::get('/users/{id}', [UserController::class, 'user_data']);
Route::post('/users', [UserController::class, 'user_create']);
Route::middleware('auth')->patch('/users/{id}',[UserController::class, 'user_update']);
Route::middleware('auth')->delete('/users/{id}',[UserController::class, 'user_delete']);
Route::group([
    'prefix' => 'auth'
], function () {

    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::post('registration', 'App\Http\Controllers\AuthController@registration');
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
    Route::post('me', 'App\Http\Controllers\AuthController@me');

});



