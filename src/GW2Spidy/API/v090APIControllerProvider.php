<?php

namespace GW2Spidy\API;

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
use GW2Spidy\DB\BuyListingPeer;
use GW2Spidy\DB\SellListingPeer;
use GW2Spidy\DB\BuyListingQuery;


class v090APIControllerProvider extends BaseAPIControllerProvider {
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
        $controllers->get("/{format}/types", function($format) use($app) {
            $format = strtolower($format);

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
                header('Content-type: text/csv');

                ob_start();

                echo "id,name,parent_id\n";

                foreach ($results as $result) {
                    echo "{$result['id']},{$result['name']},\n";
                    foreach ($result['subtypes'] as $subresult) {
                        echo "{$subresult['id']},{$subresult['name']},{$result['id']}\n";
                    }
                }

                return ob_get_clean();
            } else if ($format == 'json') {
                header('Content-type: application/json');

                return json_encode($response);
            }
        })
        ->assert('format', 'csv|json');

        /**
         * ----------------------
         *  route /items
         * ----------------------
         */
        $controllers->get("/{format}/items/{type}/{page}", function($format, $type, $page) use($app) {
            $itemsperpage = 100;
            $format = strtolower($format);
            $page = $page > 0 ? $page : 1;

            $q = ItemQuery::create();

            if (in_array($type, array('all', '*all*'))) {
                $type = null;
            }
            if (!is_null($type)) {
                $type = ItemTypeQuery::create()->findPk($type);
                $q->filterByItemType($type);
            }

            $total = $q->count();

            if ($total > 0) {
                $lastpage = ceil($total / $itemsperpage);
                if ($page > $lastpage) {
                    $page = $lastpage;
                }
            } else {
                $page     = 1;
                $lastpage = 1;
            }

            $q->offset($itemsperpage * ($page-1))
              ->limit($itemsperpage);

            $count = $q->count();

            $results = array();
            foreach ($q->find() as $item) {
                $results[] = array(
                	'data_id' => $item->getDataId(),
                	'name' => $item->getName(),
                    'rarity' => $item->getRarity(),
                    'restriction_level' => $item->getRestrictionLevel(),
                    'img' => $item->getImg(),
                    'type_id' => $item->getItemTypeId(),
                    'sub_type_id' => $item->getSubItemTypeId(),
                    'price_last_changed' => $item->getPriceLastChanged("Y-m-d H:i:s UTC"),
                    'max_offer_unit_price' => $item->getMaxOfferUnitPrice(),
                    'min_sale_unit_price' => $item->getMinSaleUnitPrice(),
                    'offer_availability' => $item->getOfferAvailability(),
                    'sale_availability' => $item->getSaleAvailability(),
                );
            }

            $response = array('count' => $count, 'page' => $page, 'last_page' => $lastpage, 'total' => $total, 'results' => $results);

            if ($format == 'csv') {
                header('Content-type: text/csv');

                ob_start();

                if (isset($response['results'][0])) {
                    echo implode(',', array_keys($response['results'][0])) . "\n";

                    foreach ($results as $result) {
                        echo implode(',', $result) . "\n";
                    }
                }

                return ob_get_clean();
            } else if ($format == 'json') {
                header('Content-type: application/json');

                return json_encode($response);
            }
        })
        ->assert('format', 'csv|json')
        ->assert('type', '\d+|all')
        ->assert('page', '\d?');

        return $controllers;
    }
}