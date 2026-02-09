<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Set user with ID 1 as Admin for testing/initial setup
        $admin = User::find(1);
        if ($admin) {
            $admin->update(['role' => 'Admin']);
            $this->command->info('User ID 1 set as Admin.');
        } else {
            $this->command->warn('User ID 1 not found.');
        }
    }
}
