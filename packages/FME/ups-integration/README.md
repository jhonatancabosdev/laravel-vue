# UPS API Integration

This is a simple guide to help you integrate 
UPS with eCommerce website based on starter-shop.

***Make sure this package is under packages folder "/packages/FME/ups-integration"***

## Installation

You can install the package via composer:

```bash
composer config repositories.local '{"type": "path", "url": "packages/FME/ups-integration"}' --file composer.json
```

Then:

```bash
composer require fme/ups-integration
```

If you have hard times installing this packages please read  [this article. ](https://laravel-news.com/developing-laravel-packages-with-local-composer-dependencies)
You can publish the config file with:

```bash
php artisan vendor:publish --provider="FME\Ups\UpsServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
	// enable request caching
	'cache' => true,
	'accessKey' => '',
	'userId' => '',
	'password' => '',
	'services' => [
		[
			'label' => 'UPS 2nd Day Air',
			'Code' => '02',
			'Description' => 'UPS 2nd Day Air',
		],
		[
			'label' => 'UPS Ground',
			'Code' => '03',
			'Description' => 'UPS Ground'
		],
	],
	'shipFrom' =>  [
        "name" => "",
        "company" => "",
        'email' => '',
        "street1" => "",
        "street2" => null,
        "street3" => null,
        "city" => "",
        "state" => "",
        "postalCode" => "",
        "country" => "US",
        "phone" => config('default-variables.phone'),
        "residential" => false
    ]
];
```

# Usage

## Address Validation

Before validating any addresses. you need to add new address type and new column to save returned response.
Create new migration and add "validated_response" column, and update "type" column


``` php
Schema::table('addresses', function (Blueprint $table) {
	$table->enum('type', ['billing', 'shipping', 'validated'])->change();
	$table->text('validated_response')->after('address_2')->nullable();
});
```



Then we can add validated address attribute to our **Order** model.



``` php
class Order 
{
	...

    /**
     * Get validated Address
     * 
     * @return Address|null
     */
    public function getValidatedAddressAttribute()
    {
        return $this->addresses->where('type', 'validated')->first();
    }
}
```



Next step, Add **ValidatedAddressTrait** to your ***AddressRepository***
This trait will allow us to create \App\Address from returned UPS response.




``` php
class AddressRepository extends BaseRepository
{
    use ValidatedAddressTrait;

```

### Validate customer address

You can validate customer address using **UpsFacade** class


``` php
$response = UpsFacade::validateAddress($address);
```

**$response** Output


``` php
..{#2104
	+"addressClassification": {#2092
		+"code": {#2093
		  	+"0": "2"
		}
		+"description": {#2097
		  	+"0": "Residential"
		}
	}
	+"consigneeName": ""
	+"buildingName": ""
	+"addressLine": "23311 W PALOMA BLANCA DR"
	+"addressLine2": null
	+"addressLine3": null
	+"region": "MALIBU CA 90265-3074"
	+"politicalDivision2": "MALIBU"
	+"politicalDivision1": "CA"
	+"postcodePrimaryLow": "90265"
	+"postcodeExtendedLow": "3074"
	+"urbanization": ""
	+"countryCode": "US"
}
```

### Exemple for using address validation:

``` php
class AddressValidationController
{
	...

	/**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ...

        $data = $request->except('_token');

        $address = $request->has('shipping_address_different')
            ? $this->addressRepository->createShippingAddress($data) 
            : $this->addressRepository->createBillingAddress($data);

        $response = UpsFacade::validateAddress($address);

        if ($response === false) {
            throw new \Exception("Invalid address");
        }

        return response()->json(
            Arr::wrap($response)
        );
    }
}
```


Remember to create new record for **validated address**. This is done on CheckoutController. Here is an exemple:



``` php

	/**
     * Create new order then redirect user to checkout.execute
     * Route where payment is processed and confirmed
     *
     * @param  GuestCheckoutRequest $request
     * @return RedirectResponse
     */
    public function store(GuestCheckoutRequest $request)
    {
    	// get all request attributes
        $data = $request->except('_token');

        ...

        // create validated address from submitted data
        $address = $this->addressRepository->createValidatedAddress(
        	$data['validatedAddress']
        );
        
        // attach address to created order
        $order->addresses()->save($address);

        ...
```



## Shipping rates

You can ignore all the details. Just use the following line.

```php
	$rates = UpsFacade::getRates($order->validatedAddress, $order->products);
```

**$rates** Output

```php
...array:2 [
  	"ups-2nd-day-air" => {#2610
		+"label": "UPS 2nd Day Air"
		+"slug": "ups-2nd-day-air"
		+"serviceCode": "02"
		+"cost": 86.92
		+"warning": "Your invoice may vary from the displayed reference rates"
		+"GuaranteedDaysToDelivery": "2"
		+"deliveryDate": "Tuesday, 05/26/2020"
  	}
  	"ups-ground" => array:5 [
	    "label" => "UPS Ground"
	    "slug" => "ups-ground"
	    "serviceCode" => "03"
	    "cost" => 22.16
	    "warning" => "Your invoice may vary from the displayed reference rates"
  	]
]
```


### Exemple for getting shipping rates

```php
class ShippingController
{
	/**
	 * @var CartRepository
	 */
	protected $cartRepository;

	/**
	 * Shipping controller
	 */
	public function __construct(CartRepository $cartRepository)
	{
		$this->cartRepository = $cartRepository;
	}

	/**
	 * @return jsonResponse
	 */
	public function index(Request $request)
	{
		...

		$cartItems = $this->cartRepository->getCartItemsTransformed();

		$rates = UpsFacade::getRates(
			$order->validatedAddress, $cartItems
		);

        return collect($rates)->sortBy('cost');
	}

	/**
     * @param  Request $request
     * @return JsonResponse                              
     */	
	public function update(UpdateUpsOptionsRequest $request)
	{
		$rate = (Object) $request->only([
			'label', 'slug', 'serviceCode', 'cost'
		]);

		if (is_null($rate)) {
			throw new \Exception("Invalid shipping");
		}

		$this->cartRepository->setShipping($rate->cost);

		return response()->json(
			(float) $this->cartRepository->getShipping()
		);
	}

	...
```


### Checkout and attach selected shipping

Update your **OrderRepository** to handle selected rates. 

Exemple:

```php
class OrderRepository extends BaseRepository
{

	...

	/**
     * @param  Order  $order           
     * @param  array  $data            
     * @param  StdClass $selectedShipping
     * @return Order
     */
    public function updateOrder(Order $order, array $data, $selectedShipping)
    {
    	...

    	// Request API to make sure customer 
    	// is getting the right rates
    	$rates = UpsFacade::getRates($order->validatedAddress, $cartItems);

    	// filter selected shipping method
        $rate = (Object) collect($rates)->values()->firstWhere('slug', $selectedShipping->slug);

        if (is_null($rate)) {
            throw new \Exception("Invalid shipping");
        }

        $this->cartRepository->setShipping($rate->cost);

        $order->carriers()->create([
            'service_name' => $rate->label,
            'service_code' => $rate->serviceCode,
            'shipment_cost' => $rate->cost,
            'carrier_code' => $rate->slug,
        ]);

        // update order shipping cost, total, subtotal ...
        $order->update([
        	...
			'shipping_cost' => $this->cartRepository->getShipping(),
        ]);

        return $order;
    }

```



