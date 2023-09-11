<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\colorController;
use App\Http\Controllers\couponController;
use App\Http\Controllers\home\homeController;
use App\Http\Controllers\ManufacturersController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ratingController;
use App\Http\Controllers\ShipsController;
use App\Http\Controllers\sizeController;
use App\Http\Controllers\staticsicalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\usersController;

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



Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/user', [usersController::class, 'user']);
    Route::post('auth/logout', [usersController::class, 'logout']);

    //product
    Route::get('product', [ProductController::class, 'index']);
    Route::get('product/create', [ProductController::class, 'create']);
    Route::post('product/create/store', [ProductController::class, 'store']);
    Route::get('product/edit/{id}', [ProductController::class, 'edit']);
    Route::put('product/update/{id}', [ProductController::class, 'update']);

    //user
    Route::post('user/create/store', [usersController::class, 'store']);
    Route::get('user/create', [usersController::class, 'create']);
    Route::get('user', [usersController::class, 'index']);
    Route::get('/user/edit/{id}', [usersController::class, 'edit']);
    Route::put('user/update/{id}', [usersController::class, 'update']);
    Route::get('user/search/{key}', [usersController::class, 'search']);
    Route::get('user/deletel/{id}', [usersController::class, 'deletel']);

    //category
    Route::get('category', [CategoriesController::class, 'index']);
    Route::get('category/create', [CategoriesController::class, 'create']);
    Route::post('category/create/store', [CategoriesController::class, 'store']);
    Route::get('category/edit/{id}', [CategoriesController::class, 'edit']);
    Route::put('category/update/{id}', [CategoriesController::class, 'update']);
    Route::get('category/deletel/{id}', [CategoriesController::class, 'deletel']);

    //color
    Route::post('color/create', [colorController::class, 'create']);
    //size
    Route::post('size/create', [sizeController::class, 'create']);
    //manufacturers
    Route::post('manufacturers/create', [ManufacturersController::class, 'create']);
    //shiping
    Route::get('ships', [ShipsController::class, 'index']);
    Route::get('ships/create', [ShipsController::class, 'create']);
    Route::post('ships/create/store', [ShipsController::class, 'store']);
    //rate
    Route::post('home/rate/{id}', [ratingController::class, 'create']);
    Route::put('home/rate/update/{id}', [ratingController::class, 'update']);
    //coupons
    Route::get('coupons', [couponController::class, 'index']);
    Route::post('coupon/create', [couponController::class, 'create']);
    //coupons_user
    Route::post('user/coupon/create', [couponController::class, 'user_create']);
    Route::get('user/coupon/index', [couponController::class, 'user_index']);
    Route::post('user/coupon/findcode', [couponController::class, 'find_code']);


    //checkout
    Route::post('cart/checkout', [CartController::class, 'checkout']);
    //order

    Route::get('order', [orderController::class, 'index']);
    Route::get('order/preview/{id}', [orderController::class, 'preview']);
    Route::post('order/updateStatus/{id}', [orderController::class, 'updateStatus']);
    Route::post('order/compleOrder/{id}', [orderController::class, 'comleOrder']);
    Route::get('order/compleOrder/index', [orderController::class, 'indexCompleOrder']);
    Route::post('order/revenue', [orderController::class, 'revenue']);

    //staticical
    Route::get('staticical', [staticsicalController::class, 'index']);


});

Route::post('auth/login', [usersController::class, 'login']);


//showhomecate
Route::get('category/showhome', [CategoriesController::class, 'showhome']);
//home
Route::get('home', [homeController::class, 'home']);
Route::get('home/products/{id}', [homeController::class, 'getProducts']);

//viewProduct
Route::post('home/product/viewProduct/{id}', [homeController::class, 'viewProduct']);
