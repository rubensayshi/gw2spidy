<?php

namespace GW2Spidy\Util;

use Predis\Client;

class RedisCacheHandler {
    static protected $instances = array();
    protected $enabled = true;
    protected $baseKey;
    protected $serialize;
    protected $gzip;
    protected $ttl;

    protected $client;

    const FLAG_NONE = 0; // bitmask
    const FLAG_COMPRESSION = 1; // bitmask
    const FLAG_SERIALIZE = 2; // bitmask

    public function __construct($key, $serialize = true, $ttl = false, $gzip = false) {
        $this->client = new Client();
        $this->baseKey = substr(md5(getAppEnv()->getEnv() . $key), 0, 10);
        $this->serialize = $serialize;
        $this->ttl = $ttl;
        $this->gzip = $gzip;
    }

    /**
     * @return RedisCacheHandler
     */
    static public function getInstance($key, $serialize = true, $ttl = false, $gzip = false) {
        if (!isset(static::$instances[$key][(int)$serialize][(int)$ttl][(int)$gzip])) {
            static::$instances[$key][(int)$serialize][(int)$ttl][(int)$gzip] = new static($key, $serialize, $ttl, $gzip);
        }

        return static::$instances[$key][(int)$serialize][(int)$ttl][(int)$gzip];
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    public function returnValue($val) {
        if ($val === null || $val === false || (!$this->gzip && !$this->serialize)) {
            return $val;
        }

        $flags = unpack("C", substr($val, 0, 1));
        $flags = current($flags);

        $val = substr($val, 1);

        if ($this->gzip && $this->hasFlag($flags, self::FLAG_COMPRESSION)) {
            $val = gzinflate($val);
        }

        if ($this->serialize && $this->hasFlag($flags, self::FLAG_SERIALIZE)) {
            $val = unserialize($val);
        }

        return $val;
    }

    public function prepareValue($val) {
        if ($val === null || $val === false || (!$this->gzip && !$this->serialize)) {
            return $val;
        }

        $flags = self::FLAG_NONE;

        if ($this->serialize && !is_string($val)) {
            $val = serialize($val);
            $this->setFlag($flags, self::FLAG_SERIALIZE);
        }

        if ($this->gzip) {
            $val = gzdeflate($val);
            $this->setFlag($flags, self::FLAG_COMPRESSION);
        }

        $flags = pack("C", $flags);

        return $flags . $val;
    }

    private function hasFlag($flags, $flag) {
        return ($flags & $flag) != 0;
    }

    private function setFlag(&$flags, $flag) {
        $flags |= $flag;
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

        return $this->client->incrby($this->getKey($key), $value);
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
