<?php

use GW2Spidy\Util\CacheHandler;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

CacheHandler::getInstance("purge")->purge();