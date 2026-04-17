<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\GetuseridController;
use App\Http\Controllers\GetusersController;
use App\Http\Controllers\UploadpictureController;
use App\Http\Controllers\MfavalidationController;
use App\Http\Controllers\ChangepasswordController;
use App\Http\Controllers\DeleteuserController;
use App\Http\Controllers\ActivatemfaController;
use App\Http\Controllers\UpdateprofileController;
use App\Http\Controllers\AddproductController;
use App\Http\Controllers\ProductlistController;
use App\Http\Controllers\ProductsearchController;

use App\Http\Controllers\ChartController;
use App\Http\Controllers\PdfController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'loginuser']);
Route::get('/getuserid/{id}', [GetuseridController::class, 'getUserbydid']);
Route::get('/getallusers', [GetusersController::class, 'getAllusers']);
Route::post('/uploadpicture/{id}', [UploadpictureController::class, 'updateProfilepicture']);
Route::patch('/mfa/verifytotp/{id}', [MfavalidationController::class, 'validateOtp']);
Route::patch('/changepassword/{id}', [ChangepasswordController::class, 'changeUserpassword']);
Route::patch('/activatemfa/{id}', [ActivatemfaController::class, 'enableMfa']);
Route::patch('/profileupdate/{id}', [UpdateprofileController::class, 'updateUser']);

Route::delete('/deleteuser/{id}', [DeleteuserController::class, 'deleteUser']);

Route::post('/addproduct', [AddproductController::class, 'addProduct']);
Route::get('/productlist/{page}', [ProductlistController::class, 'listProducts']);
Route::get('/productsearch/{page}/{key}', [ProductsearchController::class, 'productSearch']);

Route::get('/chartdata', [ChartController::class, 'generateChart']);
Route::get('/pdfreport', [PdfController::class, 'generatePdf']);
