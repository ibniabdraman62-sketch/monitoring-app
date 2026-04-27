<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder {
    public function run(): void {
        // Super Admin
        User::firstOrCreate(
            ['email' => 'admin@softseven.ma'],
            [
                'name'     => 'Administrateur',
                'password' => bcrypt('SoftSeven@2026'),
                'role'     => 'super_admin',
            ]
        );
        // Agent
        User::firstOrCreate(
            ['email' => 'agent@softseven.ma'],
            [
                'name'     => 'Agent Monitoring',
                'password' => bcrypt('Agent@2026'),
                'role'     => 'agent',
            ]
        );
    }
}