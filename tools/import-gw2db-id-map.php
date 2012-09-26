<?php

ini_set('memory_limit', '1G');


require dirname(__FILE__) . '/../autoload.php';

if (!isset($argv[1]) || !($mapfilename = $argv[1])) {
    die('map file required.');
}

if (!file_exists($mapfilename)) {
    die('map file does not exist.');
}

$data = json_decode(file_get_contents($mapfilename), true);
$cnt  = count($data);

$stmt = Propel::getConnection()->prepare("UPDATE item SET gw2db_id = :gw2db_id, gw2db_external_id = :gw2db_external_id WHERE data_id = :data_id");

foreach ($data as $i => $row) {
    echo "[{$i} / {$cnt}] \n";

    $stmt->bindValue('gw2db_id', $row['ID']);
    $stmt->bindValue('gw2db_external_id', $row['ExternalID']);
    $stmt->bindValue('data_id', $row['DataID']);

    $stmt->execute();
}


