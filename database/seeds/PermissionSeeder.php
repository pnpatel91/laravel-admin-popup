<?php

use App\Role;
use App\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = Permission::defaultPermissions();

        // create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roleAdmin = Role::create(['name' => 'admin']);
        $roleManagement = Role::create(['name' => 'management']);
        $roleStaff = Role::create(['name' => 'staff']);
        $roleAccounting = Role::create(['name' => 'accounting']);

        $roleAdmin->syncPermissions(Permission::all());
        $roleManagement->syncPermissions(Permission::all());
        $roleStaff->syncPermissions(Permission::where('name', 'like', '%company%')->get());
        $roleAccounting->syncPermissions(Permission::all());

    }
}
