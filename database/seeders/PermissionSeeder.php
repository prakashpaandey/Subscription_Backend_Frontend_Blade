<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'HOME',
                'slug' => 'home',
                'description' => 'Access dashboard',
            ],
            [
                'name' => 'NOTICE',
                'slug' => 'notice',
                'description' => 'View and create notices',
            ],
            [
                'name' => 'ORDER',
                'slug' => 'order',
                'description' => 'Manage orders',
            ],
            [
                'name' => 'COLLECTION',
                'slug' => 'collection',
                'description' => 'Handle collections',
            ],
            [
                'name' => 'SCHEDULE',
                'slug' => 'schedule',
                'description' => 'Manage schedules',
            ],
            [
                'name' => 'CHAT',
                'slug' => 'chat',
                'description' => 'Access messaging',
            ],
            [
                'name' => 'COMPLAINT',
                'slug' => 'complaint',
                'description' => 'Handle complaints',
            ],
            [
                'name' => 'REPORTS',
                'slug' => 'reports',
                'description' => 'Generate reports',
            ],
            [
                'name' => 'USER',
                'slug' => 'user',
                'description' => 'Manage users',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
