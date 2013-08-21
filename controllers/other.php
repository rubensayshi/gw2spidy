<?php

use GW2Spidy\DB\UserQuery;

use \DateTime;
use \DateInterval;

use GW2Spidy\GW2SessionManager;
use GW2Spidy\NewQueue\RequestSlotManager;
use GW2Spidy\NewQueue\QueueHelper;

use Symfony\Component\HttpFoundation\Request;

use GW2Spidy\DB\GW2Session;
use GW2Spidy\DB\ItemQuery;


/**
 * ----------------------
 *  route /
 * ----------------------
 */
$app->get("/", function() use($app) {
    // workaround for now to set active menu item
    $app->setHomeActive();

    $onehourago = new DateTime();
    $onehourago->sub(new DateInterval('PT1H'));

    $trendingUp = ItemQuery::create()
                        ->filterByLastPriceChanged($onehourago, \Criteria::GREATER_EQUAL)
                        ->filterBySalePriceChangeLastHour(500, \Criteria::LESS_EQUAL)
                        ->filterBySaleAvailability(200, \Criteria::GREATER_EQUAL)
                        ->filterByOfferAvailability(200, \Criteria::GREATER_EQUAL)
                        ->addDescendingOrderByColumn("sale_price_change_last_hour")
                        ->limit(3)
                        ->find();

    $trendingDown = ItemQuery::create()
                        ->filterByLastPriceChanged($onehourago, \Criteria::GREATER_EQUAL)
                        ->filterBySalePriceChangeLastHour(500, \Criteria::LESS_EQUAL)
                        ->filterBySaleAvailability(200, \Criteria::GREATER_EQUAL)
                        ->filterByOfferAvailability(200, \Criteria::GREATER_EQUAL)
                        ->addAscendingOrderByColumn("sale_price_change_last_hour")
                        ->limit(3)
                        ->find();


    $summary = gem_summary();

    return $app['twig']->render('index.html.twig', array(
        'trending_up' => $trendingUp,
        'trending_down' => $trendingDown,

    ) + (array)$summary);
})
->bind('homepage');

/**
 * ----------------------
 *  route /faq
 * ----------------------
 */
$app->get("/faq", function() use($app) {
    $app->setFAQActive();

    return $app['twig']->render('faq.html.twig');
})
->bind('faq');

/**
 * ----------------------
 *  route /status
 * ----------------------
 */
$app->get("/status/", function() use($app) {
    ob_start();

    echo "there are [[ " . RequestSlotManager::getInstance()->getLength() . " ]] available slots right now \n";
    echo "there are [[ " . QueueHelper::getInstance()->getItemListingDBQueueManager()->getLength() . " ]] items in the item listings queue \n";
    echo "there are [[ " . QueueHelper::getInstance()->getItemDBQueueManager()->getLength() . " ]] items in the item DB queue \n";

    $content = ob_get_clean();

    return $app['twig']->render('status.html.twig', array(
        'dump' => $content,
    ));
})
->bind('status');

/**
 * ----------------------
 *  route /admin/session
 * ----------------------
 */
$app->get("/admin/session", function(Request $request) use($app) {
    // workaround for now to set active menu item
    $app->setHomeActive();

    return $app['twig']->render('admin_session.html.twig', array(
            'flash'    => $request->get('flash'),
    ));
})
->bind('admin_session');

/**
 * ----------------------
 *  route /admin/session POST
 * ----------------------
*/
$app->post("/admin/session", function(Request $request) use($app) {
    $session_key  = $request->get('session_key');
    $game_session = (boolean)$request->get('game_session');

    if (preg_match('/s=(.+)/', $session_key, $a)) {
        $session_key = $a[1];
    }

    $gw2session = new GW2Session();
    $gw2session->setSessionKey($session_key);
    $gw2session->setGameSession($game_session);
    $gw2session->setCreated(new DateTime());

    try {
        try {
            $ok = GW2SessionManager::getInstance()->checkSessionAlive($gw2session);
        } catch (Exception $e) {
            $gw2session->save();
            return $app->redirect($app['url_generator']->generate('admin_session', array('flash' => "tpdown")));
        }

        if ($ok) {
            $gw2session->save();
            return $app->redirect($app['url_generator']->generate('admin_session', array('flash' => "ok")));
        } else {
            return $app->redirect($app['url_generator']->generate('admin_session', array('flash' => "dead")));
        }
    } catch (PropelException $e) {
        if (strstr($e->getMessage(), "Duplicate")) {
            return $app->redirect($app['url_generator']->generate('admin_session', array('flash' => "duplicate")));
        } else {
            throw $e;
        }
    }
})
->bind('admin_session_post');

/**
 * ----------------------
 *  route /admin/password
 * ----------------------
 */
$app->get("/admin/password", function(Request $request) use($app) {
    // workaround for now to set active menu item
    $app->setHomeActive();

    return $app['twig']->render('admin_password.html.twig', array(
            'flash'    => $request->get('flash'),
    ));
})
->bind('admin_password');

/**
 * ----------------------
 *  route /admin/password POST
 * ----------------------
*/
$app->post("/admin/password", function(Request $request) use($app) {
    $user_id  = $request->get('user_id');
    $password = $request->get('password');

    $user = UserQuery::create()->findPk($user_id);

    if (!$user) {
        return $app->redirect($app['url_generator']->generate('admin_password', array('flash' => "no_user")));
    }

    $encoder = $app['security.encoder_factory']->getEncoder($user);
    $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
    $user->save();

    return $app->redirect($app['url_generator']->generate('admin_password', array('flash' => "ok")));
})
->bind('admin_password_post');

/**
 * ----------------------
 *  route /profit
 * ----------------------
 */
$app->get("/profit", function(Request $request) use($app) {
    $where = "";

    if ($minlevel = intval($request->get('minlevel'))) {
        $where .= " AND (restriction_level = 0 OR restriction_level >= {$minlevel})";
    }

    $margin     = intval($request->get('margin')) ?: 500;
    $max_margin = intval($request->get('max_margin')) ?: 1000;

    if ($minprice = intval($request->get('minprice'))) {
        $where .= " AND min_sale_unit_price >= {$minprice}";
    }

    if ($maxprice = intval($request->get('maxprice'))) {
        $where .= " AND min_sale_unit_price <= {$maxprice}";
    }

    if ($type = intval($request->get('type'))) {
        $where .= " AND item_type_id = {$type}";
    }

    if ($subtype = intval($request->get('subtype'))) {
        $where .= " AND item_sub_type_id = {$subtype}";
    }

    if ($blacklist = $request->get('blacklist')) {
        foreach (explode(",", $blacklist) as $blacklist) {
            $blacklist = Propel::getConnection()->quote("%{$blacklist}%", PDO::PARAM_STR);
            $where .= " AND name NOT LIKE {$blacklist}";
        }
    }

    $offset = intval($request->get('offset')) ?: 0;
    $limit  = intval($request->get('limit'))  ?: 50;
    $mmod   = intval($request->get('mmod')) ?: 0;
    $mnum   = intval($request->get('mnum')) ?: 0;

    $stmt = Propel::getConnection()->prepare("
    SELECT
        data_id,
        name,
        min_sale_unit_price,
        max_offer_unit_price,
        sale_availability,
        offer_availability,
        ((min_sale_unit_price*0.85 - max_offer_unit_price) / max_offer_unit_price) * 100 as margin
    FROM item
    WHERE offer_availability > 1
    AND   sale_availability > 5
    AND   max_offer_unit_price > 0
    AND   ((min_sale_unit_price*0.85 - max_offer_unit_price) / max_offer_unit_price) * 100 < {$max_margin}
    AND   (min_sale_unit_price*0.85 - max_offer_unit_price) > {$margin}
    {$where}
    ORDER BY margin DESC
    LIMIT {$offset}, {$limit}");

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($mmod) {
        $i = 0;
        foreach ($data as $k => $v) {
            if ($i % $mmod != $mnum) {
                unset($data[$k]);
            }

            $i ++;
        }
    }

    if ($request->get('asJson')) {
        $json = array();

        foreach ($data as $row) {
            $json[] = $row['data_id'];
        }

        return json_encode($json);
    } else {
        return $app['twig']->render('quick_table.html.twig', array(
            'headers' => array_keys(reset($data)),
            'data'    => $data,
        ));
    }
});

