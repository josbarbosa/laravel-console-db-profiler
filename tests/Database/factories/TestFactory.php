<?php

use Faker\Generator as Faker;
use PackageTests\Database\Test;
/*
|--------------------------------------------------------------------------
| Test Factory
|--------------------------------------------------------------------------
*/

$factory->define(Test::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
