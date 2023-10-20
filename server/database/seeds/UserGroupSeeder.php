<?php

use App\Http\Controllers\UserGroup\Models\GroupUser;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groupUserFaker = Faker::create('App\Http\Controllers\UserGroup\Models\GroupUser');
        for ($i = 0; $i <= 100; $i++) {
            $data = [
                'user_id' => $groupUserFaker->numberBetween(1, 50),
                'group_id' => $groupUserFaker->numberBetween(1, 10),
            ];
            $groupUser = new GroupUser($data);
            $groupUser->save();
        }
    }
}
