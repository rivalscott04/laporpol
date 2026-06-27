<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Permission;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach (Permission::cases() as $permission) {
            PermissionModel::query()->firstOrCreate([
                'name' => $permission->value,
                'guard_name' => 'web',
            ]);
        }

        foreach (UserRole::cases() as $role) {
            $roleModel = Role::query()->where('name', $role->value)->first();

            if ($roleModel === null) {
                continue;
            }

            $permissionNames = array_map(
                static fn (Permission $permission): string => $permission->value,
                Permission::forRole($role),
            );

            $roleModel->syncPermissions($permissionNames);
        }
    }
}
