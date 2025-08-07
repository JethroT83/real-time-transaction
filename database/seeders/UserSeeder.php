<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->users() as $user)
        {
            User::create($user);
        }

        User::factory(5)->create();
    }

    private function users(): array
    {
        return [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
            ]
        ];
    }

}
