<?php

use GW2Spidy\NewQueue\QueueHelper;

require dirname(__FILE__) . '/../autoload.php';

QueueHelper::getInstance()->getItemListingDBQueueManager()->purge();
QueueHelper::getInstance()->getItemDBQueueManager()->purge();
