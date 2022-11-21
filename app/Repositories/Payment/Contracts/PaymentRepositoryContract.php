<?php 

namespace App\Repository\Payment\Contracts;

use App\Order;
use Illuminate\Http\Request;

interface PaymentRepositoryContract
{
	/**
	 * Process order payment
	 * 
	 * @param  Order  $order
	 * @return mixed
	 */
	public function process(Order $order);

	/**
	 * Check transaction status if processed
	 * 
	 * @param  Request $request
	 * @param  Order   $order
	 * @return mixed
	 * @throws \Exception
	 */
	public function check(Request $request, Order $order);

	/**
	 * Confirm transaction and mark order as confirmed
	 * 
	 * @param  Order  $order
	 * @return mixed
	 */
	public function confirm(Order $order);
}