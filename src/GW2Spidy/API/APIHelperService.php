<?php

namespace GW2Spidy\API;

use Symfony\Component\HttpFoundation\Response;

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

    public function outputResponse(Request $request, $response, $format, $name = null, $direct = false) {
        $r = new Response();

        if ($format == 'json') {
            $r->setContent($direct ? $response : $this->outputResponseJSON($request, $response));
            $r->headers->set("Content-type", "application/json", true);
        } else if ($format == 'csv') {
            $r->setContent($direct ? $response : $this->outputResponseCSV($request, $response));
            $r->headers->set("Content-type", "text/csv", true);
        } else {
            return $this->app->abort(500);
        }

        return $r;
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
        $json = json_encode($response);

        $jsonp = $request->get('jsonp') ?: $jsonp = $request->get('callback');

        return $jsonp ? "{$jsonp}({$json})" : $json;
    }

    public function buildItemDataArray(array $item) {
        $data = array(
            'data_id' => intval($item['DataId']),
            'name' => $item['Name'],
            'rarity' => intval($item['Rarity']),
            'restriction_level' => intval($item['RestrictionLevel']),
            'img' => $item['Img'],
            'type_id' => intval($item['ItemTypeId']),
            'sub_type_id' => intval($item['ItemSubTypeId']),
            'price_last_changed' => $this->dateAsUTCString($item['LastPriceChanged']),
            'max_offer_unit_price' => intval($item['MaxOfferUnitPrice']),
            'min_sale_unit_price' => intval($item['MinSaleUnitPrice']),
            'offer_availability' => intval($item['OfferAvailability']),
            'sale_availability' => intval($item['SaleAvailability']),
            'gw2db_external_id' => intval($item['Gw2dbExternalId']),
            'sale_price_change_last_hour' => intval($item['SalePriceChangeLastHour']),
        	'offer_price_change_last_hour' => intval($item['OfferPriceChangeLastHour']),
        );

        return $data;
    }

    public function buildRecipeDataArray(Recipe $recipe) {
        $data = array(
            "data_id"              => $recipe->getDataId(),
            "name"                 => $recipe->getName(),
            "result_count"         => $recipe->getCount(),
        	"result_item_data_id"  => $recipe->getResultItemId(),
            "discipline_id"        => $recipe->getDisciplineId(),
            "result_item_max_offer_unit_price" => $recipe->getResultItem()->getMaxOfferUnitPrice(),
            "result_item_min_sale_unit_price"  => $recipe->getResultItem()->getMinSaleUnitPrice(),
            "crafting_cost"		   => $recipe->getCost(),
            "rating"	     	   => $recipe->getRating(),
        );

        return $data;
    }

    public function dateAsUTCString($date) {
        $date = $date instanceof DateTime ? $date : new DateTime($date);
        $date->setTimezone(new DateTimeZone('UTC'));

        return "{$date->format("Y-m-d H:i:s")} UTC";
    }
}