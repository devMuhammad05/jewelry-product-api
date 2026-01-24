<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'role' => UserRole::Admin,
            'email' => config('admin.email'),
            'password' => 'admin',
        ]);

        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => UserRole::User,
        ]);

        $this->call([
            CategorySeeder::class,
            CollectionSeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
