<?php

use GW2Spidy\DB\RequestFloodControl;
use GW2Spidy\DB\RequestFloodControlPeer;
use GW2Spidy\DB\RequestFloodControlQuery;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$flood = isset($argv[1]) ? $argv[1] : SLOTS;
$count = RequestFloodControlQuery::create()->count();

if ($count > $flood) {
    $query = RequestFloodControlQuery::create()
                    ->orderBy(RequestFloodControlPeer::TOUCHED, Criteria::ASC)
                    ->limit($count - $flood)
                    ->find()
                    ->delete();
} else if ($count < $flood) {
    for ($i = $count; $i < $flood; $i++) {
        $new = new RequestFloodControl();
        $new->save();
    }
} else {
    // -- fine
}
