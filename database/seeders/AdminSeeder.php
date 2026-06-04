<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'vip@gmail.com'],
            [
                'name' => 'Vip',
                'password' => Hash::make('123456'),
                'role' => 'vip',
                'balance' => 10000000,
            ]
        );

        $this->command->info('Roles and Admin seeded successfully!');
    }
}
