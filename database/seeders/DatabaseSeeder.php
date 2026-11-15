<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $admin = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        $user = Role::create([
            'name' => 'User',
            'slug' => 'user',
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role_id' => $admin->id,
            'password' => bcrypt('admin'),
        ]);
        
        // Create 5 users with 'user' role
        User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'role_id' => $user->id,
            'password' => bcrypt('password'),
        ]);
        
        User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'role_id' => $user->id,
            'password' => bcrypt('password'),
        ]);
        
        User::create([
            'name' => 'User Three',
            'email' => 'user3@example.com',
            'role_id' => $user->id,
            'password' => bcrypt('password'),
        ]);
        
        User::create([
            'name' => 'User Four',
            'email' => 'user4@example.com',
            'role_id' => $user->id,
            'password' => bcrypt('password'),
        ]);
        
        User::create([
            'name' => 'User Five',
            'email' => 'user5@example.com',
            'role_id' => $user->id,
            'password' => bcrypt('password'),
        ]);
        
        // Call DeviceSeeder to create devices
        $this->call(DeviceSeeder::class);
    }
}
