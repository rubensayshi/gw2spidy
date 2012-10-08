<?php

use GW2Spidy\NewQueue\QueueHelper;

require dirname(__FILE__) . '/../autoload.php';

QueueManager::getInstance()->getItemListingDBQueueManager()->purge();
QueueManager::getInstance()->getItemDBQueueManager()->purge();
