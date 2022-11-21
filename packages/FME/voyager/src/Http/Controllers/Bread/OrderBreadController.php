<?php

namespace TCG\Voyager\Http\Controllers\Bread;

use App\Order;
use App\Address;
use App\OrderStatus;
use App\Mail\RefundMailable;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Mail;
use App\Notifications\OrderNotification;
use App\Repositories\Payment\AuthorizeNetRepository;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class OrderBreadController extends VoyagerBaseController
{
    /**
     * @param  Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->authorize('browse', app(Order::class));

        $dataType = Voyager::model('DataType')->where('slug', $this->getSlug($request))->first();

        if (! $request->ajax()) {
            $view = view()->exists("voyager::orders.browse") ? 'voyager::orders.browse' : 'voyager::bread.browse';

            return Voyager::view($view, [
                'dataType' => $dataType,
                'orderStatus' => OrderStatus::all(),
            ]);
        }

        $dataTypeContent = $this->prepareFilterQuery($request)->paginate(30);

        $dataTypeContent->transform(function ($order) {
            $order->billingAddress = $order->billingAddress;
            $order->shippingAddress = $order->shippingAddress;
            $order->lastCCDigits = $order->LastCCDigits;
            $order->makeVisible(['card_type']);
            return $order;
        });

        return $dataTypeContent;
    }

    /**
     * @return void
     */
    public function refund(Request $request, Order $order)
    {
        $this->authorize('edit', app(Order::class));

        try {
            (new AuthorizeNetRepository)->refund($order);
            Mail::send(new RefundMailable($order));
            $order->loadMissing('orderStatus');
            return $order->refresh();
        } catch (\Exception $exception) {
            $order->transaction_response = $exception->getMessage();
            $order->save();
            throw $exception;
        }
    }

    /**
     * @return void
     */
    public function updateColumn(Request $request, Order $order, String $column)
    {
        $this->authorize('edit', app(Order::class));

        return response()->json(
            $order->update([
                $column => $request->value,
            ])
        );
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function notify(Request $request)
    {
        $this->authorize('browse', app(Order::class));

        $request->validate([
            'id' => 'required|exists:orders,id'
        ]);

        try {
            $order = Order::findOrFail($request->input('id'));

            try {
                $order->notify(new OrderNotification($order));
                $order->markAsMailed();
                return response()->json([true]);
            } catch (\Exception $exception) {
                return response()->json($exception->getMessage());
            }
        } catch (\Exception $e) {
            return response()->json(false);
        }
    }

    /**
     * @return string
     */
    public function export(Request $request)
    {
        $fh = fopen('php://output', 'w');

        ob_start();

        fputcsv($fh, [
            'Order_Id',
            'Product',
            'SKU',
            'Quantity',
            'Name',
            'Email',
            'Customer_id',
            'Created_at',
            'Source',
            'Gclid',
            'Total_product_cost',
            'Shipping_cost',
            'Tax',
            'Total_paid',
            'Order_status',
            'Transaction_id',
            'Cc_number',
            'Carrier',
            
            'Billing_address',
            'Billing_address2',
            'Billing_address_state',
            'Billing_address_city',
            'Billing_address_zip',
            
            'Shipping_address1',
            'Shipping_address2',
            'Shipping_address_state',
            'Shipping_address_city',
            'Shipping_address_zip'
        ]);

        $query = $this->prepareFilterQuery($request);

        $orders = $query->get();

        foreach ($orders as $order) {
            foreach ($order->products as $index => $product) {
                fputcsv($fh, [
                    $order->id,
                    $product->id,
                    $product->sku,
                    $product->pivot->quantity,
                    $order->name,
                    $order->email,
                    $order->customer_id,
                    $order->created_at,
                    $order->order_source,
                    $order->gclid,
                    $product->price * $product->pivot->quantity,
                    $order->shipping_cost,
                    $order->tax,
                    $order->total,
                    $order->orderStatus->name,
                    $order->transaction_id,
                    $order->lastCCDigits,
                    isset($order->carrier) && is_object($order->carrier) ? $order->carrier->name ?? '' : '',

                    is_object($order->billingAddress) && isset($order->billingAddress->address1) ? $order->billingAddress->address1 : '',
                    is_object($order->billingAddress) && isset($order->billingAddress->address2) ? $order->billingAddress->address2 : '',
                    is_object($order->billingAddress) && is_object($order->billingAddress->state) && isset($order->billingAddress->state) ? $order->billingAddress->state->name : '',
                    is_object($order->billingAddress) && isset($order->billingAddress->city) ? $order->billingAddress->city : '',
                    is_object($order->billingAddress) && isset($order->billingAddress->zipcode) ? $order->billingAddress->zipcode : '',

                    is_object($order->shippingAddress) && isset($order->shippingAddress->address1) ? $order->shippingAddress->address1 : '',
                    is_object($order->shippingAddress) && isset($order->shippingAddress->address2) ? $order->shippingAddress->address2 : '',
                    is_object($order->shippingAddress) && is_object($order->shippingAddress->state) && isset($order->shippingAddress->state) ? $order->shippingAddress->state->name : '',
                    is_object($order->shippingAddress) && isset($order->shippingAddress->city) ? $order->shippingAddress->city : '',
                    is_object($order->shippingAddress) && isset($order->shippingAddress->zipcode) ? $order->shippingAddress->zipcode : '',
                ]);
            }
        }

        $string = ob_get_clean();

        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/plain',
            'Content-Disposition' => 'attachment; filename=orders-'. strtotime('now') .'.csv',
            'Expires'             => '0',
            'Pragma'              => 'public',
        ];

        return response()->stream(function () use ($string) {
            echo $string;
        }, 200, $headers);
    }

    /**
     * @return Builder
     */
    protected function prepareFilterQuery(Request $request)
    {
        $query = Order::with([
            'carriers',
            'addresses.state',
            'trackingNumbers',
            'orderStatus',
            'products.images',
        ]);

        $search = (object) ['value' => $request->get('s')];

        if ($search->value !== null && trim($search->value) !== '' && trim($request->get('s')) !== '') {
            $query = $query->search($search->value);
        }

        if (is_array($request->input('order_status_id')) && ! empty($request->input('order_status_id'))) {
            $query = $query->whereIn(
                'order_status_id',
                array_map('intval', $request->input('order_status_id'))
            );
        }

        if (! $request->boolean('showAll')) {
            $query = $query->where('confirmed', $request->boolean('confirmed'));
            $query = $query->where('refunded', $request->boolean('refunded'));
            $query = $query->where('reported', $request->boolean('reported'));
        }

        if (is_array($request->input('source')) && ! empty($request->input('source')) && count($request->input('source')) === 1) {
            if (in_array('adwords', $request->input('source'))) {
                $query = $query->whereNotNull('gclid');
            }

            if (in_array('direct', $request->input('source'))) {
                $query = $query->whereNull('gclid');
            }
        }

        $orderBy = $request->get('order_by', $dataType->order_column ?? 'id');
        $sortOrder = $request->get('sort_order', $dataType->order_direction ?? 'asc');
        $sortOrder = ! in_array(strtolower($sortOrder), ['asc', 'desc']) ? 'asc' : $sortOrder;

        if ($orderBy === 'billingAddress' || $orderBy === 'shippingAddress') {
            $type = $orderBy === 'billingAddress' ? 'billing' : 'shipping';
            $query = $query->orderBy(
                Address::select('address_1')
                        ->whereColumn('orders.id', 'order_id')
                        ->where('type', $type)
                        ->orderBy('id', 'desc'),
                $sortOrder
            );
        } else {
            $query = $query->orderBy($orderBy, $sortOrder);
        }


        return $query;
    }
}
