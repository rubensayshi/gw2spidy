<?php

namespace GW2Spidy\Util;

interface MemcacheReplacement {
    public function add($key, $var, $flag = null, $expire = null);
    public function decrement($key, $value = 1);
    public function delete($key, $timeout = 0);
    public function get($key, &$flags = null);
    public function increment($key, $value = 1);
    public function replace($key, $var, $flag = null, $expire = null);
    public function set($key, $var, $flag = null, $expire = null);
}