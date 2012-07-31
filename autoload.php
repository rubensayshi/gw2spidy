<?php

// Include the main Propel script
require_once __DIR__.'/vendor/propel/runtime/lib/Propel.php';

require __DIR__.'/vendor/symfony-class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerNamespace('GW2Spidy', __DIR__.'/src');
$loader->registerNamespace('Predis',   __DIR__.'/vendor/nrk-predis/lib');

$loader->register();

// Initialize Propel with the runtime configuration
Propel::init(__DIR__ . "/config/gw2spidy-conf.php");

// Add the generated 'classes' directory to the include path
// set_include_path("/path/to/bookstore/build/classes" . PATH_SEPARATOR . get_include_path());