<?php

use GW2Spidy\TradeMarket\Item;
use GW2Spidy\TradeMarket\TradeMarket;
use GW2Spidy\Util\CurlRequest;
require dirname(__FILE__) . '/config.inc.php';
require dirname(__FILE__) . '/autoload.php';

$trademarket = TradeMarket::getInstance();

$copper = Item::getByExactName("Copper Ore");
var_dump($copper->getSellListings());

