<?php

use GW2Spidy\Util\CacheHandler;


require dirname(__FILE__) . '/../autoload.php';

CacheHandler::getInstance("purge")->purge();

if (function_exists('apc_clear_cache')) {
    apc_clear_cache();
}
