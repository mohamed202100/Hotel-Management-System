<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Core Roles (if they don't exist)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $guestRole = Role::firstOrCreate(['name' => 'guest']);

        // Optional: Assign permissions if you had specific ones, but for now, we only need the roles.

        // 2. Create permissions for demonstration (Example)
        Permission::firstOrCreate(['name' => 'manage rooms']);
        Permission::firstOrCreate(['name' => 'manage customers']);
        Permission::firstOrCreate(['name' => 'manage all reservations']);

        // 3. Assign all permissions to Admin
        $adminRole->givePermissionTo(Permission::all());
    }
}
