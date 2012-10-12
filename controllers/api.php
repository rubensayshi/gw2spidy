<?php

use \DateTime;

use GW2Spidy\Application;
use Symfony\Component\HttpFoundation\Request;

use GW2Spidy\DB\DisciplineQuery;
use GW2Spidy\DB\ItemSubTypeQuery;
use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\RecipeQuery;
use GW2Spidy\DB\GW2Session;
use GW2Spidy\DB\GoldToGemRateQuery;
use GW2Spidy\DB\GemToGoldRateQuery;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\ItemTypeQuery;
use GW2Spidy\DB\SellListingQuery;
use GW2Spidy\DB\WorkerQueueItemQuery;
use GW2Spidy\DB\ItemPeer;
use GW2Spidy\DB\BuyListingPeer;
use GW2Spidy\DB\SellListingPeer;
use GW2Spidy\DB\BuyListingQuery;

use GW2Spidy\Util\Functions;

/**
 * ----------------------
 *  route /api
 * ----------------------
 */
$app->get("/api/{format}/{secret}", function($format, $secret) use($app) {
    $format = strtolower($format);

    // check if the secret is in the configured allowed api_secrets
    if (!in_array($secret, $app['gw2spidy']['api_secrets']) && !$app['debug']) {
        return $app->redirect("/");
    }

    Propel::disableInstancePooling();
    $items = ItemQuery::create()->find();

    if ($format == 'csv') {
        header('Content-type: text/csv');
        header('Content-disposition: attachment; filename=item_data_' . date('Ymd-His') . '.csv');

        ob_start();

        echo implode(",", ItemPeer::getFieldNames(BasePeer::TYPE_FIELDNAME)) . "\n";

        foreach ($items as $item) {
            echo implode(",", $item->toArray(BasePeer::TYPE_FIELDNAME)) . "\n";
        }

        return ob_get_clean();
    } else if ($format == 'json') {
        header('Content-type: application/json');
        header('Content-disposition: attachment; filename=item_data_' . date('Ymd-His') . '.json');

        $json = array();

        foreach ($items as $item) {
            $json[$item->getDataId()] = $item->toArray(BasePeer::TYPE_FIELDNAME);
        }

        return json_encode($json);
    }
})
->assert('format', 'csv|json')
->bind('api');

/**
 * ----------------------
 *  route /api/item
 * ----------------------
 */
$app->get("/api/listings/{dataId}/{type}/{format}/{secret}", function($dataId, $type, $format, $secret) use($app) {
    $format = strtolower($format);

    // check if the secret is in the configured allowed api_secrets
    if (!in_array($secret, $app['gw2spidy']['api_secrets']) && !$app['debug']) {
        return $app->redirect("/");
    }

    $item = ItemQuery::create()->findPK($dataId);

    if (!$item) {
        return $app->abort(404, "Page does not exist.");
    }

    $fields   = array();
    $listings = array();
    if ($type == 'sell') {
        $fields   = SellListingPeer::getFieldNames(BasePeer::TYPE_FIELDNAME);
        $listings = SellListingQuery::create()->findByItemId($item->getDataId());
    } else {
        $fields   = BuyListingPeer::getFieldNames(BasePeer::TYPE_FIELDNAME);
        $listings = BuyListingQuery::create()->findByItemId($item->getDataId());
    }

    if ($format == 'csv') {
        header('Content-type: text/csv');
        header('Content-disposition: attachment; filename=listings_data_' . $item->getDataId() . '_' . $type . '_' . date('Ymd-His') . '.csv');

        ob_start();

        echo implode(",", $fields) . "\n";

        foreach ($listings as $listing) {
        	$data = $listing->toArray(BasePeer::TYPE_FIELDNAME);

            $date = new DateTime("{$listing->getListingDatetime()}");
            $date->setTimezone(new DateTimeZone('UTC'));

            $data['listing_datetime'] = $date->format("Y-m-d H:i:s");

            echo implode(",", $data) . "\n";
        }

        return ob_get_clean();
    } else if ($format == 'json') {
        header('Content-type: application/json');
        header('Content-disposition: attachment; filename=listings_data_' . $item->getDataId() . '_' . $type . '_' . date('Ymd-His') . '.json');

        $json = array();

        foreach ($listings as $listing) {
            $json[$listing->getId()] = $listing->toArray(BasePeer::TYPE_FIELDNAME);

            $date = new DateTime("{$listing->getListingtime()}");
            $date->setTimezone(new DateTimeZone('UTC'));

            $json[$listing->getId()]['listing_datetime'] = $date->format("Y-m-d H:i:s");
        }

        return json_encode($json);
    }
})
->assert('dataId',  '\d+')
->assert('format', 'csv|json')
->assert('type',   'sell|buy')
->convert('dataId', $toInt)
->bind('api_item');


/**
 * ----------------------
 *  route /api/price
 * ----------------------
 */
$app->get("/api/price/{format}/{secret}", function(Request $request, $format, $secret) use($app) {
    $format = strtolower($format);

    // check if the secret is in the configured allowed api_secrets
    if (!in_array($secret, $app['gw2spidy']['api_secrets']) && !$app['debug']) {
        return $app->redirect("/");
    }

    $q = ItemQuery::create();

    if ($search = $request->get('search')) {
        $q->filterByName($search);
    } else if ($id = $request->get('id')) {
        $q->filterByDataId($id);
    } else {
        return $app->redirect("/");
    }

    $item = $q->findOne();

    if (!$item) {
        return $app->abort(404, "Item does not exist.");
    }

    if ($format == 'csv') {
        header('Content-type: text/csv');
        header('Content-disposition: attachment; filename=item_price_' . $item->getDataId() . '_' . date('Ymd-His') . '.csv');

        ob_start();

        echo implode(",", array('min_sale_unit_price', 'max_offer_unit_price', 'sale_availability', 'offer_availability')) . "\n";
        echo implode(",", array($item->getMinSaleUnitPrice(), $item->getMaxOfferUnitPrice(), $item->getSaleAvailability(), $item->getOfferAvailability())) . "\n";

        return ob_get_clean();
    } else if ($format == 'json') {
        header('Content-type: application/json');
        header('Content-disposition: attachment; filename=item_price' . $item->getDataId() . '_' . date('Ymd-His') . '.json');

        $json = array(
            'min_sale_unit_price'  => $item->getMinSaleUnitPrice(),
            'max_offer_unit_price' => $item->getMaxOfferUnitPrice(),
            'sale_availability'    => $item->getSaleAvailability(),
            'offer_availability'   => $item->getOfferAvailability(),
        );

        return json_encode($json);
    }
})
->assert('format', 'csv|json')
->assert('type',   'sell|buy')
->bind('api_price');

