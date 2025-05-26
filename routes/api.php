<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Chat\ChatController;

use App\Http\Controllers\Category\CategoryController;

use App\Http\Controllers\Table\TableController;
use App\Http\Controllers\Extra\ExtraControlle;
use App\Http\Controllers\Favorite\FavoriteController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\User\UserController;
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


Route::controller(AuthController::class)->group(function ()
{
    Route::post('/register_pendding','register_pendding_user');

    Route::post('/login','login')   ;

    Route::post('/register','register');

    Route::post('/send_varification_code_to_email','send_varification_code_to_email');

    Route::post('/is_varification_code_right','is_varification_code_right');

    Route::post('/reset_password','reset_password');

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




Route::controller(TableController::class)->group(function()
{
    Route::middleware(['auth:sanctum'])->group(function ()
    {
        Route::get('/show_table','show')->middleware('can:show-table');

        Route::get('/index_table','index')->middleware('can:index-table');

        Route::post('/store_table','store')->middleware('can:create-table');

        Route::post('/update_table','update')->middleware('can:update-table');

        Route::delete('/destroy_table','destroy')->middleware('can:delete-table');

    });

});



Route::controller(CategoryController::class)->group(function()
{
    Route::middleware(['auth:sanctum'])->group(function ()
    {
        Route::get('/show_category','show')->middleware('can:show-categories');

        Route::get('/index_category','index')->middleware('can:index-categories');

        Route::post('/store_category','store')->middleware('can:create-categories');

        Route::post('/update_category','update')->middleware('can:update-categories');

        Route::delete('/destroy_category','destroy')->middleware('can:delete-categories');

    });

});



Route::controller(ProductController::class)->group(function ()
{
    Route::middleware(['auth:sanctum'])->group(function ()
    {
        Route::get('/index_product','index')->middleware('can:index-products');

        Route::get('/top_ratings','top_ratings')->middleware('can:index-products');

        Route::post('/store_product','store')->middleware('can:create-products');

        Route::get('/show_product','show')->middleware('can:show-products');

        Route::get('/searchByCategory','searchByCategory')->middleware('can:show-products');

        Route::get('/search','search')->middleware('can:show-products');

        Route::get('/filter','filter')->middleware('can:filter');

        Route::post('/update_product','update')->middleware('can:update-products');

        Route::delete('/destroy_product','destroy')->middleware('can:delete-products');
    });

});



Route::controller(ChatController::class)->group(function ()
{
    Route::middleware(['auth:sanctum'])->group(function ()
    {
        Route::get('/index_chat','index_chat')->middleware('can:index-chat');

        Route::get('/index_chat_message','index_chat_message')->middleware('can:index-chat_message');

        Route::post('/send_message','send_message')->middleware('can:send-message');
    });

});



Route::controller(CartController::class)->group(function()
{
    Route::middleware(['auth:sanctum'])->group(function ()
    {

        Route::get('/index_cart','index')->middleware('can:index-cart');

        Route::post('/store_cart','store')->middleware('can:create-cart');

        Route::post('/update_quantity','update_quantity')->middleware('can:update-cart');

        Route::delete('/destroy_extra','destroy_extra')->middleware('can:update-cart');

        Route::delete('/destroy_cart','destroy')->middleware('can:update-cart');

    });

});
