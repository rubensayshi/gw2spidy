<?php

namespace GW2Spidy\Util;

use \Memcache;

class CacheHandler extends Memcache
{
    static protected $instances = array();
    protected $baseKey = null;

    static public function getInstance($key = null)
    {
        if (!isset(static::$instances[$key])) {
            static::$instances[$key] = new static($key);
            $connected = static::$instances[$key]->connect('localhost');

            if (!$connected) {
                static::$instances[$key] = $connected;
            }
        }

        return static::$instances[$key];
    }

    protected function __construct($key) {
        $this->baseKey = $key;
    }

    protected function generateKey($key) {
        return "{$this->baseKey}::{$key}";
    }
}