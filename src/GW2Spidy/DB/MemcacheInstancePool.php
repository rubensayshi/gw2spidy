<?php

namespace GW2Spidy\DB;

use GW2Spidy\Util\CacheHandler;

class MemcacheInstancePool extends CacheHandler
{
    const EXPIRES = 3600;

    public function addInstanceToPool($obj, $key) {
        $this->set($key, $obj, MEMCACHE_COMPRESSED, self::EXPIRES);
    }

    public function getInstanceFromPool($key) {
        $obj = $this->get($key);

        return $obj ?: null;
    }

    public function removeInstanceFromPool($key) {
        $this->delete($key);
    }

    public function clearInstancePool() {
        // ??
    }
}