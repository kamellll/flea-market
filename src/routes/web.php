<?php

use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
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
Route::post('/', [ItemController::class, 'index']);
Route::get('/item/{item_id}', [ItemController::class, 'detail']);
Route::post('/good', [ItemController::class, 'store']);
Route::middleware('auth')->group(function () {
    Route::post('/comment', [ItemController::class, 'comment']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/mypage/profile/update', [ProfileController::class, 'store']);
    Route::get('/mypage/profile', [ProfileController::class, 'index']);
    Route::get('/verify', function () {
        return redirect()->route('verification.notice'); // => /email/verify
    });
    Route::get('/verify', function () {
        return view('verify-email'); // 認証誘導画面
    });
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/sell', [SellController::class, 'index']);
    Route::post('/exhibiting', [SellController::class, 'store']);
    Route::get('/mypage', [ProfileController::class, 'mypage']);
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index']);
    Route::post('/product/checkout', [PurchaseController::class, 'checkout']);
    // 決済成功時に戻ってくるURL
    Route::get('/payments/success', [PurchaseController::class, 'success'])
        ->name('payments.success');

    // キャンセル時
    Route::get('/payments/cancel', [PurchaseController::class, 'cancel'])
        ->name('payments.cancel');
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'address']);
    Route::post('/purchase/address/update', [PurchaseController::class, 'update']);
});