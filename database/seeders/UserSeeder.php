<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(100)->create();
        User::factory()->isAdmin()->create(['email' => 'admin@buckhill.co.uk', 'password' => Hash::make('password')]);
    }
}
