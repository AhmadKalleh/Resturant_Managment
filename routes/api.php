<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Table\TableController;
use App\Mail\SendLinkMail;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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


Route::controller(AuthController::class)->group(function ()
{
    Route::post('/register_pendding','register_pendding_user');

    Route::post('/login','login')   ;

    Route::post('/register','register');

    Route::post('/logout','logout')->middleware('auth:sanctum');

});



Route::controller(TableController::class)->group(function()
{
    Route::middleware(['auth:sanctum'])->group(function ()
    {
         Route::get('/show-table','show')->middleware('can:show-table');

        Route::get('/index_table','index')->middleware('can:index-table');

        Route::post('/store_table','store')->middleware('can:create-table');

        Route::post('/update_table','update')->middleware('can:update-table');

        Route::delete('/destroy_table','destroy')->middleware('can:delete-table');

    });
});
