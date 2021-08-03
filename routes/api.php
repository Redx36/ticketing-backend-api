<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
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

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function () {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify', [AuthController::class, 'verify']);
    Route::post('validate', [AuthController::class, 'validatePhoneNumber']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('reset', [AuthController::class, 'reset']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);

});

Route::group([

    'middleware' => 'api',
    'headers' => ['Access-Control-Allow-Origin' => '*']

], function () {

    //tickets routes
    Route::get('tickets/todo', [TicketController::class, 'getTodo']);
    Route::get('tickets/done', [TicketController::class, 'getDone']);
    Route::post('ticket/add', [TicketController::class, 'addTicket']);
    Route::post('ticket/{id}/updateTicket', [TicketController::class, 'updateTicket'])->where('id', '[0-9]+');
    Route::post('ticket/{id}/updateOrder', [TicketController::class, 'updateOrder'])->where('id', '[0-9]+');
});
