<?php

namespace GW2Spidy;

use \Exception;

use GW2Spidy\Util\CacheHandler;
use GW2Spidy\Util\CurlRequest;

use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemType;

abstract class BaseSpider {
    protected static $instance;
    protected $loggedIn;

    abstract protected function getLoginToUrl();

    public static function getInstance() {
        var_dump(get_called_class());
        var_dump(is_null(static::$instance));

        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function ensureLogin() {
        if (!$this->loggedIn) {
            $this->loggedIn = $this->doLogin();
        }
    }

    public function doLogin() {
        if ($sid = GW2LoginManager::getInstance()->getSessionID()) {
            $loginURL = $this->getLoginToUrl() . "/authenticate";
            $loginURL .= "?account_name=". urlencode("Guild Wars 2");
            $loginURL .= "&session_key={$sid}";

            $curl = CurlRequest::newInstance($loginURL)
                        ->exec();
        } else {
            throw new Exception("Login request failed, no SID.");
        }

        return true;
    }
}

?>
