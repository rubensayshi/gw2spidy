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
class CacheHandler extends Memcache implements MemcacheReplacement
{
    static protected $instances = array();
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

    public function add($key, $var, $flag = null, $expire = null) {
        return parent::add($this->generateKey($key), $var, $flag, $expire);
    }
    public function decrement($key, $value = 1) {
        return parent::decrement($this->generateKey($key), $value);
    }
    public function delete($key, $timeout = 0) {
        return parent::delete($this->generateKey($key), $timeout);
    }
    public function get($key, &$flags = null) {
        return parent::get($this->generateKey($key), $flags);
    }
    public function increment($key, $value = 1) {
        return parent::increment($this->generateKey($key), $value);
    }
    public function replace($key, $var, $flag = null, $expire = null) {
        return parent::replace($this->generateKey($key), $var, $flag, $expire);
    }
    public function set($key, $var, $flag = null, $expire = null) {
        return parent::set($this->generateKey($key), $var, $flag, $expire);
    }
    public function purge() {
        parent::flush();
    }
}