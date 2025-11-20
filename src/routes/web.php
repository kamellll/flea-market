<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::post('/register', [AuthController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::get('/', [ItemController::class, 'index']);
Route::middleware('auth')->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'index']);
    Route::post('/mypage/profile/update', [ProfileController::class, 'store']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});