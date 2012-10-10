<?php

namespace GW2Spidy\API;

use Silex\Application;
use Silex\ControllerProviderInterface;

class OldAPIControllerProvider extends BaseAPIControllerProvider {
    public function connect(Application $app) {
        $controllers = $app['controllers_factory'];

        $controllers->get('/types', function (Application $app) {
            return $app->redirect('/');
        });

        return $controllers;
    }
}