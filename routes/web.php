<?php

use App\Events\ProcessMessage;
use App\Events\SendMessage;
use App\Http\Controllers\ToastsController;
use App\Jobs\SendEmailJob;
use App\Mail\MessageMailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
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

Route::get('event', function () {

    SendEmailJob::dispatch();
    SendEmailJob::dispatch();
    SendEmailJob::dispatch();
    SendEmailJob::dispatch();
    SendEmailJob::dispatch();
    SendEmailJob::dispatch();
    SendEmailJob::dispatch();
    SendEmailJob::dispatch();

});



Route::get('toasts/ajax', [ToastsController::class, 'ajax']);
