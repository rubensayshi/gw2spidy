<?php

namespace GW2Spidy;

use GW2Spidy\DB\ItemTypeQuery;
use Symfony\Component\HttpFoundation\Request;

class Application extends \Silex\Application {
    protected $time;
    protected $homeActive = false;
    protected $gemActive = false;
    protected $craftingActive = false;
    protected $displayTypes = null;

    protected static $instance;

    /**
     * @return Application
     */
    public static function getInstance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function __construct() {
        $this->time = microtime(true);

        parent::__construct();
    }

    public function getAdminSecret() {
        return defined('ADMIN_SECRET') ? ADMIN_SECRET : null;
    }

    public function getVersionString() {
        return defined('VERSION_STRING') ? VERSION_STRING : null;
    }

    public function isDevMode() {
        return defined('DEV_MODE') && DEV_MODE;
    }

    public function isSQLLogMode() {
        return defined('SQL_LOG_MODE') && SQL_LOG_MODE;
    }

    public function isMemcachedEnabled() {
        return !defined('MEMCACHED_DISABLED') || !MEMCACHED_DISABLED;
    }

    public function enableSQLLogging() {
        $con = \Propel::getConnection();

        $con->setLogLevel(\Propel::LOG_DEBUG);
        $con->useDebug(true);
    }

    public function getTime() {
        return microtime(true) - $this->time;
    }

    public function getMemUsage() {
        return (memory_get_peak_usage(true) / 1024  / 1024) . " MB";
    }

    public function setHomeActive($bool = true) {
        $this->homeActive = $bool;

        return $this;
    }

    public function isHomeActive() {
        return $this->homeActive;
    }

    public function setGemActive($bool = true) {
        $this->gemActive = $bool;

        return $this;
    }

    public function isGemActive() {
        return $this->gemActive;
    }

    public function setCraftingActive($bool = true) {
        $this->craftingActive = $bool;

        return $this;
    }

    public function isCraftingActive() {
        return $this->craftingActive;
    }

    public function isBrowseActive() {
        return !$this->isHomeActive() && !$this->isGemActive() && !$this->isCraftingActive();
    }

    public function getDisplayTypes() {
        if (is_null($this->displayTypes)) {
            $this->displayTypes = ItemTypeQuery::getAllTypes();

            foreach ($this->displayTypes as $k => $type) {
                if (!$type->getTitle()) {
                    unset($this->displayTypes[$k]);
                }
            }
        }

        return $this->displayTypes;
    }
}

?>