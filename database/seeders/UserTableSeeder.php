<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->first_name = 'System';
        $user->last_name = 'Admin';
        $user->email = 'developers@shakewell.agency';
        $user->password = Hash::make('abc123');
        $user->email_verified_at = Carbon::now();
        $user->processing_notifications = true;
        $user->save();

        $user->assignRole(['superAdmin']);
    }
}
