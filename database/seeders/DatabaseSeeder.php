<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $chefRole = Role::firstOrCreate(['name' => 'chef']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        Permission::firstOrCreate(['name' => 'manage_platform']);
        Permission::firstOrCreate(['name' => 'create_menu']);
        Permission::firstOrCreate(['name' => 'order_food']);

        $adminRole->givePermissionTo('manage_platform');
        $chefRole->givePermissionTo('create_menu');
        $customerRole->givePermissionTo('order_food');

        // 2. Run Taxonomies (NEW)
        $this->call(TaxonomySeeder::class);
        // 3. Run Chef Seeder (NEW)
       // $this->call(ChefSeeder::class);
        // 4. Create Admin User
        $this->call(AdminUserSeeder::class);
    }
}
