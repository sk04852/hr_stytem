<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\MediaDriveRelation;

class MediaDriveRelationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Models\MediaDriveRelation');
        
        for($i=0 ; $i<=100; $i++) {
            $media_drive_relation = new MediaDriveRelation();
            $media_drive_relation->media_id = $i+1;
            $media_drive_relation->media_type_id = 1;
            $media_drive_relation->relation_id = $faker->randomNumber();
            $media_drive_relation->save();
        }
    }
}
