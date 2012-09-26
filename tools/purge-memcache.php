<?php

use GW2Spidy\Util\CacheHandler;


require dirname(__FILE__) . '/../autoload.php';

CacheHandler::getInstance("purge")->purge();