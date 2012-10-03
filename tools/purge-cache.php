<?php

use GW2Spidy\Util\CacheHandler;


require dirname(__FILE__) . '/../autoload.php';

var_dump(CacheHandler::getInstance("purge")->purge());

if (function_exists('apc_clear_cache')) {
    apc_clear_cache();
}
