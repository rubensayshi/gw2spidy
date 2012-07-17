<?php

use GW2Spidy\QueueManager\QueueManager;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$spider = new QueueManager();
$spider->fillQueue();