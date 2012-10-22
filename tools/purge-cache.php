<?php

use GW2Spidy\Dataset\DatasetManager;

use GW2Spidy\Util\CacheHandler;


require dirname(__FILE__) . '/../autoload.php';

// memcache is completely purged this way
CacheHandler::getInstance("purge")->purge();

DatasetManager::getInstance()->purgeCache();

if (function_exists('apc_clear_cache')) {
    apc_clear_cache();
}
