<?php

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use GW2Spidy\DB\User;

use Symfony\Component\HttpFoundation\Request;

/**
 * ----------------------
 *  route /login
 * ----------------------
 */
$app->get("/login", function(Request $request) use ($app) {
    $app->setLoginActive();

    if ($app['security']->getToken() && ($user = $app['security']->getToken()->getUser()) && $user instanceof User) {
        return $app->redirect($app['url_generator']->generate('homepage'));
    }

    return $app['twig']->render('login.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})
->bind('login');

$app->match('/hybridauth', function(Request $request) {
    $config = dirname(__DIR__) . '/config/hybridauth-config.php';

    require_once dirname(__DIR__) . '/vendor/hybridauth/Hybrid/Auth.php';
    require_once dirname(__DIR__) . '/vendor/hybridauth/Hybrid/Endpoint.php';

    return Hybrid_Endpoint::process();
});

/**
 * ----------------------
 *  route /social_login
 * ----------------------
 */
$app->get("/social_login", function(Request $request) use ($app) {
    $config = dirname(__DIR__) . '/config/hybridauth-config.php';

    require_once dirname(__DIR__) . '/vendor/hybridauth/Hybrid/Auth.php';

    if (!($provider = $request->get('provider'))) {
        return $app->redirect($app['url_generator']->generate('login'));
    }

    try {
        $hybridAuth = new Hybrid_Auth($config);
        $adapter = $hybridAuth->authenticate($provider);

        $profile = $adapter->getUserProfile();

        var_dump($profile);
    } catch (\Exception $e) {
        var_dump($e);
    }


})
->bind('social_login');

/**
 * ----------------------
 *  route /register
 * ----------------------
 */
$app->match("/register", function(Request $request) use ($app) {
    $app->setLoginActive();

    $error    = null;
    $username = null;
    $email    = null;

    if ($request->getMethod() == 'POST') {
        if (!($username = $request->get('username'))) {
            $error = "Username is required";
        } else if (!($email = $request->get('email'))) {
            $error = "Email is required";
        }  else if (!preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9])+(\.[a-zA-Z0-9_-]+)+$/", $email)) {
            $error = "Email is invalid";
        } else if (!($password = $request->get('password'))) {
            $error = "Password is required";
        } else if (preg_match("/\s/", $password)) {
            $error = "Password can't contain whitespaces";
        } else if (!($password2 = $request->get('password2'))) {
            $error = "Repeating your password is required";
        } else if ($password != $password2) {
            $error = "Please repeat your password";
        }

        if (!$error) {
            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setRoles('USER_ROLE');

            // save encoded passwd
            $encoder = $app['security.encoder_factory']->getEncoder($user);
            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));

            if ($user->validate()) {
                $user->save();

                // force login
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $app['security']->setToken($token);

                return $app->redirect($app['url_generator']->generate('homepage'));
            } else {
                foreach ($user->getValidationFailures() as $failure) {
                    $error .= $failure->getMessage() . "\n";
                }
            }
        }
    }

    return $app['twig']->render('register.html.twig', array(
        'error'    => $error,
        'username' => $username,
        'email'    => $email,
    ));
})
->bind('register');
