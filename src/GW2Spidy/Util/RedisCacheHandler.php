<?php

namespace GW2Spidy\Util;

use Predis\Client;

class RedisCacheHandler {
    static protected $instances = array();
    protected $enabled = true;
    protected $baseKey;
    protected $serialize;

    protected $client;

    public function __construct($key, $serialize = true) {
        $this->client = new Client();
        $this->baseKey = substr(md5(getAppEnv()->getEnv() . $key), 0, 10);
        $this->serialize = $serialize;
    }

    /**
     * @return RedisCacheHandler
     */
    static public function getInstance($key, $serialize = true) {
        if (!isset(static::$instances[$key][(int)$serialize])) {
            static::$instances[$key][(int)$serialize] = new static($key, $serialize);
        }

        return static::$instances[$key][(int)$serialize];
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

    public function decrement($key, $value = 1) {
        if (!$this->getEnabled()) {
            return null;
        }

        if ($this->serialize) {
            throw new Exception("Can't decrement when in serialize mode!");
        }

        return $this->client->hincrby($this->baseKey, $key, -1 * $value);
    }
    public function delete($key) {
        if (!$this->getEnabled()) {
            return null;
        }

        return $this->client->hdel($this->baseKey, $key);
    }
    public function get($key) {
        if (!$this->getEnabled()) {
            return null;
        }

        return $this->returnValue($this->client->hget($this->baseKey, $key));

    }
    public function increment($key, $value = 1) {
        if (!$this->getEnabled()) {
            return null;
        }

        if ($this->serialize) {
            throw new Exception("Can't decrement when in serialize mode!");
        }

        return $this->client->hincrby($this->baseKey, $key, $var);
    }
    public function set($key, $var) {
        if (!$this->getEnabled()) {
            return null;
        }

        return $this->client->hset($this->baseKey, $key, $this->prepareValue($var));
    }
    public function purge() {
        if (!$this->getEnabled()) {
            return null;
        }

        return $this->client->del($this->baseKey);
    }
}