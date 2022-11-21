<?php

namespace FME\EloquenceCsvFeed\Handlers;

use App\Product;
use \FME\EloquenceCsvFeed\Helper;
use \FME\EloquenceCsvFeed\Base\EloquenceCsvFeedHandler;

class ProductHandler extends EloquenceCsvFeedHandler
{
    /**
     * @param $model model name
     * @param $startAt limit from
     * @param $endAt limit ends
     */
    public function __construct($model = null)
    {
        $this->model = $model;
    }

    /**
     * Set feed to feed attribute
     * 
     * @return [type] [description]
     */
    public function handle() : void
    {
        $data = [];
        $fh = fopen('php://output', 'w');

        $query = Product::with(['images','brand', 'categories'])
            ->where('price', '>', 0);

        if ($this->hasOffset()) {
            $query = $query->offset((int)$this->offset);
        }

        if ($this->hasLimit()) {
            $query = $query->limit((int)$this->limit);
        }

        if ($this->hasOffset() && !$this->hasLimit()) {
            $limit = Product::count();
            $query = $query->limit($limit);
        }

        $query->chunk(150, function($products) use (&$data) {
                foreach ($products as $product) {

                    if (strpos($product->main_image, config('default-variables.default-image')) !== false) {
                        continue;
                    }

                    if (! $product->category instanceof \App\Category) {
                        continue;
                    }

                    $data[] = $this->transform($product);
                }
            });

        $this->setFeed($data);
    }

    /**
     * transform product to array
     *
     * @param product
     * @return array
     */
    public function transform($product) : array
    {
        $googleProductCategory = $this->getGoogleProductCategory(
            $product->parentCategories->pluck('name')->toArray()
        );

        $categories = explode(' > ', $googleProductCategory);

        if (trim($product->google_product_category) === '') {
            $product->google_product_category = $googleProductCategory;
            $product->save();
        }

        $description = html_entity_decode(strip_tags($product->description));

        if (trim($description) === '') {
            $description = ucfirst($product->name);
        }

        return [

            'id'            => $product->sku,

            'title'         => ($product->condition === 'Remanufactured' ? ($product->condition . ' ') : '') . ucfirst($product->name),

            'description'   => $description,

            'link'          => route('product.show', $product->id),

            'condition'     => strtolower($product->condition === 'Remanufactured' ? 'Refurbished' : $product->condition),

            'price'         => number_format($product->price, 2, '.', '') . ' USD',

            'availability'  => $product->quantity > 0 ? 'in stock' : 'out of stock',

            'image_link'    => asset($product->main_image),

            'gtin'          => $product->upc,

            'mpn'           => $product->mpn,

            'brand'         => html_entity_decode($product->brand->name),

            'google_product_category' => $product->google_product_category,

            'shipping_weight' => $product->weight . ' lb',

            'Custom_label_0' => isset($categories[0]) ? $categories[0] : '',

            'Custom_label_1' => isset($categories[1]) ? $categories[1] : '',

            'Custom_label_2' => isset($categories[2]) ? $categories[2] : '',

            'Custom_label_3' => isset($categories[3]) ? $categories[3] : '',

            'Custom_label_4' => isset($categories[4]) ? $categories[4] : '',

            'shipping_label' => ''
        ];
    }

    /**
     * @param  array  $categories
     * @return string
     */
    private function getGoogleProductCategory(array $categories)
    {
        if (isset($categories[1]) && trim($categories[1]) !== '') {

            switch (strtolower(trim($categories[0]))) {
                case 'toner':
                    return 'Electronics > Print, Copy, Scan & Fax > Printer, Copier & Fax Machine Accessories > Printer Consumables > Toner & Inkjet Cartridges';

                case 'printer parts':
                    return 'Electronics > Print, Copy, Scan & Fax > Printer, Copier & Fax Machine Accessories > Printer, Copier & Fax Machine Replacement Parts';

                case 'drum / imaging units':
                    return 'Electronics > Print, Copy, Scan & Fax > Printer, Copier & Fax Machine Accessories > Printer Consumables > Printer Drums & Drum Kits';

                case 'developer':
                    return 'Electronics > Print, Copy, Scan & Fax > Printer, Copier & Fax Machine Accessories > Printer Consumables';

                case 'staples':
                    return 'Office Supplies > General Office Supplies > Staples';

                case 'waste containers':
                    return 'Home & Garden > Household Supplies > Waste Containment > Trash Cans & Wastebaskets';

                case 'copier drums':
                    return 'Electronics > Print, Copy, Scan & Fax > Printer, Copier & Fax Machine Accessories > Printer Consumables > Printer Drums & Drum Kits';

                case 'scanner parts':
                    return 'Electronics > Print, Copy, Scan & Fax > Printer, Copier & Fax Machine Accessories > Printer, Copier & Fax Machine Replacement Parts';

                case 'ink':
                    return 'Electronics > Print, Copy, Scan & Fax > Printer, Copier & Fax Machine Accessories > Printer Consumables > Toner & Inkjet Cartridges';
            }

        }

        switch (strtolower(trim($categories[0]))) {
            case 'ink & toner':
                return 'Electronics > Print, Copy, Scan & Fax > Printer, Copier & Fax Machine Accessories > Printer Consumables > Toner & Inkjet Cartridges';

            case 'scanners':
                return 'Electronics > Print, Copy, Scan & Fax > Scanners';

            case 'printers':
                return 'Electronics > Print, Copy, Scan & Fax > Printers, Copiers & Fax Machines';
        }
    }
}
