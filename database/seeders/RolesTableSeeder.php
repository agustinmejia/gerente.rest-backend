<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create roles
        $root = Role::create(['name' => 'root']);
        $owner = Role::create(['name' => 'propietario']);
        $manager = Role::create(['name' => 'gerente']);
        $cashier = Role::create(['name' => 'cajero']);
        $delivery = Role::create(['name' => 'repartidor']);
        $customer = Role::create(['name' => 'cliente']);
        $admin = Role::create(['name' => 'administrador']);
        $soporte = Role::create(['name' => 'soporte']);

        // create permissions

        // Dashboard
        Permission::create(['name' => 'browse dashboard']);

        // Companies
        Permission::create(['name' => 'browse companies']);
        Permission::create(['name' => 'create companies']);
        Permission::create(['name' => 'view companies']);
        Permission::create(['name' => 'edit companies']);
        Permission::create(['name' => 'delete companies']);

        // Users
        Permission::create(['name' => 'browse users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);

        // Roles
        Permission::create(['name' => 'browse roles']);
        Permission::create(['name' => 'create roles']);
        Permission::create(['name' => 'view roles']);
        Permission::create(['name' => 'edit roles']);
        Permission::create(['name' => 'delete roles']);

        // Sync permission
        $permissions = Permission::all();
        $root->syncPermissions($permissions);

        // Asing role
        $user = User::findOrFail(1);
        $user->assignRole('root');
    }
}
