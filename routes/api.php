<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\APIController;
use App\Http\Controllers\PrintController;

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
    Route::get('company/{id}', [APIController::class, 'my_company']);
    Route::post('company/{id}/update', [APIController::class, 'my_company_update']);
    Route::post('company/{id}/update/images', [APIController::class, 'my_company_update_images']);
    // Branches
    Route::get('company/{id}/branches/list', [APIController::class, 'my_company_branches_list']);
    Route::get('branch/{id}', [APIController::class, 'my_company_branch']);
    Route::post('branch/create', [APIController::class, 'my_company_branch_create']);
    Route::post('branch/{id}/update', [APIController::class, 'my_company_branch_update']);
    Route::get('branch/{id}/delete', [APIController::class, 'my_company_branch_delete']);
    // Products categories
    Route::get('company/{id}/product_category/list', [APIController::class, 'my_company_products_category_list']);
    Route::post('product_category/create', [APIController::class, 'my_company_product_category_create']);
    // Products
    Route::get('company/{id}/products/list', [APIController::class, 'my_company_products_list']);
    Route::get('company/{id}/products/category/list', [APIController::class, 'my_company_products_by_category_list']);
    Route::get('product/{id}', [APIController::class, 'my_company_product']);
    Route::post('product/create', [APIController::class, 'my_company_product_create']);
    Route::post('product/{id}/update', [APIController::class, 'my_company_product_update']);
    Route::get('product/{id}/delete', [APIController::class, 'my_company_product_delete']);
    // Customers
    Route::get('company/{id}/customer/list', [APIController::class, 'my_company_customers_list']);
    Route::post('customer/create', [APIController::class, 'my_company_customer_create']);
    Route::post('customer/{id}/update', [APIController::class, 'my_company_customer_update']);
    // Sales
    Route::get('branch/{id}/sales', [APIController::class, 'my_branch_sales_list']);
    Route::post('sales/create', [APIController::class, 'my_company_sale_create']);
    // Cashiers
    Route::get('company/{id}/cashier/list', [APIController::class, 'my_company_cashiers_list']);
    Route::post('branch/{id}/cashier/create', [APIController::class, 'my_branch_cashier_create']);
    Route::get('branch/{id}/cashier/user/{user_id}', [APIController::class, 'my_branch_cashier_user']);

});

// Route::get('print', [PrintController::class, 'print']);
