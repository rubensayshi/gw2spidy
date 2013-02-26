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
        } else if ($format == 'xml') {
            $r->setContent($direct ? $response : $this->outputResponseXML($request, $response, $name));
            $r->headers->set("Content-type", "application/xml", true);
        } else {
            return $this->app->abort(500);
        }

        return $r;
    }

    public function outputResponseXML(Request $request, $response, $name) {
        $retval = '<?xml version="1.0"?>' . "\n";

        $array_name_map = array('subtypes' => 'subtype');
        if ($name == 'types')
            $array_name_map['results'] = 'type';
        else if ($name == 'disciplines')
            $array_name_map['results'] = 'discipline';
        else if ($name == 'rarities')
            $array_name_map['results'] = 'rarity';
        else if (preg_match('/^(all-items-[0-9]*|items-[0-9]*-[0-9]*|item-search-[0-9]*)$/', $name))
            $array_name_map['results'] = 'item';
        else if (preg_match('/^(recipes-[0-9]*-[0-9]*|all-recipes-[0-9]*)$/', $name))
            $array_name_map['results'] = 'recipe';
        else if (preg_match('/^(item-listings-[0-9]*-[0-9]*-[0-9]*)$/', $name))
            $array_name_map['results'] = 'listing';
        else
            $array_name_map['results'] = 'result';

        $retval .= $this->keyPairToXML('response', $response, $array_name_map);

        return $retval;
    }

    private function keyPairToXML($key, $value, $array_name_map) {
        $retval = '<' . $key . '>';

        if (is_array($value)) {
            // we need to detect if it is an associative array or not
            if (array_keys($value) !== range(0, count($value) - 1)) {
                // associative, treat it like an object
                foreach ($value as $akey => $avalue)
                    $retval .= $this->keyPairToXML($akey, $avalue, $array_name_map);
            } else {
                // not associative, treat it like a list
                if (!isset($array_name_map[$key]))
                    throw new \Exception("Invalid array type for XML output.");

                $akey = $array_name_map[$key];
                foreach ($value as $avalue)
                    $retval .= $this->keyPairToXML($akey, $avalue, $array_name_map);
            }
        } else if (is_object($value)) {
            foreach (get_object_vars($value) as $akey => $avalue)
                $retval .= $this->keyPairToXML($akey, $avalue, $array_name_map);
        } else {
            $retval .= $value;
        }

        $retval .= '</' . $key . '>';

        return $retval;
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
                $result = array_map(array($this, 'escapeCSVValue'), $result);
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

    public function buildListingDataArray(array $listing) {
        $data = array(
            "listing_datetime" => $this->dateAsUTCString($listing['ListingDatetime']),
            "unit_price"       => intval($listing['UnitPrice']),
            "quantity"         => intval($listing['Quantity']),
            "listings"         => intval($listing['Listings']),
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

        return "{$date->format("c")}";
    }

    public function escapeCSVValue($val) {
        if (is_string($val)) {
            $val = '"' . $val . '"';
        }

        return $val;
    }
}
