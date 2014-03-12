<?php

namespace GW2Spidy\Util;

use GW2Spidy\Application;

use \Memcache;

/**
 *
 * @TODO memcache will emit "PHP Warning:  MemcachePool::get(): No servers added to memcache connection"
 *  when there are no connections (if we've disabled memcache), it's half decent workaround for now ... but would be better to check for it ...
 *
 */
class CacheHandler extends Memcache implements MemcacheReplacement {
    static protected $instances = array();
    protected $enabled = true;
    protected $baseKey = null;

    /**
     *
     * @return CacheHandler
     */
    static public function getInstance($key) {
        if (!isset(static::$instances[$key])) {
            static::$instances[$key] = new static($key);

            if (Application::getInstance()->isMemcachedEnabled()) {
                static::$instances[$key]->connect('localhost');
            } else {
                static::$instances[$key]->setEnabled(false);
            }
        }

        return static::$instances[$key];
    }

    public function __construct($key) {
        $this->baseKey = substr(md5(getAppEnv()->getEnv() . $key), 0, 10);
    }

    protected function generateKey($key) {
        return "{$this->baseKey}::{$key}";
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    public function add($key, $var, $flag = null, $expire = null) {
        if (!$this->getEnabled()) {
            return null;
        }

        return parent::add($this->generateKey($key), $var, $flag, $expire);
    }
    public function decrement($key, $value = 1) {
        if (!$this->getEnabled()) {
            return null;
        }

        return parent::decrement($this->generateKey($key), $value);
    }
    public function delete($key, $timeout = 0) {
        if (!$this->getEnabled()) {
            return null;
        }

        return parent::delete($this->generateKey($key), $timeout);
    }
    public function get($key, &$flags = null) {
        if (!$this->getEnabled()) {
            return null;
        }

        return parent::get($this->generateKey($key), $flags);
    }
    public function increment($key, $value = 1) {
        if (!$this->getEnabled()) {
            return null;
        }

        return parent::increment($this->generateKey($key), $value);
    }
    public function replace($key, $var, $flag = null, $expire = null) {
        if (!$this->getEnabled()) {
            return null;
        }

        return parent::replace($this->generateKey($key), $var, $flag, $expire);
    }
    public function set($key, $var, $flag = null, $expire = null) {
        if (!$this->getEnabled()) {
            return null;
        }

        return parent::set($this->generateKey($key), $var, $flag, $expire);
    }
    public function purge() {
        if (!$this->getEnabled()) {
            return null;
        }

        return parent::flush();
    }
}