<?php

namespace FME\EloquenceCsvFeed;

use FME\EloquenceCsvFeed\Helper;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EloquanceCsvFeedController
{
    public function __construct()
    {
        set_time_limit(0);
        ini_set("memory_limit", "-1");
    }

    /**
     * Download csv
     *
     * @param
     * @return
     */
    public function download($model, $offset = null, $limit = null)
    {
        $handler = Helper::getHandler($model);
        $handler = ( new $handler($model) );

        if (isset($limit)) {
            $handler->setLimit($limit);
        }
        
        if (isset($offset)) {
            $handler->setOffset($offset);
        }

        return response()->download(
            storage_path('app/public/'. $handler->fileName())
        );
    }

    /**
     * Download csv file
     *
     * @return csv output
     */
    public function getCsv($model)
    {
        $handler = Helper::getHandler($model);
        $handler = new $handler($model);

        $handler->storeCsv();

        return response()->download(
            storage_path('app/public/'. $handler->fileName())
        );
    }


    /**
     * Handle requested model
     *
     */
    public function getJson($model)
    {
        // get requested resource if resource not available
        // cancel this request
        if (!isset(config('eloquanceCsvFeed.models')[$model])) {
            throw new NotFoundHttpException();
        }

        $handler = Helper::getHandler($model);
        $handler = ( new $handler($model) );

        return $handler->toArray();
    }
}
