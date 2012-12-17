<?php

namespace GW2Spidy\Util;

use Predis\Client;

class RedisCacheHandler {
    static protected $instances = array();
    protected $enabled = true;
    protected $baseKey;
    protected $serialize;
    protected $ttl;

    protected $client;

    public function __construct($key, $serialize = true, $ttl = false) {
        $this->client = new Client();
        $this->baseKey = substr(md5(getAppEnv()->getEnv() . $key), 0, 10);
        $this->serialize = $serialize;
        $this->ttl = $ttl;
    }

    /**
     * @return RedisCacheHandler
     */
    static public function getInstance($key, $serialize = true, $ttl = false) {
        if (!isset(static::$instances[$key][(int)$serialize][(int)$ttl])) {
            static::$instances[$key][(int)$serialize][(int)$ttl] = new static($key, $serialize, $ttl);
        }

        return static::$instances[$key][(int)$serialize][(int)$ttl];
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    public function returnValue($val) {
        return $this->serialize ? unserialize($val) : $val;
    }

    public function prepareValue($val) {
        return $this->serialize ? serialize($val) : $val;
    }

    protected function getKey($key) {
        return "{$this->baseKey}::{$key}";
    }

    public function decrement($key, $value = 1) {
        if (!$this->getEnabled()) {
            return null;
        }

        if ($this->serialize) {
            throw new Exception("Can't decrement when in serialize mode!");
        }

        return $this->client->incrby($this->getKey($key), -1 * $value);
    }
    public function delete($key) {
        if (!$this->getEnabled()) {
            return null;
        }

        return $this->client->del($this->getKey($key));
    }
    public function get($key) {
        if (!$this->getEnabled()) {
            return null;
        }

        return $this->returnValue($this->client->get($this->getKey($key)));

    }
    public function increment($key, $value = 1) {
        if (!$this->getEnabled()) {
            return null;
        }

        if ($this->serialize) {
            throw new Exception("Can't decrement when in serialize mode!");
        }

        return $this->client->incrby($this->getKey($key), $var);
    }
    public function set($key, $var) {
        if (!$this->getEnabled()) {
            return null;
        }

        $r = $this->client->set($this->getKey($key), $this->prepareValue($var));

        if ($this->ttl) {
            $this->client->expire($this->getKey($key), $this->ttl);
        }

        return $r;
    }
    public function purge() {
        if (!$this->getEnabled()) {
            return null;
        }

        foreach ($this->client->keys($this->getKey("*")) as $key) {
            $this->client->del($key);
        }
    }
}
