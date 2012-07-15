<?php

namespace GW2Spidy\TradeMarket;

use GW2Spidy\Util\CurlRequest;

class TradeMarket {
    protected $cookiejar;

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

    /**
     * @return Item
     */
    public function getItemByExactName($name) {
        $curl = CurlRequest::newInstance("https://tradingpost-live.ncplatform.net/ws/search.json?text=".urlencode($name)."&levelmin=0&levelmax=80")
             ->exec()
             ;

        $data = json_decode($curl->getResult(), true);

        foreach ($data['results'] as $item) {
            if ($item['name'] == $name) {
                return Item::fromStdObject($item);
            }
        }

        return null;
    }

    public function getListingsById($id, $type = "sells") {
        $curl = CurlRequest::newInstance("https://tradingpost-live.ncplatform.net/ws/listings.json?id={$id}&type={$type}")
             ->exec()
             ;

        $data = json_decode($curl->getResult(), true);

        return $data;
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
            throw new Exception("Failed to extract GW2.market JSON from HTML");
        }
    }
}

?>