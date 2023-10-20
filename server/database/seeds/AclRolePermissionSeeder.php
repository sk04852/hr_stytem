<?php

use Illuminate\Database\Seeder;
use App\Models\AclRolePermission;
use Faker\Factory as Faker;

class AclRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Models\AclRolePermission');

        for($i=0 ; $i<=10; $i++) {
        
            $acl_role_permission = new AclRolePermission();
            $acl_role_permission->role_name = $faker->word();
            $acl_role_permission->role_id = $i+1;
            $acl_role_permission->permission_name = $faker->sentence($nbWords = 2, $variableNbWords = true);
            $acl_role_permission->permission_slugn = $faker->sentence($nbWords = 2, $variableNbWords = true);
            $acl_role_permission->save();
        }
    }
}
