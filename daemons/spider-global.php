<?php

use GW2Spidy\Spider\ItemDBSpider;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$spider = new ItemDBSpider();
$spider->run();
