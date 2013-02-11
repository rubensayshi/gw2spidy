<?php

use GW2Spidy\DB\GW2DBItemArchiveQuery;
use GW2Spidy\Util\CacheHandler;
use GW2Spidy\DB\GW2DBItemArchive;
use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;

ini_set('memory_limit', '1G');

require dirname(__FILE__) . '/../autoload.php';

// ensure purged cache, otherwise everything goes to hell
CacheHandler::getInstance("purge")->purge();

function get($url) {
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $data = curl_exec($curl);

    curl_close($curl);

    return $data;
}

function get_page_urls($url) {
    $urls = array();
    $doc = new DOMDocument();
    $doc->loadHTML(get($url));
    $xpath = new DOMXPath($doc);
    $list = $xpath->query('//div[@id="mw-pages"]//a');
    for ($i = 0; $i < $list->length; $i++) {
        $urls[] = 'http://wiki.guildwars2.com' . $list->item($i)->attributes->getNamedItem('href')->value;
    }
    return $urls;
}

function get_page_urls2($url) {
    $urls = array();
    $doc = new DOMDocument();
    $doc->loadHTML(get($url));
    $xpath = new DOMXPath($doc);
    $list = $xpath->query('//table[contains(concat(" ", normalize-space(@class), " "), " npc ") and '
                                . 'contains(concat(" ", normalize-space(@class), " "), " table ")]//tr/td[position()=1]/a');
    for ($i = 0; $i < $list->length; $i++) {
        $urls[] = 'http://wiki.guildwars2.com' . $list->item($i)->attributes->getNamedItem('href')->value;
    }
    return $urls;
}

function get_items($url) {
    $doc = new DOMDocument();
    $doc->loadHTML(get($url));
    $xpath = new DOMXPath($doc);
    $vendorP = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " npc ") and '
                           . 'contains(concat(" ", normalize-space(@class), " "), " infobox ")]/p[@class="heading"]');
    if ($vendorP->length > 0) {
        $vendor = str_replace('"', '&quot;',trim($vendorP->item(0)->textContent));
        $items=array();
        $headers = $xpath->query('//table[contains(concat(" ", normalize-space(@class), " "), " npc ") and '
                               . 'contains(concat(" ", normalize-space(@class), " "), " table ")]//tr[position()=1]/th');
        $npos = -1;
        $rpos = -1;
        $ppos = -1;
        for ($i = 0; $i < $headers->length; $i++) {
            $value = strtolower(trim($headers->item($i)->textContent));
            if ($value == 'item') $npos = $i + 1;
            else if ($value == 'rarity') $rpos = $i + 1;
            else if ($value == 'price') $ppos = $i + 1;
        }

        if ($npos >=0 && $rpos >= 0 && $ppos >= 0) {
            $list = $xpath->query('//table[contains(concat(" ", normalize-space(@class), " "), " npc ") and '
                                . 'contains(concat(" ", normalize-space(@class), " "), " table ")]//tr');
            for ($i = 0; $i < $list->length; $i++) {
                $nameN = $xpath->query('./td[position()='.$npos.']/a', $list->item($i));
                $rarityN = $xpath->query('./td[position()='.$rpos.']//b', $list->item($i));
                if ($rarityN->length == 0) {
                    $rarityN = $xpath->query('./td[position()='.$rpos.']', $list->item($i));
                }
                $priceN = $xpath->query('./td[position()='.$ppos.']', $list->item($i));
                if ($nameN->length > 0 && $rarityN->length > 0 && $priceN->length > 0) {
                    $name = str_replace('"', '&quot;', trim($nameN->item(0)->textContent));
                    $rarity = str_replace('"', '&quot;', strtolower(trim($rarityN->item(0)->textContent)));
                    $price = intval(trim($priceN->item(0)->textContent));
                    $items[] = array('name' => $name, 'price' => $price, 'rarity' => $rarity);
                }
            }
        }
        return array('vendor' => $vendor, 'items' => $items);
    }
    return null;
}

$urls = array_merge(
        get_page_urls('http://wiki.guildwars2.com/index.php?title=Category:Renown_heart_NPCs'),
        get_page_urls('http://wiki.guildwars2.com/index.php?title=Category:Renown_heart_NPCs&from=Queldip'),
        get_page_urls2('http://wiki.guildwars2.com/wiki/Karma_merchant')
    );


$stmt = Propel::getConnection()->prepare("UPDATE item SET
                                              karma_price = :karma_price
                                          WHERE name = :name OR name = :names");

// fix for some words where the plural is irregular
$singular = array(
    "Tomatoe"       => "Tomato",
    "Cherrie"       => "Cherry",
    "Peache"        => "Peach",
    "Buttermilk"    => "Glass of Buttermilk",
    "Rice"          => "Rice Ball",
    "Sour Cream"    => "Bowl[s] of Sour Cream",
    "Yeast"         => "Packet[s] of Yeast",
);

var_dump(count($urls));

foreach($urls as $url) {
    if ($vendor = get_items($url)) {
        foreach ($vendor['items'] as $item) {

            $stmt->bindValue('name', $item['name']);
            $stmt->bindValue('names', "{$item['name']}[s]");
            $stmt->bindValue('karma_price', $item['price']);

            $stmt->execute();

            if ($stmt->rowCount()) {
                echo "{$item['name']} \n";
            }

            if(preg_match("/^(.+?)s? in Bulk$/", $item['name'], $match)) {
                $name = $match[1];
                if(array_key_exists($name, $singular)) {
                    $name = $singular[$name];
                }

                $stmt->bindValue('name', $name);
                $stmt->bindValue('names', "{$name}[s]");
                $stmt->bindValue('karma_price', ceil($item['price'] / 25));

                $stmt->execute();

                if ($stmt->rowCount()) {
                echo "{$name} !!BULK!! \n";
                }
            }
        }
    }
}
