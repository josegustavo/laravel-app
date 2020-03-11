<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Project;
use Faker\Generator as Faker;
use \Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker, $force_params=[]) {

    $result = [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => Crypt::encrypt('dummypassword'),
        'role' => $faker->randomElement(['manager', 'scrum_master', 'developer']),
    ];
    array_merge($result, $force_params);

    return $result;

});

$factory->define(Project::class, function (Faker $faker, $force_params=[]) {

    $result = [
        'code' => $faker->slug,
        'summary' => $faker->text,
        'type' => $faker->randomElement(['type1', 'type2', 'type3', 'type4', 'type5']),
        'name' => $faker->text(50),
    ];

    array_merge($result, $force_params);

    return $result;

});
