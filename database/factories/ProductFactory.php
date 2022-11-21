<?php

use App\Product;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;

$factory->define(Product::class, function (Faker $faker) {

    // product sku
    $sku = $faker->bothify('?????-###-??');

    $name = implode(' ', $faker->words(mt_rand(1, 13)));

    $cost = $faker->numberBetween(1, 3000);
    $price = $cost * 1.35;

    $clicks = $faker->numberBetween(0, 1000);

    $sales = $clicks * .34;

    $descriptionFirstParagraph = $faker->paragraph(mt_rand(1,5));
    $descriptionInternalHeadline = '<h3>' . $faker->sentence(mt_rand(2,5)) . '</h3>';
    $descriptionEnd = '<p>' . implode('</p>', $faker->paragraphs($nb = mt_rand(1,4), $asText = false)) . '</p>';

    $description = $descriptionFirstParagraph . $descriptionInternalHeadline . $descriptionEnd;

    $skuPlus = $sku . "-" . mt_rand(100,999);

    return [
        'name'              => $name,
        'slug'              => Str::slug(substr($name, 40)) . '-' . $sku,
        'cost'              => $cost,
        'price'             => $price,
        'status'            => 1,
        'quantity'          => $faker->numberBetween(0, 100),
        'sku'               => $skuPlus,
        'upc'               => $sku . '-UPC',
        'description'       => $description,
        'item_features'     => $faker->text,
        'short_description' => $faker->text,
        'quantity_per_case' => $faker->numberBetween(1, 100),
        'clicks_counter'    => $clicks,
        'sales_count'       => $sales,
        'homepage_order'           => $faker->boolean,
        'weight_uom'        => "lbs",
        'height_uom'        => "in",
        'width_uom'         => "in",
        'length_uom'        => "in",
        'weight'            => $faker->randomFloat(2, 0.5, 300),
        'height'            => $faker->randomFloat(2, 8, 80),
        'width'             => $faker->randomFloat(2, 8, 64),
        'length'            => $faker->randomFloat(2, 9, 74),
    ];
});