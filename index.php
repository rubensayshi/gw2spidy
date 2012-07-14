<?php

use GW2Spidy\TradeMarket\TradeMarket;
use GW2Spidy\Util\CurlRequest;
require dirname(__FILE__) . '/config.inc.php';
require dirname(__FILE__) . '/autoload.php';

define('TRADINGPOST_URL', 'https://tradingpost-live.ncplatform.net/');
define('AUTH_URL', 'https://account.guildwars2.com/login?redirect_uri=http://tradingpost-live.ncplatform.net/authenticate?source=%2F&game_code=gw2');

$trademarket = TradeMarket::getInstance();

$copper = $trademarket->getItemByExactName("Copper Ore");
var_dump($trademarket->getListingsById($copper->data_id));

