<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view suggestions', 'edit suggestions', 'delete suggestions',
            'view services',    'edit services',    'delete services',
            'view agences',     'edit agences',     'delete agences',
            'view users',       'edit users',       'delete users',
            'view settings',    'edit settings',
            'view logs',
            'export excel',     'export pdf',
            'view qrcode',      'download qrcode',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'view suggestions', 'edit suggestions',
            'view services',    'edit services',
            'view agences',
            'view users',
            'view settings',
            'view logs',
            'export excel', 'export pdf',
            'view qrcode', 'download qrcode',
        ]);

        $responsable = Role::firstOrCreate(['name' => 'responsable_service', 'guard_name' => 'web']);
        $responsable->syncPermissions([
            'view suggestions', 'edit suggestions',
            'view services',
            'export excel',
        ]);
    }
}
