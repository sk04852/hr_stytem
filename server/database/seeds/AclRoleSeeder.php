<?php

use Illuminate\Database\Seeder;
use App\Models\AclRole;
use Faker\Factory as Faker;

class AclRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Models\AclRole');

        for($i=0 ; $i<=10; $i++) {
        
            $acl_role = new AclRole();
            $acl_role->is_assigned_to_all = random_int(0, 1);
            $acl_role->name = $faker->name();
            $acl_role->slug = $faker->word();
            $acl_role->description =$faker->sentence($nbWords = 6, $variableNbWords = true);
            $acl_role->save();
        }
    }
}
