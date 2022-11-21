<?php

namespace App\Http\Controllers;

use App\Zipcode;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateTaxRequest;
use App\Repositories\Contracts\CartRepositoryContract;

class TaxController extends Controller
{
	/**
	 * Shipping controller
	 */
	public function __construct(CartRepositoryContract $cartRepository)
	{
		$this->cartRepository = $cartRepository;
	}

    /**
     * Update tax on cart
     * 
     * @param  UpdateTaxRequest $request
     * @return JsonResponse                   
     */
	public function update(UpdateTaxRequest $request, Zipcode $zipcode)
	{
		$zipcodes = config('tax.zipcodes', []);
		if($zipcodes == [] || confiin_array($zipcode->state->abv, $zipcodes)) {
			$this->cartRepository->setTax($zipcode->tax_rate);
			return response()->json($zipcode->tax_rate);
		} else {
			$this->cartRepository->setTax(0);
			return response()->json(0);

		}
	}
}
