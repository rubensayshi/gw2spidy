<?php

namespace GW2Spidy\API;

use GW2Spidy\DB\Recipe;

use \DateTime;
use \DateTimeZone;

use Symfony\Component\HttpFoundation\Request;

use GW2Spidy\DB\Item;

use Silex\Application;
use Silex\ControllerProviderInterface;

class APIHelperService {
    protected $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function outputResponse(Request $request, $response, $format, $name = null) {
        $this->outputHeaders($format, $name);
        if ($format == 'json') {
            return $this->outputResponseJSON($request, $response);
        } else if ($format == 'csv') {
            return $this->outputResponseCSV($request, $response);
        }
    }

    public function outputHeaders($format = 'json', $name = null) {
        $name = ($name ?: 'gw2spidy-api') . '_' . date('Ymd-His');

        if ($format == 'csv') {
            header('Content-type: text/csv');
        } else if ($format == 'json') {
            header('Content-type: application/json');
        }

        if (!$this->app['debug']) {
            header("Content-disposition: attachment; filename={$name}.{$format}");
        }
    }

    public function outputResponseCSV(Request $request, $response) {
        ob_start();

        if (isset($response['results'])) {
            $results = $response['results'];
        } else if (isset($response['result'])) {
            $results = array($response['result']);
        } else {
            throw new \Exception("Invalid response to output as CSV.");
        }

        if (count($results)) {
            echo implode(',', array_keys(reset($results))) . "\r\n";

            foreach ($results as $result) {
                echo implode(',', $result) . "\r\n";
            }
        }

        return ob_get_clean();
    }

    public function outputResponseJSON(Request $request, $response) {
        return json_encode($response);
    }

    public function buildItemDataArray(Item $item) {
        $data = array(
            'data_id' => $item->getDataId(),
            'name' => $item->getName(),
            'rarity' => $item->getRarity(),
            'restriction_level' => $item->getRestrictionLevel(),
            'img' => $item->getImg(),
            'type_id' => $item->getItemTypeId(),
            'sub_type_id' => $item->getItemSubTypeId(),
            'price_last_changed' => $this->dateAsUTCString($item->getLastPriceChanged()),
            'max_offer_unit_price' => $item->getMaxOfferUnitPrice(),
            'min_sale_unit_price' => $item->getMinSaleUnitPrice(),
            'offer_availability' => $item->getOfferAvailability(),
            'sale_availability' => $item->getSaleAvailability(),
            'gw2db_external_id' => $item->getGW2DBExternalId(),
            'sale_price_change_last_hour' => $item->getSalePriceChangeLastHour(),
        	'offer_price_change_last_hour' => $item->getOfferPriceChangeLastHour(),
        );

        return $data;
    }

    public function buildRecipeDataArray(Recipe $recipe) {
        $data = array(
            "date_id"              => $recipe->getDataId(),
            "name"                 => $recipe->getName(),
            "result_count"         => $recipe->getCount(),
        	"result_item_data_id"  => $recipe->getResultItemId(),
            "discipline_id"        => $recipe->getDisciplineId(),
            "result_item_max_offer_unit_price" => $recipe->getResultItem()->getMaxOfferUnitPrice(),
            "result_item_min_sale_unit_price"  => $recipe->getResultItem()->getMinSaleUnitPrice(),
            "crafting_cost"		   => $recipe->getCost(),
        );

        return $data;
    }

    public function dateAsUTCString($date) {
        $date = $date instanceof DateTime ? $date : new DateTime($date);
        $date->setTimezone(new DateTimeZone('UTC'));

        return "{$date->format("Y-m-d H:i:s")} UTC";
    }
}