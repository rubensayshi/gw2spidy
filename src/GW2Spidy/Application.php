<?php

namespace GW2Spidy;

use GW2Spidy\DB\ItemTypeQuery;
use Symfony\Component\HttpFoundation\Request;

class Application extends \Silex\Application {
    protected $time;
    protected $homeActive = false;
    protected $gemActive = false;
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
        return $this['admin_secret'];
    }

    public function getVersionString() {
        return $this['version_string'] != 'dev' ? $this['version_string'] : null;
    }

    public function isDevMode() {
        return $this['dev_mode'];
    }

    public function isMemcachedEnabled() {
        return $this['memcached_enabled'];
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

    public function isBrowseActive() {
        return !$this->isHomeActive() && !$this->isGemActive();
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