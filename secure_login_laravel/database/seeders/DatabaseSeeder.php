<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Demo accounts. Run with: php artisan db:seed
     * The usernames and passwords are listed in README.md.
     */
    public function run(): void
    {
        $demoUsers = [
            ['name' => 'System Administrator', 'username' => 'admin',   'email' => 'admin@example.com',   'password' => 'Admin@1234',   'role' => 'admin'],
            ['name' => 'Demo Student',         'username' => 'student', 'email' => 'student@example.com', 'password' => 'Student@1234', 'role' => 'user'],
        ];

        foreach ($demoUsers as $data) {
            // forceFill is used because "role" is not mass-assignable.
            // The "hashed" cast hashes the password before it is saved.
            User::firstOrNew(['username' => $data['username']])->forceFill($data)->save();
        }
    }
}
