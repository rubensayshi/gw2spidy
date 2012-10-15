<?php

use Symfony\Component\HttpFoundation\Request;

/**
 * ----------------------
 *  route /login
 * ----------------------
 */
$app->get("/login", function(Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})
->bind('login');
