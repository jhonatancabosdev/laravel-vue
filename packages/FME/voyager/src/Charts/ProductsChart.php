<?php

namespace TCG\Voyager\Charts;

use App\Order;
use App\OrderProduct;
use TCG\Voyager\Helpers\ColorHash;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class ProductsChart extends Chart
{
    /**
     * @var Collection
     */
    protected $productsData;

    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->productsData = collect();
    }

    /**
     * @return Collection
     */
    public function getProductsData()
    {
        return $this->productsData;
    }

    public function setData()
    {
        $data = collect([]);

        $labels = collect();

        for ($i = 15; $i > -1; $i--) {
            $products = OrderProduct::whereHas('order', function ($query) {
                return $query->where('confirmed', true);
            })
            ->whereDate('created_at', now()->subDay($i))
            ->with(['product' => function ($query) {
                $query->remember(60 * 60);
            }])
            ->get();

            $labels->push(
                $products->pluck('product_id')->unique()->values()->flatten()
            );

            if (! $products->isEmpty()) {
                $data->push(
                    $products->pluck('product')->unique()->values()
                );
            }
        }

        $labels = $labels->flatten();

        $productsData = collect();

        $data = $data->flatten();

        $mappedData = $labels->countBy()->mapWithKeys(function ($sales, $id) use ($data, &$productsData) {
            $product = $data->where('id', $id)->first();
            $productName = $product->sku ?? $id;

            $productsData->push((Object) [
                'product' => $product,
                'sales' => $sales,
            ]);

            return [
                $productName => $sales
            ];
        });

        $this->productsData = $productsData;

        $this->labels($mappedData->keys());

        $colorHash = new ColorHash();

        $colors = $mappedData->map(function ($text, $sales) use ($colorHash) {
            return $colorHash->rgb($sales . $text);
        });

        $this->dataset('Sales per product', 'pie', $mappedData->values())
            ->options([
                'backgroundColor' => $colors->values()->map(function ($color) {
                    return 'rgba(' . implode(', ', $color) . ')';
                }),
            ]);
        ;

        // blue
        return $this;
    }
}
