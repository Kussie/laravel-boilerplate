<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = Permission::all();
        Role::findOrCreate('NovaAccess');

        $admin = Role::findOrCreate('superAdmin');
        $admin->syncPermissions($permissions);

        $Admin = Role::findOrCreate('admin');
        $Admin->syncPermissions($permissions);

        $role = Role::findOrCreate('viewNova');
        $permissions = Permission::wherein(
            'name',
            [
                'View User',
            ]
        )->get();
        $role->syncPermissions($permissions);
    }
}
