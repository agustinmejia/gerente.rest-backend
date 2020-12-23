<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\CompaniesController;

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

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {

    // Users
    Route::resource('users', UsersController::class);
    Route::get('users/list/registers', [UsersController::class, 'list'])->name('users.list.registers');

    // Roles
    Route::resource('roles', RolesController::class);
    Route::get('roles/list/registers', [RolesController::class, 'list'])->name('roles.list.registers');

    // Companies
    Route::resource('companies', CompaniesController::class);
    Route::get('companies/list/registers', [CompaniesController::class, 'list'])->name('companies.list.registers');
    // Branches
    Route::get('companies/{company}/braches', [CompaniesController::class, 'braches'])->name('companies.braches');
    Route::get('companies/{company}/braches/list', [CompaniesController::class, 'braches_list'])->name('companies.braches.list');
    Route::get('companies/{company}/braches/create', [CompaniesController::class, 'braches_create'])->name('companies.braches.create');
    Route::get('companies/{company}/braches/{brach}/view', [CompaniesController::class, 'braches_view'])->name('companies.braches.view');

});

Auth::routes();
