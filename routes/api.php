<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Extra\ExtraControlle;
use App\Http\Controllers\Favorite\FavoriteController;
use App\Http\Controllers\User\UserController;
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


Route::controller(UserController::class)->group(function ()
{
    Route::middleware(['auth:sanctum'])->group(function ()
    {
        Route::get('/show_Info','show')->middleware('can:show-info');

        Route::post('/update_mobile','change_mobile')->middleware('can:change-mobile');

        Route::get('/check_password','check_password');

        Route::post('/update_password','update_password')->middleware('can:update-password');

        Route::post('/update_image_profile','update_image_profile')->middleware('can:update-image-profile');

        Route::delete('/delete_account','destroy')->middleware('can:delete-account');
    });

});


Route::controller(ExtraControlle::class)->group(function()
{
    Route::middleware(['auth:sanctum'])->group(function ()
    {
        Route::get('/index_extra','index')->middleware('can:index-extra');

        Route::get('/show_extra','show')->middleware('can:show-extra');

        Route::post('/store_extra','store')->middleware('can:create-extra');

        Route::post('/update_extra','update')->middleware('can:update-extra');

        Route::delete('/destroy_extra','destroy')->middleware('can:delete-extra');

    });
});




Route::controller(FavoriteController::class)->group(function ()
{
    Route::middleware(['auth:sanctum'])->group(function ()
    {
        Route::get('/index_favorite','index')->middleware('can:index-favorite');

        Route::post('/store_favorite','store')->middleware('can:create-favorite');

        Route::delete('/delete_favorite','destroy')->middleware('can:delete-favorite');
    });

});




// Route::get('/test',function () {
//     return QrCode::size(300)->generate('http://laraveldaily.com/courses');
// });
