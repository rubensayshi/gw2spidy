<?php

namespace GW2Spidy\Util;

class ApplicationCache extends CacheHandler {
    protected static $applicationCacheKey = "gw2spidy";

    /**
     * @return ApplicationCache
     */
    static public function getInstance() {
        return parent::getInstance(self::$applicationCacheKey);
    }

    protected function __construct() {
        parent::__construct(self::$applicationCacheKey);
    }
}