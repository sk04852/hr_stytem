<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\Users\Models\UserExtraInfo;

class AdminAccountSeeder extends Seeder
{
    public function run()
    {
        $DefaultUser = new User();
        $DefaultUser->company_id = 1;
        $DefaultUser->first_name = "John";
        $DefaultUser->last_name = "Doe";
        $DefaultUser->username = "admin";
        $DefaultUser->email = "test@email.com";
        $DefaultUser->password = Hash::make(123456);
        $DefaultUser->email_verified = true;
        $DefaultUser->role_id = 1; // System Admin role
        $DefaultUser->status = 'Active';
        $DefaultUser->save();

        $user_extra_info = new UserExtraInfo();
        $user_extra_info->user_id = $DefaultUser->id;
        $user_extra_info->phone_number = 111222333;
        $user_extra_info->save();
    }
}
