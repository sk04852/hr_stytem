<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Http\Controllers\Users\Models\User;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    return [
        'first_name' => 'Admin',
        'last_name' => 'Admin',
        'email' => 'admin@crm.com',
        'password' => '$2y$10$hCyMCKGbiBOVb3CzIN6BNuoTcp1P8D9laZa6xMS9TdHXU1DZmLCvu', // password
    ];
});
