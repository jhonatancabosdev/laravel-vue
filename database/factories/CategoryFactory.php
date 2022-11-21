<?php

use App\Category;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;

$factory->define(Category::class, function (Faker $faker) {

    $name = implode(' ', $faker->words(mt_rand(1, 3)));

    return [
        'name' => $name,
        'slug' => Str::slug($name),
        'cover' => UploadedFile::fake()->image('category.png', 600, 600)->store('categories', ['disk' => 'public']),
        'description' => $faker->paragraph,
        'marketing_description' => $faker->text,
        'on_filter' => mt_rand(0, 1),
        'on_navbar' => 1,
        'homepage_order' => NULL,
        'status' => true
    ];
});
