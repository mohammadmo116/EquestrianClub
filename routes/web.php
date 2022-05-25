<?php

use App\Http\Controllers\testControler;
use Illuminate\Support\Facades\Route;

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


// Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
// ->middleware(['guest:'.config('fortify.guard')])
// ->name('password.request');

// Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
// ->middleware(['guest:'.config('fortify.guard')])
// ->name('password.reset');
