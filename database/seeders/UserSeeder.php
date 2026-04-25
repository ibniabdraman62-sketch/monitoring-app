<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@softseven.ma'],
            [
                'name'     => 'Administrateur',
                'password' => bcrypt('SoftSeven@2026'),
            ]
        );
    }
}