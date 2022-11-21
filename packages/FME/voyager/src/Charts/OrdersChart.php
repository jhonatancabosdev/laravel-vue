<?php

namespace TCG\Voyager\Charts;

use App\Order;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class OrdersChart extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function setLabels()
    {
        $labels = [];

        for ($i = 15; $i > -1; $i--) {
            $labels[] = now()->subDay($i)->format('D, d-m');
        }

        $this->labels($labels);
        
        return $this;
    }

    public function setData()
    {
        $this->setLabels();
        
        $ordersConfirmedData = collect([]);
        $ordersNotConfirmedData = collect([]);
        $refundedOrders = collect([]);

        for ($i = 15; $i > -1; $i--) {
            $ordersConfirmedData->push(
                Order::confirmed()->whereDate('created_at', now()->subDay($i))->remember(60 * 60)->count()
            );

            $ordersNotConfirmedData->push(
                Order::notConfirmed()->whereDate('created_at', now()->subDay($i))->remember(60 * 60)->count()
            );

            $refundedOrders->push(
                Order::refunded()->whereDate('created_at', now()->subDay($i))->remember(60 * 60)->count()
            );
        }

        $this->dataset('Confirmed orders', 'bar', $ordersConfirmedData)->backgroundColor('rgba(52, 144, 220, 0.64)'); // blue
        $this->dataset('Abandoned orders', 'bar', $ordersNotConfirmedData)->backgroundColor('rgba(115, 115, 115, 0.46)'); // secondary
        $this->dataset('Refunds', 'bar', $refundedOrders)->backgroundColor('rgba(146, 15, 22, 0.62)'); // red
        $this->options(json_decode('{"scales":{"xAxes":[{"stacked":true}],"yAxes":[{"stacked":true}]}}', true));
        return $this;
    }
}
