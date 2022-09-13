<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

         //Roles
        $admin = Role::create(['name' => 'admin']);
        $owner = Role::create(['name' => 'owner']);
        $driver = Role::create(['name' => 'driver']);
        $client = Role::create(['name' => 'client']);
        $staff = Role::create(['name' => 'staff']);
        $kitchen = Role::create(['name' => 'kitchen']);
        $manager_restorant = Role::create(['name' => 'manager_restorant']);

        //Permissions
        $admin->givePermissionTo(Permission::create(['name' => 'manage restorants']));
        $admin->givePermissionTo(Permission::create(['name' => 'manage drivers']));
        $admin->givePermissionTo(Permission::create(['name' => 'manage orders']));
        $admin->givePermissionTo(Permission::create(['name' => 'edit settings']));

        Permission::create(['name' => 'view orders'])->syncRoles([$owner, $manager_restorant]);
        Permission::create(['name' => 'edit restorant'])->syncRoles([$owner, $manager_restorant]);

        $driver->givePermissionTo(Permission::create(['name' => 'edit orders']));

        $backedn = Permission::create(['name' => 'access backedn']);
        $admin->givePermissionTo($backedn);
        $owner->givePermissionTo($backedn);
        $driver->givePermissionTo($backedn);

        //ADD ADMIN USER ROLE
        DB::table('model_has_roles')->insert([
            'role_id' => 1,
            'model_type' =>  \App\User::class,
            'model_id'=> 1,
        ]);
    }
}
