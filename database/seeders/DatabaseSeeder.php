<?php

namespace Database\Seeders;

use App\Models\LabOrder;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::updateOrCreate(
            ['email' => 'staff@example.com'],
            ['name' => 'Staff User', 'password' => Hash::make('password'), 'role' => 'staff']
        );

        $viewer = User::updateOrCreate(
            ['email' => 'viewer@example.com'],
            ['name' => 'Viewer User', 'password' => Hash::make('password'), 'role' => 'viewer']
        );

        $staffToken = $staff->createToken('api')->plainTextToken;
        $viewerToken = $viewer->createToken('api')->plainTextToken;

        echo "Staff Token: $staffToken\n";
        echo "Viewer Token: $viewerToken\n";


        LabOrder::factory()->count(5)->create();
    }
}
