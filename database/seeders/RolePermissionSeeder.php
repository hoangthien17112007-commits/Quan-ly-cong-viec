<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ================= PERMISSIONS =================
        $permissions = [

            // USER
            'user.view',
            'user.create',
            'user.update',
            'user.delete',

            // LOCATION
            'location.view',
            'location.create',
            'location.update',
            'location.delete',

            // BOOK
            'book.view',
            'book.create',
            'book.update',
            'book.delete',

            // ROLE
            'role.view',
            'role.create',
            'role.update',
            'role.delete',

            // PERMISSION
            'permission.view',
            'permission.create',
            'permission.update',
            'permission.delete',
            'permission.assign',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ================= ROLES =================
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $staff = Role::firstOrCreate(['name' => 'staff']);

        // ADMIN: full quyền
        $admin->syncPermissions(Permission::all());

        // STAFF: quyền cơ bản
        $staff->syncPermissions([
            'user.view',
            'role.view',
            'permission.view',
            'book.view',
            'location.view',
        ]);
    }
}
