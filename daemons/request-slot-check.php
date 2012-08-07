<?php

use GW2Spidy\Queue\RequestSlotManager;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

RequestSlotManager::getInstance()->check();
