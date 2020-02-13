<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\EpisodeDownload;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This is the the model factory definitions of EpisodeDownload events
|
*/

$factory->define(EpisodeDownload::class, function (Faker $faker) {
    return [
        'eventId' => $faker->unique()->word(),
        'episodeId' => $faker->numberBetween($min = 0, $max = 32767),
        'podcastId' => $faker->numberBetween($min = 0, $max = 32767),
        'occurredAt' => $faker->dateTimeBetween($startDate = '-13 days', $endDate = 'now', $timezone = null)
    ];
});
