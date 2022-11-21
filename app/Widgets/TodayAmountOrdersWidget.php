<?php

namespace App\Widgets;

use App\Order;
use Arrilot\Widgets\AbstractWidget;

class TodayAmountOrdersWidget extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        return view('widgets.today_amount_orders_widget', [
            'todaySales' => '$' . number_format(Order::today()->sum('total'), 2),
            'countOrders' => Order::today()->count(),
            'count' => Order::confirmed()->count(),
            'drops' => Order::notConfirmed()->count(),
            'sum' => '$' . number_format(Order::confirmed()->sum('total'), 2),
        ]);
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return true;
    }
}
