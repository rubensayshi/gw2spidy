<?php

namespace GW2Spidy;

use GW2Spidy\DB\ItemTypeQuery;
use Symfony\Component\HttpFoundation\Request;

class Application extends \Silex\Application {
    protected $time;
    protected $homeActive = false;
    protected $displayTypes = null;

    public function __construct() {
        $this->time    = microtime(true);

        parent::__construct();
    }

    public function debugSQL() {
        $con = \Propel::getConnection();

        $con->setLogLevel(\Propel::LOG_DEBUG);
        $con->useDebug(true);
    }

    public function getTime() {
        return microtime(true) - $this->time;
    }

    public function setHomeActive($bool = true) {
        $this->homeActive = $bool;

        return $this;
    }

    public function isHomeActive() {
        return $this->homeActive;
    }

    public function isBrowseActive() {
        return !$this->isHomeActive();
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