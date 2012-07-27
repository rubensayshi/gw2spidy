<?php

namespace GW2Spidy\DB;

use GW2Spidy\Util\CacheHandler;

class MemcacheInstancePool extends CacheHandler
{
    const EXPIRES = 3600;

    public function addInstanceToPool($obj, $key) {
        $this->set($this->generateKey($key), $obj, MEMCACHE_COMPRESSED, self::EXPIRES);
    }

    public function getInstanceFromPool($key) {
        $this->get($this->generateKey($key));
    }

    public function removeInstanceFromPool($key) {
        $this->delete($this->generateKey($key));
    }

    public function clearInstancePool() {
        // ??
    }
}