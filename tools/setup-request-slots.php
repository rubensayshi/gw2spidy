<?php

use GW2Spidy\NewQueue\RequestSlotManager;


require dirname(__FILE__) . '/../autoload.php';

RequestSlotManager::getInstance()->setup();

$count = getAppConfig("gw2spidy.request-slots.count");
$cooldown = getAppConfig("gw2spidy.request-slots.cooldown");
/**
 * 100 slots with 10 sec cooldown gives us :
 *     100 requests / 10 seconds = 10 requests / sec
 * this is excluding the time it takes to handle the slots
 */
$max = $count / $cooldown;
$maxH = $max * 60 * 60;
echo "set up slot manager (count={$count}, cooldown={$cooldown}, max-requests per seconds={$max} / per hour={$maxH})\n";
    