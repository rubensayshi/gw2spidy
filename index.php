<?php

require dirname(__FILE__) . '/config.inc.php';

define('TRADINGPOST_URL', 'https://tradingpost-live.ncplatform.net/');
define('AUTH_URL', 'https://account.guildwars2.com/login?redirect_uri=http://tradingpost-live.ncplatform.net/authenticate?source=%2F&game_code=gw2');

function getcookiejar() {
    static $jar = null;

    if (is_null($jar)) {
        $jar = "tmp/" . uniqid("cookie_jar");
    }

    return $jar;
}

function cleanupcookiejar() {
    unlink(getcookiejar());
}

function curlit($url, $options = array(), $headers = array()) {
    $options = $options ?: array();
    $headers = $headers ?: array();

    $headers = $headers + array(
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
        "Accept-Language: en-us,en;q=0.5",
        // "Accept-Encoding: gzip, deflate",
        "Accept-Encoding: deflate", # while we can get raw, we don't have to gunzip the response <3
        "Connection: keep-alive",
        "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0.1",
    );

    $headers[] = "Referer: {$url}";

    $options = $options + array(
        CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_HEADER         => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        // CURLOPT_VERBOSE        => true,
        CURLOPT_FAILONERROR    => false,
        CURLOPT_COOKIEJAR      => getcookiejar(),
        CURLOPT_COOKIEFILE     => getcookiejar(),
    );

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $return = curl_exec($ch);
    curl_close($ch);

    return $return;
}

function login($email, $pass) {
    $return = curlit(AUTH_URL, array(
        CURLOPT_POST        => true,
        CURLOPT_POSTFIELDS  => http_build_query(array('email' => $email, 'password' => $pass)),
    ), array(
        "Host: account.guildwars2.com",
        // --
    ));

    if (!$return) {
        return false;
    }

    return true; // are we logged in ?
}

function getitem($name) {
    $return = curlit("https://tradingpost-live.ncplatform.net/ws/search.json?text=".urlencode($name)."&levelmin=0&levelmax=80", array(
        //--
    ), array(
        //--
    ));

    if (!$return) {
        return false;
    }

    $data = json_decode($return);

    foreach ($data->results as $item) {
        if ($item->name == $name) {
            return $item;
        }
    }

    return null;
}

function getlistings($id) {
    $return = curlit("https://tradingpost-live.ncplatform.net/ws/listings.json?id={$id}&type=sells", array(
        // --
    ), array(
        // --
    ));

    if (!$return) {
        return false;
    }

    $data = json_decode($return);

    return $data;
}



login(EMAIL, PASSWORD);
$copper = getitem("Copper Ore");
var_dump(getlistings($copper->data_id));

cleanupcookiejar();

