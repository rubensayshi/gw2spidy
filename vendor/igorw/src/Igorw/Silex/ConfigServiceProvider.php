<?php

namespace Igorw\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface {
    protected $cnf = null;

    public function __construct(Config $cnf) {
        $this->cnf = $cnf;
    }

    public function register(Application $app) {
        $config = $this->cnf->getConfig();

        foreach ($config as $name => $value) {
            $app[$name] = $value;
        }
    }

    public function boot(Application $app) {}
}
