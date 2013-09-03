<?php

namespace GW2Spidy\Security;

use GW2Spidy\DB\User;
use GW2Spidy\UserProvider;

use Silex\Application;
use Silex\Provider\SecurityServiceProvider;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;


class CustomSecurityServiceProvider implements ServiceProviderInterface {
    public function register(Application $app) {
        // register security provider
        $app->register(new SecurityServiceProvider(), array(
            'security.firewalls' => array(
                    'login' => array(
                            'pattern' => '^/login$',
                    ),
                    'main' => array(
                            'anonymous' => true,
                            'form'      => array('login_path' => '/login', 'check_path' => '/login_check'),
                            'logout'    => array('logout_path' => '/logout'),
                            'users'     => $app->share(function () use ($app) {
                                return new UserProvider();
                            }),
                            /*'users'     => array(
                             // raw password is foo
                                    'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
                                    'user'  => array('ROLE_USER',  '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
                            ),*/
                    ),
            ),
            'security.role_hierarchy' => array(
                    'ROLE_ADMIN' => array('ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH'),
                    'ROLE_USER' => array(),
            ),
            'security.access_rules' => array(
                    array('^/admin', 'ROLE_ADMIN'),
            ),
        ));

        $app['security.authentication.success_handler._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($name, $options, $app) {
                $handler = new CustomAuthenticationSuccessHandler(
                        $app['security.http_utils'],
                        $options
                );
                $handler->setProviderKey($name);

                return $handler;
            });
        });

        $app['security.authentication.logout_handler._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($name, $options, $app) {
                return new CustomLogoutSuccessHandler(
                        $app['security.http_utils'],
                        isset($options['target_url']) ? $options['target_url'] : '/'
                );
            });
        });

        // hit the security.firewall_map and the security so they initialize properly before Twig tries to use them in some odd way
        $app['security.firewall_map'];
        $app['security'];

        $app['user'] = $app->share(function() use ($app) {
            if (!$app['security']->getToken() || !($user = $app['security']->getToken()->getUser()) || !$user instanceof User) {
                return null;
            }

            return $user;
        });

        $app->before(function(Request $request) use ($app) {
            $app['isLoggedIn'] = $request->cookies->get('logged_in', null);

            $ignoreUrls = array('/login/', '/register/', '/hybridauth/', '/favicon/');
            if (!$request->isXmlHttpRequest() && $request->getMethod() == 'GET') {
                $ignore = false;
                foreach ($ignoreUrls as $i) {
                    if (preg_match($i, $request->getRequestUri())) {
                        $ignore = true;
                        break;
                    }
                }

                if (!$ignore) {
                    $app['session']->set('_security.main.target_path', $request->getRequestUri());
                }
            }
        });
    }

    public function boot(Application $app) {}

}