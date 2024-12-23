<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Reset cached roles and permissions
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    // Create Tender-related Permissions
    Permission::create(['name' => 'create tender']);
    Permission::create(['name' => 'edit tender']);
    Permission::create(['name' => 'view tender']);
    Permission::create(['name' => 'delete tender']);

    // Create Bid-related Permissions
    Permission::create(['name' => 'create bid']);
    Permission::create(['name' => 'edit bid']);
    Permission::create(['name' => 'view bid']);
    Permission::create(['name' => 'delete bid']);

    // Create Roles and assign permissions
    $adminRole = Role::create(['name' => 'admin']);
    $adminRole->givePermissionTo(Permission::all());

    $managerRole = Role::create(['name' => 'manager']);
    $managerRole->givePermissionTo([
      'create tender', 'edit tender', 'view tender',
      'create bid', 'edit bid', 'view bid'
    ]);

    $userRole = Role::create(['name' => 'user']);
    $userRole->givePermissionTo([
      'create tender', 'view tender',
      'create bid', 'view bid'
    ]);
  }
}
