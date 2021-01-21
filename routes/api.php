<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\APIController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('auth/login', [ApiController::class, 'login']);
Route::post('auth/register', [APIController::class, 'register']);
Route::get('cities/list/registers', [CitiesController::class, 'list']);

Route::group(['middleware' => ['auth:api']], function(){
	// Companies
    Route::get('company/owner/{id}', [APIController::class, 'my_company']);
    Route::post('company/owner/{id}/update', [APIController::class, 'my_company_update']);
    Route::post('company/owner/{id}/update/images', [APIController::class, 'my_company_update_images']);
    // Branches
    Route::get('company/owner/{id}/branch/list', [APIController::class, 'my_company_branch_list']);
    Route::get('branch/{id}', [APIController::class, 'my_company_branch']);
    Route::post('branch/create', [APIController::class, 'my_company_branch_create']);
    Route::post('branch/{id}/update', [APIController::class, 'my_company_branch_update']);
    Route::get('branch/{id}/delete', [APIController::class, 'my_company_branch_delete']);
});