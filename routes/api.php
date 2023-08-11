<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\home\homeController;
use App\Http\Controllers\ProductController;
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


Route::post('user/create/store', [usersController::class, 'store']);
Route::get('user/create', [usersController::class, 'create']);
Route::get('user', [usersController::class, 'index']);
Route::get('/user/edit/{id}', [usersController::class, 'edit']);
Route::put('user/update/{id}', [usersController::class, 'update']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/user', [usersController::class, 'user']);
    Route::post('auth/logout', [usersController::class, 'logout']);

    //product

    Route::get('product', [ProductController::class, 'index']);
    Route::get('product/create', [ProductController::class, 'create']);
    Route::post('product/create/store', [ProductController::class, 'store']);
    Route::get('product/edit/{id}', [ProductController::class, 'edit']);
    Route::put('product/update/{id}', [ProductController::class, 'update']);
});

Route::post('auth/login', [usersController::class, 'login']);



//category
Route::get('category', [CategoriesController::class, 'index']);
Route::get('category/create', [CategoriesController::class, 'create']);
Route::post('category/create/store', [CategoriesController::class, 'store']);
Route::get('category/edit/{id}', [CategoriesController::class, 'edit']);
Route::put('category/update/{id}', [CategoriesController::class, 'update']);

//showhomecate
Route::get('category/showhome', [CategoriesController::class, 'showhome']);
//home
Route::get('home', [homeController::class, 'home']);
Route::get('home/products/{id}', [homeController::class, 'getProducts']);
