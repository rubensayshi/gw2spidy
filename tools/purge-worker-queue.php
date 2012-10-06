<?php

use GW2Spidy\Queue\QueueManager;


require dirname(__FILE__) . '/../autoload.php';

QueueManager::getInstance()->getItemListingsQueueManager()->purge();
QueueManager::getInstance()->getItemQueueManager()->purge();
