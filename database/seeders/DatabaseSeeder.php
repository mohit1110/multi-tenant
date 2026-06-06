<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::create([
            'name'      => 'Super Admin',
            'email'     => 'admin@test.com',
            'password'  => Hash::make('1234'),
            'role'      => 'super_admin',
            'is_active' => true,
        ]);

        $this->command->info('Super Admin created: admin@test.com / 1234');


        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('Seeding complete!');
        $this->command->info('');
        $this->command->info('Super Admin:');
        $this->command->info('  URL: /admin');
        $this->command->info('  Email: admin@test.com');
        $this->command->info('  Password: 1234');
        $this->command->info('');
        $this->command->info('========================================');
    }
}
