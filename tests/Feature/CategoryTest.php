<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\{ Category, Product };
use Illuminate\Foundation\Testing\{ RefreshDatabase, WithFaker };

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testBeginCategoryTests()
    {
        $this->assertTrue(TRUE);
    }
    
    /**
     * Factory works on the Category Class
     */
    public function testCreatesCategory()
    {
        $category = factory(Category::class,1)->create()->first();
        $this->assertDatabaseHas('categories', ['name' => $category->name ]);
    }

    public function testCategoryHasProducts()
    {
        $category = factory(Category::class,1)->create()->first();
        $products = factory(Product::class,3)->create();
        $category->products()->saveMany($products);
        foreach($products as $product) {
            $this->assertTrue($category->products->contains('id', $product->id));
        }
    }

    /**
     * Products linked to the class have tags
     * Category can 
     */
    public function testCategoryFilters()
    {

    }
}
