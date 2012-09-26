<?php

use GW2Spidy\Queue\RequestSlotManager;


require dirname(__FILE__) . '/../autoload.php';

RequestSlotManager::getInstance()->setup();
