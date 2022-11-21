<?php

namespace FME\EloquenceCsvFeed\Base;

use Illuminate\Support\Str;
use FME\EloquenceCsvFeed\Helper;
use Illuminate\Support\Facades\Storage;

abstract class EloquenceCsvFeedHandler
{
    /**
     * Model name
     *
     * @var
     */
    protected $model;

    /**
     * Limit
     *
     * @var
     */
    protected $limit;

    /**
     * Offset
     *
     * @var
     */
    protected $offset;

    /**
     * Data
     *
     * @var collection
     */
    protected $feed;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->feed = collect();
    }

    /**
     * Generate csv file and save it to storage
     *
     * @return generated array
     */
    abstract public function handle() : void;

    /**
     * Here you can set CSV lines
     *
     * @param  item to transform
     * @return wanted format
     */
    abstract public function transform($item) : array;

    /**
     * transform feed to csv array
     *
     * @return array
     */
    public function toArray() : array
    {
        if (is_null($this->feed) || $this->feed->isEmpty()) {
            $this->handle();
        }

        if (is_null($this->feed)) {
            throw new \Exception("You should set feed data on handle method", 1);
        }

        return $this->feed->toArray();
    }

    /**
     * @return [type] [description]
     */
    public function storeCsv()
    {
        $fh = fopen('php://output', 'w');

        $items = $this->toArray();

        $headers = array_keys($items[0]);

        ob_start();
        fputcsv($fh, $headers);

        foreach ($items as $item) {
            fputcsv($fh, array_values($item));
        }

        $content = ob_get_clean();
        fclose($fh);

        Storage::disk('public')->delete($this->fileName());

        return Storage::disk('public')->put($this->fileName(), $content);
    }

    /**
     * Set feed
     */
    public function setFeed(array $data)
    {
        $this->feed = collect($data);
    }

    /**
     * Set Limit
     * @param $startAt
     * @param $endAt
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set offset
     * @param $startAt
     * @param $endAt
     */
    public function setOffset(int $offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasLimit()
    {
        return isset($this->limit) && (int)$this->limit > 0;
    }

    /**
     * @return boolean
     */
    public function hasOffset()
    {
        return isset($this->offset) && is_int($this->offset);
    }
    
    /**
     * Filename
     */
    public function fileName()
    {
        $fileName = Str::slug($this->model);

        if ($this->hasLimit()) {
            $fileName .= '-'.$this->limit;
        }
        
        if ($this->hasOffset()) {
            $fileName .= '-'.$this->offset;
        }

        return $fileName . '-feed.csv';
    }
}
