<?php

namespace TCG\Voyager\Charts;

use App\Order;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class SalesChart extends Chart
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

        $data = collect([]);

        for ($i = 15; $i > -1; $i--) {
            $data->push(
                Order::confirmed()->whereDate('created_at', now()->subDay($i))->remember(60 * 60)->sum('subtotal')
            );
        }

        // blue
        $this->dataset('Sales per day', 'line', $data)->backgroundColor('rgba(52, 144, 220, 0.64)');

        return $this;
    }
}
