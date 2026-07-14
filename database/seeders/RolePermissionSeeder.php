<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'beneficiaries.create',
            'beneficiaries.read',
            'beneficiaries.update',
            'beneficiaries.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        $operator = Role::firstOrCreate(['name' => 'Operator', 'guard_name' => 'web']);
        $operator->syncPermissions(['beneficiaries.create', 'beneficiaries.read', 'beneficiaries.update']);

        $viewer = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions(['beneficiaries.read']);

        // Create admin user
        $user = User::firstOrCreate(
            ['email' => 'admin@rockylinux.local'],
            [
                'name' => 'Rocky Linux',
                'password' => 'rockly',
            ]
        );

        $user->assignRole('Admin');
    }
}
