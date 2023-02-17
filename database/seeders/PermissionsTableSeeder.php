<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::findOrCreate('View User');
        Permission::findOrCreate('View Any User');
        Permission::findOrCreate('Create User');
        Permission::findOrCreate('Update User');
        Permission::findOrCreate('Delete User');

        Permission::findOrCreate('View User Activity Log');
    }
}
