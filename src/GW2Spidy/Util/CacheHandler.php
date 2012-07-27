<?php

namespace GW2Spidy\Util;

use \Memcache;

class CacheHandler extends Memcache
{
    static protected $instances = array();
    protected $baseKey = null;

    static public function getInstance($key = null)
    {
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new self($key);
            $connected = self::$instances[$key]->connect();

            if (!$connected) {
                self::$instances[$key] = $connected;
            }
        }

        return self::$instances[$key];
    }

    protected function __construct($key) {
        $this->baseKey = $key;
    }

    protected function generateKey($key) {
        return "{$this->baseKey}::{$key}";
    }
}