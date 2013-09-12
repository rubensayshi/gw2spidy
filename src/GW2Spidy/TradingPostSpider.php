<?php

namespace GW2Spidy;

use \Exception;

use GW2Spidy\Util\CurlRequest;

use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemType;


class TradingPostSpider extends BaseSpider {
    const LISTING_TYPE_SELL = 'sells';
    const LISTING_TYPE_BUY  = 'buys';

    public function getItemById($id) {
        $results = $this->getItemsByIds(array($id));

        return reset($results);
    }

    public function getItemsByIds(array $ids) {
        /*
         * Shroud gave me an alternative way of fetching this which avoids the buggy cache
         *  unfortunatly he gave it to me after I agreed not to share it so that's why this weird snippet is here
         */
        if (getAppConfig("gw2spidy.use_shroud_magic") && class_exists("\\ShroudMagic\\ShroudMagicSpiderHelper")) {
            return \ShroudMagic\ShroudMagicSpiderHelper::getItemsByIds($this, $ids);
        }

        $ids = array_map('urlencode', $ids);

        $s = $this->getSession();

        $curl = CurlRequest::newInstance(getAppConfig('gw2spidy.tradingpost_url') . "/ws/search.json?ids=".implode(",", $ids)."")
             ->setCookieJar($s->getCookieJar())
             ->setCookie("s={$s->getSessionKey()}")
             ->setHeader("X-Requested-With: XMLHttpRequest")
             ->exec()
             ;

        $data = json_decode($curl->getResponseBody(), true);

        return $data['results'];
    }

    public function getItemByExactName($name) {

        $s = $this->getSession();

        $curl = CurlRequest::newInstance(getAppConfig('gw2spidy.tradingpost_url') . " /ws/search.json?text=".urlencode($name)."&levelmin=0&levelmax=80")
                    ->setCookieJar($s->getCookieJar())
                    ->setCookie("s={$s->getSessionKey()}")
                    ->setHeader("X-Requested-With: XMLHttpRequest")
                    ->exec()
                    ;

        $data = json_decode($curl->getResponseBody(), true);

        foreach ($data['results'] as $item) {
            if ($item['name'] == $name) {
                return $item;
            }
        }

        return null;
    }

    public function getListingsById($id, $type = self::LISTING_TYPE_SELL) {

        $s = $this->getSession();

        $curl = CurlRequest::newInstance(getAppConfig('gw2spidy.tradingpost_url') . "/ws/listings.json?id={$id}&type={$type}")
                    ->setCookieJar($s->getCookieJar())
                    ->setCookie("s={$s->getSessionKey()}")
                    ->exec()
                    ;

        $data = json_decode($curl->getResponseBody(), true);

        if (!isset($data['listings']) || !isset($data['listings'][$type])) {
            throw new Exception("Failed to retrieve proper listingsdata from the request result");
        }

        $listings = $data['listings'][$type];


        return $listings;
    }

    public function getAllListingsById($id) {

        $s = $this->getSession();

        $curl = CurlRequest::newInstance(getAppConfig('gw2spidy.tradingpost_url') . "/ws/listings.json?id={$id}&type=all")
                    ->setCookieJar($s->getCookieJar())
                    ->setCookie("s={$s->getSessionKey()}")
                    ->exec()
                    ;

        $data = json_decode($curl->getResponseBody(), true);

        if (!isset($data['listings']) || !isset($data['listings']['buys']) || !isset($data['listings']['sells'])) {
            throw new Exception("Failed to retrieve proper listingsdata from the request result");
        }

        $listings = array(
            self::LISTING_TYPE_SELL => $data['listings'][self::LISTING_TYPE_SELL],
            self::LISTING_TYPE_BUY	=> $data['listings'][self::LISTING_TYPE_BUY],
        );

        return $listings;
    }

    public function getMarketData() {

        $s = $this->getSession();

        $curl = CurlRequest::newInstance(getAppConfig('gw2spidy.tradingpost_url'))
             ->setCookieJar($s->getCookieJar())
             ->setCookie("s={$s->getSessionKey()}")
             ->exec()
             ;


        $result = $curl->getResponseBody();

        if (preg_match("/<script>[\n ]+GW2\.market = (\{.*?\})[\n ]+<\/script>/ms", $result, $matches)) {
            $json = json_decode($matches[1], true);
            return $json['data'];
        } else {
            throw new Exception("Failed to extract GW2.market JSON from HTML");
        }
    }

    public function getItemList($type=null, $subType=null, $offset=0) {

        $s = $this->getSession();

        $typeId    = ($type instanceof ItemType)       ? $type->getId()    : $type;
        $subTypeId = ($subType instanceof ItemSubType) ? $subType->getId() : $subType;

        $url = getAppConfig('gw2spidy.tradingpost_url') . "/ws/search.json?text=&levelmin=0&levelmax=80&offset={$offset}";

        if ($typeId !== null) {
            $url = "{$url}&type={$typeId}";
        }
        if ($subTypeId !== null) {
            $url = "{$url}&subtype={$subTypeId}";
        }

        $curl = CurlRequest::newInstance($url)
                    ->setHeader("X-Requested-With: XMLHttpRequest")
                    ->setCookieJar($s->getCookieJar())
                    ->setCookie("s={$s->getSessionKey()}")
                    ->exec()
                    ;

        $result = $curl->getResponseBody();
        $json   = json_decode($result, true);

        if (isset($json['results']) && $json['results']) {
            return $json['results'];
        } else {
            return false;
        }
    }
}
