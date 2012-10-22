<?php

namespace GW2Spidy\Dataset;

use GW2Spidy\Util\RedisCacheHandler;
use GW2Spidy\Util\Singleton;

class DatasetManager extends Singleton {
    protected $cache;

    public function __construct() {
        $this->cache = RedisCacheHandler::getInstance('datasets', true);
    }

    public function getGemDataset($type) {
        $cacheKey = "gem_{$type}";
        $dataset  = $this->cache->get($cacheKey);

        if (!$dataset) {
            $dataset = new GemExchangeDataset($type);
        }

        $dataset->updateDataset();

        $this->cache->set($cacheKey, $dataset);

        return $dataset;
    }

}
