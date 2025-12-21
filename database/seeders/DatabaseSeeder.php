<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Roles
        $adminRole = Role::create(['name' => 'admin']);
        $chefRole = Role::create(['name' => 'chef']);
        $customerRole = Role::create(['name' => 'customer']);

        // 2. Create Permissions (Examples)
        Permission::create(['name' => 'manage_platform']);
        Permission::create(['name' => 'create_menu']);
        Permission::create(['name' => 'order_food']);

        // 3. Assign Permissions
        $adminRole->givePermissionTo('manage_platform');
        $chefRole->givePermissionTo('create_menu');
        $customerRole->givePermissionTo('order_food');
    }
}
