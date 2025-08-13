<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DCCategoryController;

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
return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});
Route::get('/register', function () {
    return view('register');
});
Route::get('/categories', [DCCategoryController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
Route::get('/categories/{id}', [DcCategoryController::class, 'show']);
});

require __DIR__.'/auth.php';
