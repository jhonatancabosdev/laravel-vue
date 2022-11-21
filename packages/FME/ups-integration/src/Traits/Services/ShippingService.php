<?php

namespace FME\Ups\Traits\Services;

use App\Order;
use App\TrackingNumber;
use App\trackingNumbers;
use Ups\Entity\Shipment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

trait ShippingService
{


    /**
     * @return \Ups\Shipping
     */
    public function getShippingService(): \Ups\Shipping
    {
        return new \Ups\Shipping(
            $this->config['accessKey'],
            $this->config['userId'],
            $this->config['password'],
            $this->config['sandbox']
        );
    }

    /**
     * @param  Order  $order
     *
     * @return Shipment
     */
    public function getShippingShipment(Order $order, Collection $products): Shipment
    {
        $shipment = new Shipment();
        $shipment->setShipper($this->getShipper());
        $shipment->setShipFrom($this->getShipFrom());
        $shipment->setShipTo($this->getShipTo($order->validatedAddress));
        $shipment->setSoldTo($this->getSoldTo($order->validatedAddress));

        $shipment->setPackages(
            $this->getPackages($products, $freeShipping = false)
        );

        $service = new \Ups\Entity\Service();
        $service->setCode($order->carrier->service_code);
        $service->setDescription($service->getName());

        $shipment->setService($service);
        $shipment->setDescription("Order #{$order->id}");

        // $reference = new \Ups\Entity\ReferenceNumber();
        // $reference->setCode(\Ups\Entity\ReferenceNumber::CODE_INVOICE_NUMBER);
        // $reference->setValue($order->id);
        //    $shipment->setReferenceNumber($reference);

        $shipment->setPaymentInformation(
            new \Ups\Entity\PaymentInformation('prepaid', (object) ['AccountNumber' => $this->getAccountNumber()])
        );

        return $shipment;
    }

    /**
     * @param  Stdclass $shipping
     * @param  string $disk
     * @return TrackingNumber
     */
    public function storeTrackingNumber(Order $order, $shipping, string $disk = 'tracking'): TrackingNumber
    {
        $labelName = $shipping->ShipmentIdentificationNumber;
        $labelFormat = $shipping->PackageResults->LabelImage->LabelImageFormat->Code;
        $labelContent = base64_decode($shipping->PackageResults->LabelImage->GraphicImage);

        if (! Storage::disk($disk)->put("$labelName.$labelFormat", $labelContent)) {
            throw new \Exception("can't store tracking label");
        }

        return TrackingNumber::create([
            'order_id' => $order->id,
            'carrier_code' => $order->carrier->service_code,
            'carrier_name' => $order->carrier->service_name,
            'number' => $shipping->PackageResults->TrackingNumber,
            'shipment_cost' => $shipping->ShipmentCharges->TotalCharges->MonetaryValue,
            'shipment_id' => $shipping->ShipmentIdentificationNumber,
            'file_name' => "$labelName.$labelFormat",
            'file_path' => Storage::disk($disk)->url("$labelName.$labelFormat"),
            'details' => json_encode($shipping),
        ]);
    }

    /**
     * @param  Order  $order
     *
     * @return mixed
     */
    public function getShipping(Order $order, Collection $products)
    {
        set_time_limit(0);
        
        $api = $this->getShippingService();

        $shipment = $this->getShippingShipment($order, $products);

        $confirm = $api->confirm(\Ups\Shipping::REQ_VALIDATE, $shipment);

        if (! isset($confirm->ShipmentDigest)) {
            throw new \Exception("Error Processing Shipping Request");
        }

        return $api->accept($confirm->ShipmentDigest);
    }
}
