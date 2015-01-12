<?php

use Symfony\Component\HttpFoundation\Cookie;

use GW2Spidy\DB\UserQuery;

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

/**
 * ----------------------
 *  route /hybridauth
 *  end point for the hybridauth lib to recieve callbacks on
 * ----------------------
 */
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
        return $app->redirect($app['url_generator']->generate('login', array('fail' => 'provider_not_found')));
    }

    try {
        $hybridAuth = new Hybrid_Auth($config);
        $adapter = $hybridAuth->authenticate($provider);

        $profile = $adapter->getUserProfile();

        $q = UserQuery::create()
                ->filterByHybridAuthProviderId($provider)
                ->filterByHybridAuthId($profile->identifier)
                ;

        $user = $q->findOne();

        if (!$user) {
            $base = "ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz123456789";
            $pass = "";
            for ($i = 0; $i < 10; $i++) {
                $pass .= $base{mt_rand(0, strlen($base)-1)};
            }

            $user = new User();
            $user->setHybridAuthProviderId($provider);
            $user->setHybridAuthId($profile->identifier);
            $user->setEmail($profile->email);
            $user->setUsername("{$provider}::{$profile->identifier}");
            $encoder = $app['security.encoder_factory']->getEncoder($user);
            $user->setPassword($encoder->encodePassword($pass, $user->getSalt()));

            $user->save();
        }

        // force login
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $app['security']->setToken($token);

        // $response = $app->redirect($app['session']->get('_security.main.target_path') ?: $app['url_generator']->generate('homepage'));
        $response = $app->redirect($app['url_generator']->generate('watchlist'));
        $app['session']->set('_security.main.target_path', '');
        $response->headers->setCookie(new Cookie('logged_in', true));

        return $response;
    } catch (\Exception $e) {
        // throw database errors, they are a real problem and not just a failed HybridAuth login
        if ($e instanceof PropelException) {
            throw $e;
        }

        return $app->redirect($app['url_generator']->generate('login', array('fail' => 'exception')));
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
        }  else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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

            // save encoded passwd
            $encoder = $app['security.encoder_factory']->getEncoder($user);
            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));

            if ($user->validate()) {
                $user->save();

                // force login
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $app['security']->setToken($token);

                $response = $app->redirect($app['url_generator']->generate('homepage'));
                $response->headers->setCookie(new Cookie('logged_in', true));

                return $response;
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


/**
 * ----------------------
 *  route /reset-password
 * ----------------------
 */
$app->get("/reset-password/{reset}", function(Request $request, $reset) use($app) {
    $app->setLoginActive();

    if (!$reset || !($user = UserQuery::create()->findOneByResetPassword($reset))) {
        return $app->redirect($app['url_generator']->generate('login'));
    }

    return $app['twig']->render('reset_password.html.twig', array(
        'reset' => $reset,
        'error' => '',
    ));
})
    ->bind('reset_password');

/**
 * ----------------------
 *  route /reset-password POST
 * ----------------------
 */
$app->post("/reset-password/{reset}", function(Request $request, $reset) use($app) {
    $app->setLoginActive();

    if (!$reset || !($user = UserQuery::create()->findOneByResetPassword($reset))) {
        return $app->redirect($app['url_generator']->generate('login'));
    }

    $error = null;

    if ($request->getMethod() == 'POST') {
        if (!($password = $request->get('password'))) {
            $error = "Password is required";
        } else if (preg_match("/\s/", $password)) {
            $error = "Password can't contain whitespaces";
        } else if (!($password2 = $request->get('password2'))) {
            $error = "Repeating your password is required";
        } else if ($password != $password2) {
            $error = "Please repeat your password";
        }

        if (!$error) {
            // save encoded passwd
            $encoder = $app['security.encoder_factory']->getEncoder($user);
            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
            $user->setResetPassword("");

            if ($user->validate()) {
                $user->save();

                // force login
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $app['security']->setToken($token);

                $response = $app->redirect($app['url_generator']->generate('homepage'));
                $response->headers->setCookie(new Cookie('logged_in', true));

                return $response;
            } else {
                foreach ($user->getValidationFailures() as $failure) {
                    $error .= $failure->getMessage() . "\n";
                }
            }
        }
    }

    return $app['twig']->render('reset_password.html.twig', array(
        'error' => $error,
        'reset' => $reset,
    ));
})
    ->bind('reset_password_post');
