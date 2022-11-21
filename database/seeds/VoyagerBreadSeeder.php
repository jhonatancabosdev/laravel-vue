<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Traits\Seedable;

class VoyagerBreadSeeder extends Seeder
{
    use Seedable;

    /**
     * @var string
     */
    protected $seedersPath = __DIR__.'/breads/';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seed(BrandsBreadTypeAdded::class);
        $this->seed(BrandsBreadRowAdded::class);
        
        $this->seed(ProductsBreadTypeAdded::class);
        $this->seed(ProductsBreadRowAdded::class);

        $this->seed(CategoriesBreadTypeAdded::class);
        $this->seed(CategoriesBreadRowAdded::class);
        
        $this->seed(OrdersBreadTypeAdded::class);
        $this->seed(OrdersBreadRowAdded::class);
    }
}
