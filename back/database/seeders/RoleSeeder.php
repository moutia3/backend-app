<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RoleSeeder extends Seeder
{
    
  
public function run()
{
  
    $adminRole = Role::create(['name' => 'admin']);
    $managerRole = Role::create(['name' => 'manager']);
    $employeeRole = Role::create(['name' => 'employee']);

   
    $permission1 = Permission::create(['name' => 'create-posts']);
    $permission2 = Permission::create(['name' => 'edit-posts']);

    
    $adminRole->givePermissionTo([$permission1, $permission2]);
    $managerRole->givePermissionTo($permission1);
}
}
