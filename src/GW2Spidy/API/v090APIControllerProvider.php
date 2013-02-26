<?php

namespace GW2Spidy\API;

use Symfony\Component\HttpFoundation\Request;

use Silex\Application;
use Silex\ControllerProviderInterface;

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
use GW2Spidy\DB\RecipePeer;
use GW2Spidy\DB\BuyListingPeer;
use GW2Spidy\DB\SellListingPeer;
use GW2Spidy\DB\BuyListingQuery;


class v090APIControllerProvider implements ControllerProviderInterface {
    public function connect(Application $app) {
        $toInt = function($val) {
            return (int) $val;
        };

        $controllers = $app['controllers_factory'];

        /**
         * ----------------------
         *  route /types
         * ----------------------
         */
        $controllers->get("/{format}/types", function(Request $request, $format) use($app) {

            $results = array();
            foreach (ItemTypeQuery::getAllTypes() as $type) {
                $result = array('id' => $type->getId(), 'name' => $type->getTitle(), 'subtypes' => array());

                foreach ($type->getSubTypes() as $subtype) {
                    $result['subtypes'][] = array('id' => $subtype->getId(), 'name' => $subtype->getTitle());
                }
                $results[] = $result;
            }

            $response = array('results' => $results);

            if ($format == 'csv') {
                ob_start();

                echo "id,name,parent_id\r\n";

                foreach ($results as $result) {
                    echo "{$result['id']},{$result['name']},\r\n";
                    foreach ($result['subtypes'] as $subresult) {
                        echo "{$subresult['id']},{$subresult['name']},{$result['id']}\r\n";
                    }
                }

                return $app['api-helper']->outputResponse($request, ob_get_clean(), $format, "types", true);
            } else {
                return $app['api-helper']->outputResponse($request, $response, $format, "types");
            }

        })
        ->assert('format', 'csv|json|xml');

        /**
         * ----------------------
         *  route /disciplines
         * ----------------------
         */
        $controllers->get("/{format}/disciplines", function(Request $request, $format) use($app) {

            $results = array();
            foreach (DisciplineQuery::getAllDisciplines() as $disc) {
                $results[] = array('id' => $disc->getId(), 'name' => $disc->getName());
            }

            $response = array('results' => $results);

            return $app['api-helper']->outputResponse($request, $response, $format, "disciplines");
        })
        ->assert('format', 'csv|json|xml');

        /**
         * ----------------------
         *  route /rarities
         * ----------------------
         */
        $controllers->get("/{format}/rarities", function(Request $request, $format) use($app) {

            $results = array(
                array("id" => 0, "name" => "Junk"),
                array("id" => 1, "name" => "Common"),
                array("id" => 2, "name" => "Fine"),
                array("id" => 3, "name" => "Masterwork"),
                array("id" => 4, "name" => "Rare"),
                array("id" => 5, "name" => "Exotic"),
                array("id" => 6, "name" => "Legendary"),
            );

            $response = array('results' => $results);

            return $app['api-helper']->outputResponse($request, $response, $format, "rarities");
        })
        ->assert('format', 'csv|json|xml');

        /**
         * ----------------------
         *  route /all-items
         * ----------------------
         */
        $controllers->match("/{format}/all-items/{typeId}", function(Request $request, $format, $typeId) use($app) {
            $t = microtime(true);
            $q = ItemQuery::create()->select(ItemPeer::getFieldNames(\BasePeer::TYPE_PHPNAME));

            if (in_array($typeId, array('all', '*all*'))) {
                $typeId = null;
            }
            if (!is_null($typeId)) {
                if (!($type = ItemTypeQuery::create()->findPk($typeId))) {
                    return $app->abort(404, "Invalid type [{$typeId}]");
                }

                $q->filterByItemType($type);
            }

            $count = $q->count();

            $results = array();
            foreach ($q->find() as $item) {
                $results[] = $app['api-helper']->buildItemDataArray($item);
            }

            $response = array(
                'count'     => $count,
                'results'   => $results
            );

            return $app['api-helper']->outputResponse($request, $response, $format, "all-items-{$typeId}");
        })
        ->assert('format', 'csv|json|xml')
        ->assert('typeId', '\d+|\*?all\*?');

        /**
         * ----------------------
         *  route /items
         * ----------------------
         */
        $controllers->match("/{format}/items/{typeId}/{page}", function(Request $request, $format, $typeId, $page) use($app) {

            $itemsperpage = 100;
            $page = intval($page > 0 ? $page : 1);

            $q = ItemQuery::create()->select(ItemPeer::getFieldNames(\BasePeer::TYPE_PHPNAME));

            if (in_array($typeId, array('all', '*all*'))) {
                $typeId = null;
            }
            if (!is_null($typeId)) {
                if (!($type = ItemTypeQuery::create()->findPk($typeId))) {
                    return $app->abort(404, "Invalid type [{$typeId}]");
                }

                $q->filterByItemType($type);
            }

            if (($sortTrending = $request->get('sort_trending')) && in_array($sortTrending, array('sale', 'offer'))) {
                $q->filterBySaleAvailability(200, \Criteria::GREATER_THAN);
                $q->filterByOfferAvailability(200, \Criteria::GREATER_THAN);
                if ($sortTrending == 'sale') {
                    $q->orderBySalePriceChangeLastHour(\Criteria::DESC);
                } else if ($sortTrending == 'offer') {
                    $q->orderByOfferPriceChangeLastHour(\Criteria::DESC);
                }
            }

            if ($filterIds = $request->get('filter_ids')) {
                $filterIds = array_unique(array_filter(array_map('intval', explode(",", $filterIds))));

                if (count($filterIds) > $itemsperpage) {
                    return $app->abort(400, "More IDs in filter_ids than allowed.");
                }

                $q->filterByDataId($filterIds, \Criteria::IN);
            }

            $total = $q->count();

            if ($total > 0) {
                $lastpage = ceil($total / $itemsperpage);
            } else {
                $page     = 1;
                $lastpage = 1;
            }

            $q->offset($itemsperpage * ($page-1))
              ->limit($itemsperpage);

            $count = $q->count();

            $results = array();
            foreach ($q->find() as $item) {
                $results[] = $app['api-helper']->buildItemDataArray($item);
            }

            $response = array(
                'count'     => $count,
                'page'      => $page,
                'last_page' => $lastpage,
                'total'     => $total,
                'results'   => $results
            );

            return $app['api-helper']->outputResponse($request, $response, $format, "items-{$typeId}-{$page}");
        })
        ->assert('format', 'csv|json|xml')
        ->assert('typeId', '\d+|\*?all\*?')
        ->assert('page', '\d*');

        /**
         * ----------------------
         *  route /item
         * ----------------------
         */
        $controllers->get("/{format}/item/{dataId}", function(Request $request, $format, $dataId) use($app) {
            $q = ItemQuery::create()->select(ItemPeer::getFieldNames(\BasePeer::TYPE_PHPNAME));
            $q->filterByPrimaryKey($dataId);
            if (!($item = $q->findOne())) {
                return $app->abort(404, "Item Not Found [{$dataId}].");
            }

            $response = array('result' => $app['api-helper']->buildItemDataArray($item));

            return $app['api-helper']->outputResponse($request, $response, $format, "item-{$dataId}");
        })
        ->assert('format', 'csv|json|xml')
        ->assert('dataId', '\d+');

        /**
         * ----------------------
         *  route /listings
         * ----------------------
         */
        $controllers->get("/{format}/listings/{dataId}/{type}/{page}", function(Request $request, $format, $dataId, $type, $page) use($app) {

            $itemsperpage = 1000;
            $page = intval($page > 0 ? $page : 1);

            $fields   = array();
            $listings = array();
            if ($type == 'sell') {
                $q = SellListingQuery::create()->select(SellListingPeer::getFieldNames(\BasePeer::TYPE_PHPNAME));
            } else {
                $q = BuyListingQuery::create()->select(BuyListingPeer::getFieldNames(\BasePeer::TYPE_PHPNAME));
            }

            $q->filterByItemId($dataId);

            $q->orderByListingDatetime(\ModelCriteria::DESC);

            $total = $q->count();

            if ($total > 0) {
                $lastpage = ceil($total / $itemsperpage);
            } else {
                $page     = 1;
                $lastpage = 1;
            }

            $q->offset($itemsperpage * ($page-1))
              ->limit($itemsperpage);

            $count   = 0;
            $results = array();
            foreach ($q->find() as $listing) {
                $results[] = $app['api-helper']->buildListingDataArray($listing);

                $count++;
            }

            $response = array(
                'sell-or-buy' => $type,
                'count'       => $count,
                'page'        => $page,
                'last_page'   => $lastpage,
                'total'       => $total,
                'results'     => $results
            );

            return $app['api-helper']->outputResponse($request, $response, $format, "item-listings-{$dataId}-{$type}-{$page}");
        })
        ->assert('dataId', '\d+')
        ->assert('format', 'csv|json|xml')
        ->assert('page',   '\d*')
        ->assert('type',   'sell|buy');

        /**
         * ----------------------
         *  route /item-search
         * ----------------------
         */
        $controllers->get("/{format}/item-search/{name}/{page}", function(Request $request, $format, $name, $page) use($app) {
            $itemsperpage = 50;
            $page = intval($page > 0 ? $page : 1);

            $q = ItemQuery::create()->select(ItemPeer::getFieldNames(\BasePeer::TYPE_PHPNAME));
            $q->filterByName("%{$name}%");

            if ($q->count() == 0 && $name != trim($name)) {
                $name = trim($name);
                $q = ItemQuery::create()->select(ItemPeer::getFieldNames(\BasePeer::TYPE_PHPNAME));
                $q->filterByName("%{$name}%");
            }

            $total = $q->count();

            if ($total > 0) {
                $lastpage = ceil($total / $itemsperpage);
            } else {
                $page     = 1;
                $lastpage = 1;
            }

            $q->offset($itemsperpage * ($page-1))
              ->limit($itemsperpage)
              ->addAscendingOrderByColumn('name');

            $count = $q->count();

            $results = array();
            foreach ($q->find() as $item) {
                $results[] = $app['api-helper']->buildItemDataArray($item);
            }

            $response = array(
                'count'     => $count,
                'page'      => $page,
                'last_page' => $lastpage,
                'total'     => $total,
                'results'   => $results
            );

            return $app['api-helper']->outputResponse($request, $response, $format, "item-search-{$page}");
        })
        ->assert('format', 'csv|json|xml')
        ->assert('page', '\d*');

        /**
         * ----------------------
         *  route /recipes
         * ----------------------
         */
        $controllers->get("/{format}/recipes/{discId}/{page}", function(Request $request, $format, $discId, $page) use($app) {

            $itemsperpage = 100;
            $page = intval($page > 0 ? $page : 1);

            $q = RecipeQuery::create();

            if (in_array($discId, array('all', '*all*'))) {
                $discId = null;
            }
            if (!is_null($discId)) {
                if (!($disc = DisciplineQuery::create()->findPk($discId))) {
                    return $app->abort(404, "Invalid discipline [{$discId}]");
                }

                $q->filterByDiscipline($disc);
            }

            $total = $q->count();

            if ($total > 0) {
                $lastpage = ceil($total / $itemsperpage);
            } else {
                $page     = 1;
                $lastpage = 1;
            }

            $q->offset($itemsperpage * ($page-1))
              ->limit($itemsperpage);

            $count = $q->count();

            $results = array();
            foreach ($q->find() as $recipe) {
                $results[] = $app['api-helper']->buildRecipeDataArray($recipe);
            }

            $response = array(
                'count'     => $count,
                'page'      => $page,
                'last_page' => $lastpage,
                'total'     => $total,
                'results'   => $results
            );

            return $app['api-helper']->outputResponse($request, $response, $format, "recipes-{$discId}-{$page}");
        })
        ->assert('format', 'csv|json|xml')
        ->assert('discId', '\d+|\*?all\*?')
        ->assert('page', '\d*');

        /**
         * ----------------------
         *  route /all-recipes
         * ----------------------
         */
        $controllers->match("/{format}/all-recipes/{discId}", function(Request $request, $format, $discId) use($app) {
            $t = microtime(true);
            $q = RecipeQuery::create();

            if (in_array($discId, array('all', '*all*'))) {
                $discId = null;
            }
            if (!is_null($discId)) {
                if (!($disc = DisciplineQuery::create()->findPk($discId))) {
                    return $app->abort(404, "Invalid discipline [{$discId}]");
                }

                $q->filterByDiscipline($disc);
            }

            $count = $q->count();

            $results = array();
            foreach ($q->find() as $recipe) {
                $results[] = $app['api-helper']->buildRecipeDataArray($recipe);
            }

            $response = array(
                'count'     => $count,
                'results'   => $results
            );

            return $app['api-helper']->outputResponse($request, $response, $format, "all-recipes-{$discId}");

        })
        ->assert('format', 'csv|json|xml')
        ->assert('typeId', '\d+|\*?all\*?');

        /**
         * ----------------------
         *  route /recipe
         * ----------------------
         */
        $controllers->get("/{format}/recipe/{dataId}", function(Request $request, $format, $dataId) use($app) {

            if (!($recipe = RecipeQuery::create()->findPk($dataId))) {
                return $app->abort(404, "Recipe Not Found [{$dataId}].");
            }

            $response = array('result' => $app['api-helper']->buildRecipeDataArray($recipe));

            return $app['api-helper']->outputResponse($request, $response, $format, "recipe-{$dataId}");
        })
        ->assert('format', 'csv|json|xml')
        ->assert('dataId', '\d+');

        /**
         * ----------------------
         *  route /gem-price
         * ----------------------
         */
        $controllers->get("/{format}/gem-price", function(Request $request, $format) use($app) {
            $gemtogold = GemToGoldRateQuery::create()
                        ->addDescendingOrderByColumn("rate_datetime")
                        ->offset(-1)
                        ->limit(1)
                        ->findOne();

            $goldtogem = GoldToGemRateQuery::create()
                        ->addDescendingOrderByColumn("rate_datetime")
                        ->offset(-1)
                        ->limit(1)
                        ->findOne();


            if (!$gemtogold || !$goldtogem) {
                return $app->abort(404, "Gem Data Not Found.");
            }

            $response = array('result' => array('gem_to_gold' => $gemtogold->getRate(), 'gold_to_gem' => $goldtogem->getRate()));

            return $app['api-helper']->outputResponse($request, $response, $format, "gem-price");
        })
        ->assert('format', 'csv|json|xml');

        return $controllers;
    }
}
