<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\MediaDrive;

class MediaDriveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Models\MediaDrive');
        
        for($i=0 ; $i<=100; $i++) {

            $media_drive = new MediaDrive();
            $media_drive->file_name = $faker->mimeType();
            $media_drive->media_type = 1;
            $media_drive->relation_id = $faker->randomDigit();
            $media_drive->is_default = (bool)random_int(0, 1); 
            $media_drive->media_size = $faker->randomNumber();
            $media_drive->media_height = $faker->randomNumber();
            $media_drive->media_weight = $faker->randomNumber();
            $media_drive->media_title = $faker->text($maxNbChars = 10);
            $media_drive->media_alt_text = $faker->text($maxNbChars = 60);
            $media_drive->media_description = $faker->text($maxNbChars = 150);
            $media_drive->media_mime_type = $faker->text($maxNbChars = 50);
            $media_drive->media_extension = $faker->fileExtension(); 
            $media_drive->media_url = $faker->url(); 
            $media_drive->media_is_image = (bool)random_int(0, 1); 
            $media_drive->thumbnails = $faker->imageUrl(); 
            $media_drive->data =$faker->sentence($nbWords = 6, $variableNbWords = true);
            $media_drive->save();
        
        }
    }
}
