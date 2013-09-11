<?php

use GW2Spidy\API\APIHelperService;
use Symfony\Component\HttpFoundation\Request;

$app['api-helper'] = $app->share(function() use ($app) {
    return new APIHelperService($app);
});

$app->mount('/api', new GW2Spidy\API\OldAPIControllerProvider());
$app->mount('/api/v0.9', new GW2Spidy\API\v090APIControllerProvider());
