<?php

use Faker\Generator as Faker;
use JosBarbosa\ConsoleDbProfiler\Tests\Database\Test;
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
