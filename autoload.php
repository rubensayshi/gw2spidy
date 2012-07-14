<?php

require __DIR__.'/vendor/symfony-class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerNamespace('GW2Spidy', __DIR__.'/src');

$loader->register();