<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

// Medels
use App\Models\User;
use App\Models\Role;
use App\Models\ModelHasRole;
use App\Models\Person;
use App\Models\Owner;
use App\Models\Employe;
use App\Models\Company;
use App\Models\CompaniesType;
use App\Models\Branch;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SalesDetail;
use App\Models\Cashier;
use App\Models\CashierDetail;
use App\Models\InventoryHistory;
use App\Models\Subscription;
use App\Models\SubscriptionsType;

class APIController extends Controller
{
    // Auth
    public function login(Request $request){
        $user = null;
        $token = null;

        if($request->social_login){
            if(!$request->social_token){
                return response()->json(['error' => "Error en la petición."]);
            }
            $user = User::where('email', $request->email)->with(['roles', 'owner.person', 'employe.person'])->where('status', 1)->where('deleted_at', NULL)->first();
            if(!$user){
                return response()->json(['error' => "No existe ningún usuario con este email, debes registrarte."]);
            }
            $token = $user->createToken('gerente.rest')->accessToken;

            // Actualizar token de firebase
            if($request->firebase_token){
                User::where('id', $user->id)->update([
                    'firebase_token' => $request->firebase_token
                ]);
            }
        }else{
            $credentials = ['email' => $request->email, 'password' => $request->password];
            if (Auth::attempt($credentials)) {
                $auth = Auth::user();
                $token = $auth->createToken('gerente.rest')->accessToken;
                $user = User::where('id', $auth->id)->with(['roles', 'owner.person', 'employe.person'])->where('status', 1)->where('deleted_at', NULL)->first();
                if($user->roles){
                    if($user->roles[0]->id == 1){
                        return response()->json(['error' => "No tienes permiso para ingresar a nuestra plataforma con estos credenciales."]);
                    }
                }

                // Actualizar token de firebase
                if($request->firebase_token){
                    $user_update = User::findOrFail($user->id);
                    $user_update->firebase_token = $request->firebase_token;
                    $user_update->save();
                }
            }
        }

        if($user && $token){
            // Company info
            $user_company_info = $this->user_company_info($user);
            $company = $user_company_info['company'];
            $branch = $user_company_info['branch'];
            $subscription = $user_company_info['subscription'];
            return response()->json(['user' => $user, 'company' => $company, 'branch' => $branch,'token' => $token, 'subscription' => $subscription, 'social_token' => $request->social_token ?? null]);
        }else{
            return response()->json(['error' => "Usuario o contraseña incorrectos!"]);
        }
    }

    public function register(Request $request){
        if($request->social_register){
            $password = Str::random(10);
        }else{
            $password = $request->password;
        }

        DB::beginTransaction();
        try {
            if(User::where('email', $request->email)->first()){
                return response()->json(['error' => 'El Email ingresado ya existe, intenta con otro!']);
            }

            $user = User::create([
                'name' => $request->firstName,
                'email' => $request->email,
                'avatar' => $request->avatar ?? '../images/user.svg',
                'password' => bcrypt($password)
            ]);
            $user->assignRole('propietario');

            // create person
            $person = Person::create([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'phone' => $request->phone
            ]);

            // Create owner
            $owner = Owner::create([
                'person_id' => $person->id,
                'user_id' => $user->id
            ]);

            $type = CompaniesType::where('status', 1)->where('deleted_at', NULL)->first();

            // Create company
            $company = Company::create([
                'owner_id' => $owner->id,
                'name' => $request->companyName,
                'city_id' => $request->city,
                'companies_type_id' => $type ? $type->id : null
            ]);

            // Create branch
            $branch = Branch::create([
                'company_id' => $company->id,
                'name' => 'Casa matriz',
                'phones' => 'No definido',
                'address' => 'No definido',
                'city_id' => $request->city,
            ]);

            // Create test product
            $category = ProductCategory::where('deleted_at', NULL)->first();
            Product::create([
                'company_id' => $company->id,
                'product_category_id' => $category ? $category->id : NULL,
                'name' => 'Hamburguesa',
                'type' => 'Sencilla',
                'short_description' => 'Producto de prueba, puedes editarlo o eliminarlo.',
                'price' => 12,
            ]);

            // Create free subscription
            $subscriptions_type = SubscriptionsType::where('deleted_at', NULL)->first();
            if($subscriptions_type){
                $days = $subscriptions_type->expiration_days ?? 15;
                $current_date = date('Y-m-d');
                Subscription::create([
                    'user_id' => $user->id,
                    'subscriptions_type_id' => $subscriptions_type->id,
                    'start' => $current_date,
                    'end' => date("Y-m-d", strtotime("$current_date +$days days"))
                ]);
            }

            $user = User::where('id', $user->id)->with(['roles', 'owner.person', 'employe.person'])->first();
            $token = $user->createToken('gerente.rest')->accessToken;

            // Company info
            $user_company_info = $this->user_company_info($user);
            $company = $user_company_info['company'];
            $subscription = $user_company_info['subscription'];

            DB::commit();
            return response()->json(['user' => $user, 'company' => $company, 'branch' => $branch, 'token' => $token, 'subscription' => $subscription]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['error' => 'Ocurrió un error inesperado!']);
        }
    }

    public function user_company_info($user){
        // Get user role
        $role = ModelHasRole::where('model_type' ,'App\Models\User')->where('model_id', $user->id)->first();

        // Get company info
        $company = null;
        $branch = null;
        $subscription = null;
        if($role->role_id == 2){
            $company = Company::with('city')->where('owner_id', $user->owner->id)->where('deleted_at', NULL)->first();
            $branch = Branch::where('company_id', $company->id)->where('status', 1)->where('deleted_at', NULL)->first();
            $subscription = Subscription::with('type')->where('user_id', $user->id)->first();
        }else{
            $employe = Employe::where('id', $user->employe->id)->first();
            if($employe){
                $branch = Branch::findOrFail($employe->branch_id);
                $company = Company::findOrFail($branch->company_id);

                // Obtener el propietario para consultar la suscripción
                $owner = Owner::where('owner_id', $company->owner_id)->first();
                $subscription = Subscription::with('type')->where('user_id', $owner->user_id)->first();
            }
        }
        return ['company' => $company, 'branch' => $branch, 'subscription' => $subscription];
    }

    // Company
    public function my_company($id){
        $company = Company::with(['branches'])->findOrFail($id);
        return response()->json(['company' => $company]);
    }

    public function my_company_update($id, Request $request){
        try {
            $company = Company::findOrFail($id);
            $company->name = $request->name;
            $company->slogan = $request->slogan;
            $company->city_id = $request->city_id;
            $company->phones = $request->phones;
            $company->address = $request->address;
            $company->short_description = $request->short_description;
            $company->save();

            $company_update = Company::with('city')->where('id', $id)->first();
            return response()->json(['company' => $company_update]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Ocurrió un error inesperado!']);
        }
    }

    public function my_company_update_images($id, Request $request){
        $logo = $this->save_image($request->file('logo'), 'companies');
        $banner = $this->save_image($request->file('banner'), 'companies');

        try {
            $company = Company::findOrFail($id);
            if($logo){
                $company->logos = $logo;
            }
            if($banner){
                $company->banners = $banner;
            }
            $company->save();

            $company = Company::findOrFail($company->id);

            return response()->json(['company' => $company]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    // Branches
    public function my_company_branches_list($id){
        try{
            $branches = Branch::where('company_id', $id)->where('deleted_at', NULL)->get();
            return response()->json(['branches' => $branches]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_branch($id){
        $branch = Branch::findOrFail($id);
        return response()->json(['branch' => $branch]);
    }

    public function my_company_branch_create(Request $request){
        DB::beginTransaction();
        try {
            $company = Company::where('owner_id', $request->ownerId)->first();
            $branch = Branch::create([
                'company_id' => $company->id,
                'name' => $request->name,
                'city_id' => $request->city,
                'location' => json_encode($request->location),
                'phones' => $request->phones,
                'address' => $request->address
            ]);

            DB::commit();
            return response()->json(['branch' => $branch]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_branch_update($id, Request $request){
        DB::beginTransaction();
        try {
            $branch = Branch::where('id', $id)->update([
                'name' => $request->name,
                'city_id' => $request->city,
                'location' => json_encode($request->location),
                'phones' => $request->phones,
                'address' => $request->address
            ]);

            DB::commit();
            return response()->json(['branch' => $branch]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_branch_delete($id){
        DB::beginTransaction();
        try {
            $branch = Branch::findOrFail($id);
            $branch->slug = $branch->slug.'_'.$id;
            $branch->deleted_at = Carbon::now();
            $branch->save();

            DB::commit();
            return response()->json(['branch' => $id]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['error' => 'Ocurrió un error inesperado!']);
        }
    }

    // Products categories
    public function my_company_products_category_list($id){
        try{
            $categories = ProductCategory::whereRaw("(company_id = $id or company_id is NULL)")->where('deleted_at', NULL)->orderBy('name')->get();
            return response()->json(['categories' => $categories]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_product_category_create($id, Request $request){
        DB::beginTransaction();
        try {
            $image = $this->save_image($request->file('image'), 'product_category');
            $product_category = ProductCategory::create([
                'company_id' => $id,
                'name' => $request->name,
                'description' => $request->description,
                'image' => $image
            ]);

            DB::commit();
            return response()->json(['product_category' => $product_category]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    // Products
    public function my_company_products_list($id){
        try{
            $products = Product::with(['stock.branch'])->where('company_id', $id)->where('deleted_at', NULL)->get();
            return response()->json(['products' => $products]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_products_by_category_list($id){
        try{
            $products_category = ProductCategory::with(['products' => function ($query) use ($id){
                                    return $query->where('products.company_id',$id)->where('deleted_at', NULL);
                                }, 'products.stock.branch'])->get();
            return response()->json(['products_category' => $products_category]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Error en el servidor.' ]);
        }
    }

    public function my_company_product($id){
        $product = Product::findOrFail($id);
        return response()->json(['product' => $product]);
    }

    public function my_company_product_create($id, Request $request){
        DB::beginTransaction();
        try {
            $company = Company::where('owner_id', $request->owner_id)->first();
            $image = $this->save_image($request->file('image'), 'products');
            $product = Product::create([
                'company_id' => $id,
                'product_category_id' => $request->product_category_id,
                'name' => $request->name,
                'type' => $request->type,
                'short_description' => $request->short_description,
                'price' => $request->price,
                'image' => $image
            ]);

            DB::commit();
            return response()->json(['product' => $product]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_product_inventory_store($id, Request $request){
        try {
            $product_branch = ProductBranch::where('product_id', $id)->where('branch_id', $request->branch_id)->first();
            if($product_branch){
                $product_branch->stock += $request->stock;
                $product_branch->save();
            }else{
                $product_branch = ProductBranch::create([
                    'branch_id' => $request->branch_id,
                    'product_id' => $id,
                    'stock' => $request->stock
                ]);
            }

            InventoryHistory::create([
                'branch_id' => $request->branch_id,
                'user_id' => $request->user_id,
                'product_id' => $id,
                'stock' => $request->stock
            ]);

            $stock = ProductBranch::with(['branch'])->where('product_id', $id)->where('deleted_at', NULL)->get();

            return response()->json([ 'stock' => $stock ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Ocurrió un error inesperado!']);
        }
    }

    public function my_company_product_update($id, Request $request){
        try {
            $image = $this->save_image($request->file('image'), 'products');

            $product = Product::findOrFail($id);
            $product->name = $request->name;
            $product->type = $request->type;
            $product->price = $request->price;
            $product->product_category_id = $request->product_category_id;
            $product->short_description = $request->short_description;
            if($image){
                $product->image = $image;
            }
            $product->save();
            return response()->json(['product' => $product]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Ocurrió un error inesperado!']);
        }
    }

    public function my_company_product_delete($id){
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);
            $product->slug = $product->slug.'_'.$id;
            $product->deleted_at = Carbon::now();
            $product->save();

            DB::commit();
            return response()->json(['product_id' => $id]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['error' => 'Ocurrió un error inesperado!']);
        }
    }

    // Customers
    public function my_company_customers_list($id){
        try{
            $customers = Customer::with(['person', 'company'])
                            ->whereRaw("(company_id = $id or company_id is NULL)")
                            ->where('id', '>', 1)->where('deleted_at', NULL)->get();
            return response()->json(['customers' => $customers]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_customer_create(Request $request){
        DB::beginTransaction();
        try {
            $company = Company::where('owner_id', $request->owner_id)->first();

            $person = Person::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'ci_nit' => $request->ci_nit,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            $customer = Customer::create([
                'person_id' => $person->id,
                'company_id' => $request->company_id
            ]);

            $customer = Customer::with(['person', 'company'])->where('id', $customer->id)->first();

            DB::commit();
            return response()->json(['customer' => $customer]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_customer_update($id, Request $request){
        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($id);
            $person = Person::where('id', $customer->person_id)->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'ci_nit' => $request->ci_nit,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            $customer = Customer::with(['person', 'company'])->where('id', $id)->first();

            DB::commit();
            return response()->json(['customer' => $customer]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    // Sales
    public function my_branch_sales_list($id, $user_id){
        try{
            $sales = Sale::with(['customer.person', 'status', 'details.product'])
                            ->where('branch_id', $id)->where('user_id', $user_id)->where('deleted_at', NULL)
                            ->whereDate('created_at', Carbon::now())->orderBY('id', 'DESC')->get();
            return response()->json(['sales' => $sales]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_branch_sales_kitchen_list($id){
        try{
            $sales = Sale::with(['details.product', 'status'])
                            ->where('branch_id', $id)->where('sales_status_id', 2)->where('deleted_at', NULL)
                            ->whereDate('created_at', date('Y-m-d'))->orderBY('id', 'ASC')->get();
            return response()->json(['sales' => $sales]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_branch_sales_tickets_list($id){
        try{
            $sales = Sale::with(['status'])
                            ->where('branch_id', $id)->whereRaw("(sales_status_id = 2 or sales_status_id = 3)")->where('deleted_at', NULL)
                            ->whereDate('created_at', Carbon::now())->orderBY('id', 'ASC')->orderBY('sales_status_id', 'DESC')->get();
            return response()->json(['sales' => $sales]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_sale_create(Request $request){
        DB::beginTransaction();
        try {

            $total = $request->total ?? 0;
            $discount = $request->discount ?? 0;
            $amount_received = $request->amount_received ?? 0;
            $paid_out = ($amount_received >= ($total - $discount)) ? 1 : 0;

            $sale = Sale::create([
                'branch_id' => $request->branch_id,
                'customer_id' => $request->customer_id,
                'user_id' => $request->user_id,
                'cashier_id' => $request->cashier_id,
                'sale_number' => $this->countSalesPerDay($request->branch_id) +1,
                'payment_type' => $request->payment_type,
                'sale_type' => $request->sale_type,
                'sales_status_id' => $request->sales_status_id,
                'total' => $total,
                'discount' => $discount,
                'paid_out' => $paid_out,
                'table_number' => $request->table_number,
                'amount_received' => $amount_received,
                'observations' => $request->observations
            ]);

            // Create sale details
            foreach ($request->sale_details as $item) {
                // Si el producto está registrado en almacen se disminuye su stock
                $product_branches = ProductBranch::where('product_id', $item['id'])->where('branch_id', $request->branch_id)->first();
                $decrement_stock = 0;
                if($product_branches){
                    $product = ProductBranch::find($product_branches->id);
                    // Si el stock es mayor a la compra, decrementamos la compra
                    if($product->stock >= $item['quantity']){
                        $decrement_stock = $item['quantity'];
                    }else{
                        // si la compra es mayor al stock, decrementamos el stock
                        $decrement_stock = $product->stock;
                    }
                    $product->stock -= $decrement_stock;
                    $product->save();
                }

                SalesDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'quantity_decrement' => $decrement_stock
                ]);
            }

            // Si el pago es en efectivo se registra en caja
            if($request->payment_type == 1){
                // Create cashier detail
                CashierDetail::create([
                    'cashier_id' => $request->cashier_id,
                    'user_id' => $request->user_id,
                    'amount' => ($total - $discount),
                    'description' => 'Venta realizada COD:'.$sale->id,
                    'type' => 1,
                    'sale_id' => $sale->id
                ]);
            }

            DB::commit();
            return response()->json(['sale' => $sale]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_sale($id){
        try {
            $sale = Sale::with(['customer.person', 'details.product', 'branch.city', 'status', 'employe'])
                            ->where('id', $id)->first();
            return response()->json(['sale' => $sale]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Ocurrió un error desconocido']);
        }
    }

    public function my_company_sale_update_status($id, Request $request){
        try{
            Sale::where('id', $id)->update([
                'sales_status_id' => $request->sales_status_id
            ]);
            return response()->json(['sale' => $id]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_sale_delete($id){
        DB::beginTransaction();
        try {
            // Elimianr venta
            $sale = Sale::findOrFail($id);
            $sale->deleted_at = Carbon::now();
            $sale->save();

            // Eliminar detalle de caja
            SalesDetail::where('sale_id', $id)->update([
                'deleted_at' => Carbon::now()
            ]);

            // Eliminar asiento en caja
            CashierDetail::where('sale_id', $id)->update([
                'deleted_at' => Carbon::now()
            ]);

            // Devolver stock de productos
            $sales_details = SalesDetail::where('sale_id', $id)->get();
            foreach ($sales_details as $item) {
                if($item->quantity_decrement > 0){
                    ProductBranch::where('branch_id', $sale->branch_id)->where('product_id', $item->product_id)
                    ->increment('stock', $item->quantity_decrement);
                }
            }

            DB::commit();
            return response()->json(['sale_id' => $id]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['error' => 'Ocurrió un error inesperado!']);
        }
    }

    // Cashiers
    public function my_company_cashiers_list($id){
        try{
            $branches = [];
            // Obtener todas las sucursales del restaurante
            foreach (Branch::where('company_id', $id)->get('id') as $item) {
                array_push($branches, $item->id);
            }
            $cashiers = Cashier::with(['user', 'branch', 'details'])->whereIn('branch_id', $branches)
                        ->where('deleted_at', NULL)->orderBY('id', 'DESC')->get();
            return response()->json(['cashiers' => $cashiers]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_cashier($id){
        try{
            $cashier = Cashier::with(['user', 'branch', 'details'])->where('id', $id)->first();
            return response()->json(['cashier' => $cashier]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_cashier_detail_store($id, Request $request){

        // Si es un egreso verificar que sea menor al monto en caja
        if($request->type == 2){
            $cashier = Cashier::with(['details' => function($q){
                $q->where('deleted_at', NULL);
            }])->where('id', $id)->where('deleted_at', NULL)->first();
            $total = $cashier->opening_amount;

            // Recorrer todos los ingresos y sumarlos al monto de apertura
            foreach ($cashier->details as $value) {
                if($value->type == 1){
                    $total += $value->amount;
                }else{
                    $total -= $value->amount;
                }
            }

            if($total < $request->amount){
                return response()->json([ 'error' => 'El monto de egreso excede el monto de dinero en caja.']);
            }
        }

        try{
            CashierDetail::create([
                'cashier_id' => $id,
                'user_id' => $request->user_id,
                'description' => $request->description,
                'amount' => $request->amount,
                'type' => $request->type
            ]);
            $cashier = Cashier::with(['user', 'branch', 'details'])->where('id', $id)->first();
            return response()->json(['cashier' => $cashier]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error en nuestro servidor, intente nuevamente!' ]);
        }
    }

    public function my_company_cashier_close($id, Request $request){
        try{
            $cashier = Cashier::where('id', $id)->update([
                'closing_amount' => $request->closing_amount,
                'missing_amount' => $request->missing_amount,
                'observations' => $request->observations,
                'real_amount' => $request->real_amount,
                'status' => 2,
                'closing' => Carbon::now()
            ]);
            return response()->json(['cashier' => $cashier]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_cashier_delete($id){
        try {
            Cashier::where('id', $id)->update([
                'observations' => 'Eliminada',
                'deleted_at' => Carbon::now()
            ]);
            return response()->json(['cashier_id' => $id]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_branch_cashier_create($id, Request $request){
        try{
            $cashier = Cashier::create([
                'branch_id' => $id,
                'user_id' => $request->user_id,
                'name' => $request->name,
                'opening' => Carbon::now(),
                'opening_amount' => $request->opening_amount
            ]);
            return response()->json(['cashier' => $cashier]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_branch_cashier_user($id, $user_id){
        try{
            $cashier = Cashier::where('branch_id', $id)->where('user_id', $user_id)
                        ->where('status', 1)->where('deleted_at', NULL)->first();
            return response()->json(['cashier' => $cashier]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    // Employes
    public function my_company_employes_list($id){
        try{
            $employes = Employe::with(['person', 'user.roles', 'branch'])->where('branch_id', $id)
                        ->where('deleted_at', NULL)->orderBY('id', 'DESC')->get();
            return response()->json(['employes' => $employes]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_employe($id){
        try{
            $employe = Employe::with(['person', 'user.roles', 'branch'])->where('id', $id)->first();
            return response()->json(['employe' => $employe]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => 'Ocurrió un error al cargar los datos del empleado.' ]);
        }
    }

    public function my_company_employes_create($id, Request $request){
        DB::beginTransaction();
        try {

            if(User::where('email', $request->email)->first()){
                return response()->json(['error' => 'El Email ingresado ya existe, intenta con otro!']);
            }

            $image = $this->save_image($request->file('image'), 'employes');
            $user = User::create([
                'name' => $request->first_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'avatar' => $image ?? '../images/user.svg'
            ]);

            $role = Role::find($request->role_id);
            $user->assignRole($role->name);

            // create person
            $person = Person::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'ci_nit' => $request->ci,
                'phone' => $request->phone,
                'address' => $request->address
            ]);

            $employe = Employe::create([
                'person_id' => $person->id,
                'user_id' => $user->id,
                'branch_id' => $request->branch_id
            ]);

            DB::commit();
            return response()->json(['employe' => $employe]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_employes_update($id, Request $request){
        DB::beginTransaction();
        try {

            $employe = Employe::with(['person', 'user'])->where('id', $id)->first();

            if(User::where('email', $request->email)->where('id', '<>', $employe->user->id)->first()){
                return response()->json(['error' => 'El Email ingresado ya existe, intenta con otro!']);
            }

            $image = $this->save_image($request->file('image'), 'employes');
            $user = User::find($employe->user->id);
            $user->name = $request->first_name;
            $user->email = $request->email;
            if($request->password){
                $user->password = bcrypt($request->password);
            }
            if($image){
                $user->avatar = $image;
            }
            $user->save();

            ModelHasRole::where('model_type', 'App\Models\User')->where('model_id', $employe->user->id)->delete();

            $role = Role::find($request->role_id);
            $user->assignRole($role->name);

            // create person
            $person = Person::where('id', $employe->person->id)->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'ci_nit' => $request->ci,
                'phone' => $request->phone,
                'address' => $request->address
            ]);

            $employe = Employe::where('id', $employe->id)->update([
                'branch_id' => $request->branch_id
            ]);

            DB::commit();
            return response()->json(['employe' => $employe]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    public function my_company_employes_delete($id){
        DB::beginTransaction();
        try {
            $employe = Employe::with(['person', 'user'])->where('id', $id)->first();

            User::where('id', $employe->user->id)->update([
                'deleted_at' => Carbon::now()
            ]);

            Person::where('id', $employe->person->id)->update([
                'deleted_at' => Carbon::now()
            ]);

            Employe::where('id', $employe->id)->update([
                'status' => 0,
                'deleted_at' => Carbon::now()
            ]);

            DB::commit();
            return response()->json(['employe' => $employe]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    // Configuration
    public function profile_update($id, Request $request){
        DB::beginTransaction();
        try {
            if(User::where('email', $request->email)->where('id', '<>', $id)->first()){
                return response()->json(['error' => 'El Email ingresado ya existe, intenta con otro!']);
            }

            $image = $this->save_image($request->file('image'), 'employes');
            $user = User::find($id);
            $user->name = $request->first_name;
            $user->email = $request->email;
            if($request->password){
                $user->password = bcrypt($request->password);
            }
            if($image){
                $user->avatar = $image;
            }
            $user->save();

            // update person
            $role = ModelHasRole::where('model_type' ,'App\Models\User')->where('model_id', $id)->first();
            if($role->role_id == 2){
                $profile = Owner::with(['person'])->where('user_id', $id)->first();
            }else{
                $profile = Employe::with(['person'])->where('user_id', $id)->first();
            }
            $person = Person::where('id', $profile->person->id)->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'ci_nit' => $request->ci,
                'phone' => $request->phone,
                'address' => $request->address
            ]);

            $user = User::where('id', $id)->with(['roles', 'owner.person', 'employe.person'])->where('status', 1)->where('deleted_at', NULL)->first();

            DB::commit();
            return response()->json(['profile' => $user]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    // Reports
    public function get_metrics($company_id, $user_id){
        $cash = 0;
        $count_sales = 0;
        $count_products = Product::where('company_id', $company_id)->where('deleted_at', NULL)->count();
        $count_customer = Customer::where('company_id', $company_id)->count();
        $best_seller = Product::with(['sales'])->where('deleted_at', NULL)->where('company_id', $company_id)->limit(5)->get();

        // Mostrar monto en caja dependiendo si es administrador o empleado
        $role = ModelHasRole::where('model_type' ,'App\Models\User')->where('model_id', $user_id)->first();
        if($role->role_id == 2){
            $sales = Company::with(['branches.sales' => function($query){
                $query->whereDate('created_at', date('Y-m-d'));
            }, 'branches.sales.cashier'])->where('id', $company_id)->first();

        }else{
            $sales = Company::with(['branches.sales' => function($query) use($user_id){
                $query->whereDate('created_at', date('Y-m-d'))->where('user_id', $user_id);
            }, 'branches.sales.cashier'])->where('id', $company_id)->first();
        }
        // Obtener contador y sumatoria de ventas
        foreach ($sales->branches as $branche) {
            foreach ($branche->sales as $sale) {
                if($sale->cashier->status == 1 && $sale->deleted_at == NULL){
                    $cash += $sale->total - $sale->discount;
                    $count_sales++;
                }
            }
        }

        // Obtener Ultimos 7 dias de ventas
        $branches = [];
        // Obtener todas las sucursales del restaurante
        foreach (Branch::where('company_id', $company_id)->get('id') as $item) {
            array_push($branches, $item->id);
        }
        $current_sales = Sale::whereIn('branch_id', $branches)->where('deleted_at', NULL)
                                ->groupBy(DB::raw('Date(created_at)'))->orderBy('created_at')->limit(7)
                                ->get(array(
                                    DB::raw('Date(created_at) as date'),
                                    DB::raw('SUM(total - discount) as total'),
                                    DB::raw('deleted_at as gasto')
                                ));

        $cont = 0;
        foreach ($current_sales as $sale) {
            $gastos = CashierDetail::with(['cashier.branch' => function($query) use($company_id){
                $query->where('company_id', $company_id);
            }])->whereDate('created_at', $sale->date)->where('type', 2)->first(DB::raw('SUM(amount) as total'));
            $current_sales[$cont]->gasto = $gastos['total'] ?? 0;
            $cont++;
        }

        return response()->json([ 'cash' => $cash, 'count_sales' => $count_sales, 'count_products' => $count_products, 'count_customer' =>  $count_customer, 'current_sales' => $current_sales, 'best_seller' => $best_seller ]);
    }

    public function report_sales($id, Request $request){
        $query_date = 1;
        $date_report = [];
        switch ($request->type) {
            case 'day':
                $query_date = "CONCAT(YEAR(created_at),'-',MONTH(created_at),'-',DAY(created_at)) = '".date('Y-n-j', strtotime($request->date))."'";
                $date_report = ['date' => $request->date];
                break;
            case 'month':
                $query_date = "(YEAR(created_at) = '$request->year' and MONTH(created_at) = '$request->month')";
                $date_report = ['date' => $request->year.'-'.$request->month.'-01'];
                break;
            case 'range':
                $query_date = "( CONCAT(YEAR(created_at),'-',MONTH(created_at),'-',DAY(created_at)) >= '".date('Y-n-j', strtotime($request->start))."' and CONCAT(YEAR(created_at),'-',MONTH(created_at),'-',DAY(created_at)) <= '".date('Y-n-j', strtotime($request->finish))."' )";
                $date_report = ['start' => $request->start, 'finish' => $request->finish];
                break;
        }

        $group = $request->group;
        $branches = [];
        $discount = 0;
        // Obtener todas las sucursales del restaurante
        foreach (Branch::where('company_id', $id)->get('id') as $item) {
            array_push($branches, $item->id);
        }
        if($group == 'sales'){
            // Obtener reporte de ventas detallado
            $report = Sale::with(['details.product', 'customer.person', 'employe'])
                        ->whereIn('branch_id', $branches)->where('deleted_at', NULL)
                        ->whereRaw($query_date)->get();
        }else{
            // Obtener reporte de ventas agrupado por productos
            $report = Product::with(['sales' => function($q) use($query_date) {
                $q->where('deleted_at', NULL)->whereRaw($query_date);
            }])->where('company_id', $id)->get();

            // Obtener el dato auxiliar de el total de descuentos, debido a que puede que el descuento se repita
            // debido a la forma de agrupar la consulta
            $sales = Sale::whereIn('branch_id', $branches)->where('deleted_at', NULL)->whereRaw($query_date)->get();
            foreach ($sales as $item) {
                $discount += $item->discount;
            }
        }
        return response()->json([ 'report' => $report, 'group' => $group, 'discount' => $discount, 'date' => $date_report, 'query_date' => $query_date ]);
    }
}
