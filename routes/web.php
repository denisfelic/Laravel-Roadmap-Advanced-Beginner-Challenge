<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
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


Route::prefix('/')->middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return  view('dashboard');
    })->name('dashboard');

    Route::resource('/client', ClientController::class);
    Route::post('users/role/{user}', [UserController::class, 'change_role'])->name('users.change_role');
    Route::resource('/users', UserController::class);
    Route::resource('project', ProjectController::class);
});



require __DIR__ . '/auth.php';
