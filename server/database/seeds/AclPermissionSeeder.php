<?php

use Illuminate\Database\Seeder;
use App\Models\AclPermission;
use Faker\Factory as Faker;

class AclPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Models\AclPermission');

        for($i=0 ; $i<=10; $i++) {
        
            $acl_permission = new AclPermission();
            $acl_permission->name = $faker->name();
            $acl_permission->slug = $faker->word();
            $acl_permission->description = $faker->sentence($nbWords = 6, $variableNbWords = true);
            $acl_permission->save();
        }
            
    }
}
