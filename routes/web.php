<?php

use App\Http\Controllers\ToastsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

require('auth.php');
require('verify_routes.php');
require('forgot_password.php');

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('toasts/ajax', [ToastsController::class, 'ajax']);
