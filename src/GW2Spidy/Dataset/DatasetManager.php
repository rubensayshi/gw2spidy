<?php

namespace GW2Spidy\Dataset;

use GW2Spidy\DB\Item;

use GW2Spidy\Util\RedisCacheHandler;
use GW2Spidy\Util\Singleton;

class DatasetManager extends Singleton {
    protected $cache;
    protected $useCache = true;

    public function __construct() {
        $this->cache = RedisCacheHandler::getInstance('datasets', true);
    }

    public function setUseCache($useCache = true) {
        $this->useCache = false;
    }

    public function getGemDataset($type) {
        $cacheKey = "gem_{$type}";
        $dataset  = $this->cache->get($cacheKey);

        if (!$this->useCache || !$dataset) {
            $dataset = new GemExchangeDataset($type);
        }

        $dataset->updateDataset();

        $this->cache->set($cacheKey, $dataset);

        return $dataset;
    }

    public function getItemDataset(Item $item, $type) {
        $cacheKey = "item_{$item->getDataId()}_{$type}";
        $dataset  = $this->cache->get($cacheKey);

        if (!$this->useCache || !$dataset) {
            $dataset = new ItemDataset($item->getDataId(), $type);
        }

        $dataset->updateDataset();

        $this->cache->set($cacheKey, $dataset);

        return $dataset;
    }

    public function getItemVolumeDataset(Item $item, $type) {
        $cacheKey = "item_v_{$item->getDataId()}_{$type}";
        $dataset  = $this->cache->get($cacheKey);

        if (!$this->useCache || !$dataset) {
            $dataset = new ItemVolumeDataset($item->getDataId(), $type);
        }

        $dataset->updateDataset();

        $this->cache->set($cacheKey, $dataset);

        return $dataset;
    }

    public function purgeCache() {
        $this->cache->purge();
    }
}
