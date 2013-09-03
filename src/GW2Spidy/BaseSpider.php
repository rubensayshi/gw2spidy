<?php

namespace GW2Spidy;

use GW2Spidy\Util\Singleton;

use \Exception;

use GW2Spidy\Util\CacheHandler;
use GW2Spidy\Util\CurlRequest;

use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemType;

abstract class BaseSpider extends Singleton {
    protected $gw2session;

    public function getSession() {
        if (is_null($this->gw2session)) {
            $this->gw2session = GW2SessionManager::getInstance();
        }

        return $this->gw2session;
    }

    public function getSessionKey() {
        $this->getSession()->getSessionKey();
    }

    public function setSession(GW2SessionManager $gw2session) {
        $this->gw2session = $gw2session;
    }
}

?>
