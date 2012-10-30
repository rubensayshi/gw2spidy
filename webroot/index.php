<?php

/**
 * using Silex micro framework
 *  this file contains all routing and the 'controllers' using lambda functions
 */

use GW2Spidy\Util\Functions;

use GW2Spidy\Application;

use GW2Spidy\Twig\VersionedAssetsRoutingExtension;
use GW2Spidy\Twig\ItemListRoutingExtension;
use GW2Spidy\Twig\GW2MoneyExtension;
use GW2Spidy\Twig\GenericHelpersExtension;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

require dirname(__FILE__) . '/../autoload.php';

// initiate the application, check config to enable debug / sql logging when needed
$app = Application::getInstance();
$app['no_cache'] = false;

// register config provider
$app->register(new Igorw\Silex\ConfigServiceProvider(getAppConfig()));

// setup dev mode related stuff based on config
$app['sql_logging'] && $app->enableSQLLogging();

// register providers
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'    => dirname(__FILE__) . '/../templates',
    'twig.options' => array(
        'cache' => dirname(__FILE__) . '/../tmp/twig-cache',
    ),
));

// register custom twig extensions
$app['twig']->addExtension(new GenericHelpersExtension());
$app['twig']->addExtension(new VersionedAssetsRoutingExtension($app['url_generator']));
$app['twig']->addExtension(new GW2MoneyExtension());
$app['twig']->addExtension(new ItemListRoutingExtension($app['url_generator']));

/*
 * it's not very clean and silex-like but following are some includes to split up all the routing / functionality
 *  instead of using their mounting stuff, because it's just to much trouble
 */
$root = dirname(__DIR__);

// helper functions shared among the various others
require "{$root}/controllers/helpers.php";

// generic stuff like the homepage, etc
require "{$root}/controllers/other.php";

// type list, item lists, item page and chart
require "{$root}/controllers/items.php";

// recipe lists and recipe
require "{$root}/controllers/crafting.php";

// gem and chart
require "{$root}/controllers/gems.php";

// search
require "{$root}/controllers/search.php";

// api stuff
require "{$root}/controllers/api.php";

$app->after(function (Request $request, Response $response) use ($app) {
    if ($app['no_cache']) {
        $response->headers->set('X-Varnish-No-Cache', '1');
    }
});

// bootstrap the app
$app->run();
