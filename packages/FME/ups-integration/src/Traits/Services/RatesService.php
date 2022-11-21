<?php

namespace FME\Ups\Traits\Services;

use App\Address;
use App\Product;
use Ups\Entity\Package;
use Ups\Entity\Service;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Ups\Entity\PackagingType;
use Ups\Entity\UnitOfMeasurement;
use Illuminate\Support\Collection;
use Ups\Entity\ShipmentTotalWeight;
use App\Repositories\CartRepository;
use Illuminate\Support\Facades\Cache;
use Ups\Entity\ShipmentServiceOptions;

trait RatesService
{
    /**
     * @return \Ups\Rate
     */
    public function getRatesService(): \Ups\Rate
    {
        return new \Ups\Rate(
            $this->config['accessKey'],
            $this->config['userId'],
            $this->config['password'],
            $this->config['sandbox']
        );
    }

    /**
     * @param  Address     $address
     * @param  Collection  $products
     * @param  string|null $serviceCode
     * @return array
     */
    private function getRatesRequest(Address $address, Collection $products, string $serviceCode = null)
    {
        $response = [];
        $shipment = new \Ups\Entity\Shipment();
        $shipment->setShipper($this->getShipper());
        $shipment->setShipFrom($this->getShipFrom());
        $shipment->setShipTo($this->getShipTo($address));

        $packages = $this->getPackages($products);

        if (count($packages) === 0 && ! $products->isEmpty()) {
            return $products->map(function ($product) {
                return $product instanceof Product
                    ? $product->freeShippingService
                    : $product->product->freeShippingService;
            })
            ->unique('slug')
            ->values();
        }

        $shipment->setPackages($packages);

        $ratesService = $this->getRatesService();

        foreach ($this->getEnabledServices($serviceCode) as $service) {
            try {
                $rate = $ratesService->getRate(
                    $shipment->setService(new Service((Object) $service))
                );

                $key = Str::slug($service['label']);

                $rate = json_decode(json_encode($rate->RatedShipment), true)[0];

                if (config('app.env') === 'local') {
                    $response[$key]['response'] = $rate;
                }

                $response[$key]['label'] = $service['label'];
                $response[$key]['slug'] = $key;
                $response[$key]['serviceCode'] = $service['Code'];
                $response[$key]['cost'] = $rate['TotalCharges']['MonetaryValue'] ?? 0;
                $response[$key]['warning'] = $rate['RateShipmentWarning'];
                $response[$key]['shipping_label'] = $rate['shipping_label'] ?? null;

                if (isset($rate['GuaranteedDaysToDelivery']) && trim($rate['GuaranteedDaysToDelivery']) !== '') {
                    $response[$key]['GuaranteedDaysToDelivery'] = $rate['GuaranteedDaysToDelivery'];
                    $response[$key]['deliveryDate'] = now()->addWeekdays($rate['GuaranteedDaysToDelivery'])->format('l, m/d/Y');
                }

                $response[$key] = (Object) $response[$key];
            } catch (\Exception $e) {
            }
        }

        return $response;
    }

    /**
     * @param  Address     $address
     * @param  Collection  $products
     * @param  string|null $serviceCode
     * @return array
     */
    public function getRates(Address $address, Collection $products, string $serviceCode = null)
    {
        if((new CartRepository())->getWeight() > config('default-variables.flat_rate_shipping_at')){
            return collect([
                'Ground' => [
                    'label' => 'Ground',
                    'serviceCode' => '03',
                    'cost' => config('default-variables.flat_rate_shipping'),
                    'slug' => 'ground'
                ]
            ]);
        }
        
        if (! config('ups.cache')) {
            return $this->getRatesRequest($address, $products, $serviceCode);
        }

        $self = $this;
        $cacheKey = md5($address->toJson . $products->toJson() . $serviceCode);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($self, $address, $products, $serviceCode) {
            return $this->getRatesRequest($address, $products, $serviceCode);
        });
    }
}
