<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// Medels
use App\Models\User;
use App\Models\Role;
use App\Models\ModelHasRole;
use App\Models\Person;
use App\Models\Owner;
use App\Models\Employe;
use App\Models\Company;
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

class APIController extends Controller
{
    // Auth
    public function login(Request $request){
        $user = null;
        $token = null;

        if($request->social_login){
            $user = User::where('email', $request->email)->with(['roles', 'suscription'])->where('status', 1)->where('deleted_at', NULL)->first() ?? $this->new_owner($request);
            $token = $user->createToken('appxiapi')->accessToken;

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
                $user = User::where('id', $auth->id)->with(['roles', 'suscription'])->where('status', 1)->where('deleted_at', NULL)->first();
                
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
            return response()->json(['user' => $user, 'company' => $company, 'branch' => $branch,'token' => $token]);
        }else{
            return response()->json(['error' => "Usuario o contraseña incorrectos!"]);
        }
    }

    public function register(Request $request){
        DB::beginTransaction();
        try {
            if(User::where('email', $request->email)->first()){
                return response()->json(['error' => 'El Email ingresado ya existe, intenta con otro!']);
            }

            $user = User::create([
                'name' => $request->firstName,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
            $user->assignRole('owner');

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

            // Create company
            $company = Company::create([
                'owner_id' => $owner->id,
                'name' => $request->companyName,
                'city_id' => $request->city,
            ]);

            $branch = Branch::create([
                'company_id' => $company->id,
                'name' => 'Casa matriz',
                'city_id' => $request->city,
            ]);

            $user = User::where('id', $user->id)->with(['roles'])->first();
            $token = $user->createToken('gerente.rest')->accessToken;

            // Company info
            $user_company_info = $this->user_company_info($user);
            $company = $user_company_info['company'];

            DB::commit();
            return response()->json(['user' => $user, 'company' => $company, 'branch' => $branch, 'token' => $token]);
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
        if($role->role_id == 2){
            $company = Company::where('owner_id', $user->owner->id)->where('deleted_at', NULL)->first();
            $branch = Branch::where('company_id', $company->id)->where('status', 1)->where('deleted_at', NULL)->first();
        }else{
            $employe = Employe::where('user_id', $user->id)->first();
            if($employe){
                $branch = Branch::findOrFail($employe->branch_id);
                $company = Company::findOrFail($branch->company_id);
            }
        }
        return ['company' => $company, 'branch' => $branch];
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
            return response()->json(['company' => $company]);
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
                'location' => $request->location,
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
                'location' => $request->location,
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
            $products_category = ProductCategory::with(['products.stock.branch' => function ($query) use ($id){
                                    return $query->where('company_id',$id)->where('deleted_at', NULL);
                                }])
                                ->whereRaw("(company_id = $id or company_id is NULL)")->get();
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
                'person_id' => $person->id
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
    public function my_branch_sales_list($id){
        try{
            $sales = Sale::with(['customer.person'])
                            ->where('branch_id', $id)->where('deleted_at', NULL)
                            ->whereDate('created_at', Carbon::now())->orderBY('id', 'DESC')->get();
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
                'total' => $total,
                'discount' => $discount,
                'paid_out' => $paid_out,
                'table_number' => $request->table_number,
                'amount_received' => $amount_received,
                'observations' => $request->observations
            ]);

            // Create sale details
            foreach ($request->sale_details as $item) {
                SalesDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);
            }

            // Create cashier detail
            CashierDetail::create([
                'cashier_id' => $request->cashier_id,
                'user_id' => $request->user_id,
                'amount' => ($total - $discount),
                'description' => 'Venta realizada COD:'.$sale->id,
                'type' => 1,
                'sale_id' => $sale->id
            ]);

            DB::commit();
            return response()->json(['sale' => $sale]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([ 'error' => 'Ocurrió un error inesperado!' ]);
        }
    }

    // Cashiers
    public function my_company_cashiers_list($id){
        try{
            $cashiers = Cashier::with(['user', 'branch', 'details'])->where('branch_id', $id)
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
            $cashier = Cashier::with(['details'])->where('id', $id)->first();
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
                'avatar' => $image
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
}
