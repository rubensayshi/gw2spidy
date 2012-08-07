<?php

namespace GW2Spidy;

use GW2Spidy\DB\ItemSubType;

use GW2Spidy\DB\ItemType;

use GW2Spidy\Util\CurlRequest;

class TradeMarket {
    protected static $instance;

    public function __construct() {
        $this->doLogin();
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function doLogin() {
        $curl = CurlRequest::newInstance(AUTH_URL)
            ->setOption(CURLOPT_POST, true)
            ->setOption(CURLOPT_POSTFIELDS, http_build_query(array('email' => LOGIN_EMAIL, 'password' => LOGIN_PASSWORD)))
            ->exec()
            ;
    }

    public function getItemByExactName($name) {
        $curl = CurlRequest::newInstance("https://tradingpost-live.ncplatform.net/ws/search.json?text=".urlencode($name)."&levelmin=0&levelmax=80")
             ->exec()
             ;

        $data = json_decode($curl->getResult(), true);

        foreach ($data['results'] as $item) {
            if ($item['name'] == $name) {
                return $item;
            }
        }

        return null;
    }

    public function getListingsById($id, $type = "sells") {
        $curl = CurlRequest::newInstance("https://tradingpost-live.ncplatform.net/ws/listings.json?id={$id}&type={$type}")
             ->exec()
             ;

        $data = json_decode($curl->getResult(), true);

        return isset($data['listings'][$type]) ? $data['listings'][$type] : null;
    }

    public function getMarketData() {
        $curl = CurlRequest::newInstance("https://tradingpost-live.ncplatform.net/")
             ->exec()
             ;

        $result = $curl->getResult();

        if (preg_match("/<script>[\n ]+GW2\.market = (\{.*?\})[\n ]+<\/script>/ms", $result, $matches)) {
            $json = json_decode($matches[1], true);
            return $json['data'];
        } else {
            throw new \Exception("Failed to extract GW2.market JSON from HTML");
        }
    }

    public function getItemList($type=null, $subType=null, $offset=0) {

        $typeId    = ($type instanceof ItemType)       ? $type->getId()    : $type;
        $subTypeId = ($subType instanceof ItemSubType) ? $subType->getId() : $subType;

        $url = "https://tradingpost-live.ncplatform.net/ws/search.json?text=&levelmin=0&levelmax=80&offset={$offset}";

        if ($typeId !== null) {
            $url = "{$url}&type={$typeId}";
        }
        if ($subTypeId !== null) {
            $url = "{$url}&subtype={$subTypeId}";
        }

        $curl = CurlRequest::newInstance($url)
             ->exec()
             ;

        $result = $curl->getResult();
        $json   = json_decode($result, true);

        if (isset($json['results']) && $json['results']) {
            return $json['results'];
        } else {
            return false;
        }
    }
}

?>