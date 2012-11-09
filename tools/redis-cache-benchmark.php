<?php

use GW2Spidy\Dataset\GemExchangeDataset;

use GW2Spidy\Dataset\DatasetManager;

use GW2Spidy\Util\RedisCacheHandler;

require dirname(__FILE__) . '/../autoload.php';

$data = DatasetManager::getInstance()->getGemDataset(GemExchangeDataset::TYPE_GEM_TO_GOLD);
$plain = serialize($data);
$loops = 1000;

$oSerAndG = RedisCacheHandler::getInstance('ser_and_g', true, true);
$oSerNoG  = RedisCacheHandler::getInstance('ser_no_g',  true, false);
$oPlain   = RedisCacheHandler::getInstance('plain',     false, false);

$sSerAndG = $oSerAndG->prepareValue($data);
$sSerNoG = $oSerNoG->prepareValue($data);

echo "strlen \n";
echo "with gzip length    [[ " . strlen($sSerAndG) . " ]] \n";
echo "without gzip length [[ " .  strlen($sSerNoG) . " ]] \n";
echo "plain               [[ ------ ]] \n";

$time = microtime(true);
for ($i = 0; $i < $loops; $i++) {
    $oSerAndG->prepareValue($data);
}
$iSerAndG = microtime(true) - $time;

$time = microtime(true);
for ($i = 0; $i < $loops; $i++) {
    $oSerNoG->prepareValue($data);
}
$iSerNoG = microtime(true) - $time;

$time = microtime(true);
for ($i = 0; $i < $loops; $i++) {
    $oPlain->prepareValue($plain);
}
$iPlain = microtime(true) - $time;

echo "prep time \n";
echo "with gzip time    [[ " .  $iSerAndG . " ]] avg [[ " .  $iSerAndG / $loops . " ]] \n";
echo "without gzip time [[ " .  $iSerNoG .  " ]] avg [[ " .  $iSerNoG / $loops .  " ]] \n";
echo "plain             [[ " .  $iPlain .   " ]] avg [[ " .  $iPlain / $loops .   " ]] \n";

$time = microtime(true);
for ($i = 0; $i < $loops; $i++) {
    $oSerAndG->returnValue($sSerAndG);
}
$iSerAndG = microtime(true) - $time;

$time = microtime(true);
for ($i = 0; $i < $loops; $i++) {
    $oSerAndG->returnValue($sSerNoG);
}
$iSerNoG = microtime(true) - $time;

$time = microtime(true);
for ($i = 0; $i < $loops; $i++) {
    $oPlain->returnValue($plain);
}
$iPlain = microtime(true) - $time;

echo "return time \n";
echo "with gzip time    [[ " .  $iSerAndG . " ]] avg [[ " .  $iSerAndG / $loops . " ]] \n";
echo "without gzip time [[ " .  $iSerNoG .  " ]] avg [[ " .  $iSerNoG / $loops .  " ]] \n";
echo "plain             [[ " .  $iPlain .   " ]] avg [[ " .  $iPlain / $loops .   " ]] \n";

