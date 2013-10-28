<?php

use GW2Spidy\Util\CacheHandler;

ini_set('memory_limit', '1G');

$money_alts = array(
    'Copper coin' => 'c',
    'Silver coin' => 's',
    'Gold coin'   => 'g',
    'Karma'       => 'k',
    'Karma.png'   => 'k',
);

require dirname(__FILE__) . '/../autoload.php';

// ensure purged cache, otherwise everything goes to hell
CacheHandler::getInstance("purge")->purge();

function get_html_from_node($node) {
    $html = '';

    $tmp_doc = new DOMDocument();
    $tmp_doc->appendChild($tmp_doc->importNode($node,true));
    $html .= $tmp_doc->saveHTML();

    return $html;
}

function get_html_from_nodelist($nodeList) {
    $html = '';

    foreach ($nodeList as $node) {
        $html .= get_html_from_node($node);
    }

    return $html;
}

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

function get_price($td, $xpath) {
    $coin  = null;
    $karma = null;

    $num  = false;
    foreach ($td->childNodes as $n) {
        $t = trim($n->textContent);
        $pass = false;

        // it's a number
        if (!$pass && $num === false && intval($t) == $t) {
            $num = $t;
            $pass = true;
        }
        if (!$pass && $num !== false) {
            $img = $xpath->query('.//img', $n);

            if ($img->length > 0) {
                $alt = $img->item(0)->getAttribute('alt');

                if (isset($GLOBALS['money_alts'][$alt])) {
                    if ($GLOBALS['money_alts'][$alt] == 'k') {
                        $karma = $num;
                    } else {
                        if ($GLOBALS['money_alts'][$alt] == 'c') {
                            $coin += $num;
                        } elseif ($GLOBALS['money_alts'][$alt] == 's') {
                            $coin += ($num * 100);
                        } elseif ($GLOBALS['money_alts'][$alt] == 'g') {
                            $coin += ($num * 10000);
                        }
                    }

                    $pass = false;
                }
            }
        }

        if (!$pass) {
            $num = false;
        }
    }

    return array('coin' => $coin, 'karma' => $karma);
}

function get_items($url) {
    $doc = new DOMDocument();
    $doc->loadHTML(get($url));
    $xpath = new DOMXPath($doc);
    $vendorP = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " npc ") and '
                           . 'contains(concat(" ", normalize-space(@class), " "), " infobox ")]/p[@class="heading"]');

    if ($vendorP->length > 0) {
        $vendor = str_replace('"', '&quot;',trim($vendorP->item(0)->textContent));
        $items = array();
    } else {
        $vendor = '?';
        $items = array();
    }

    if (true) {
        $tables = $xpath->query('//table[contains(concat(" ", normalize-space(@class), " "), " npc ") and '
                               . 'contains(concat(" ", normalize-space(@class), " "), " table ")]');

        foreach ($tables as $table) {
            $headers = $xpath->query('./tr[position()=1]/th', $table);
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
                $list = $xpath->query('./tr', $table);
                for ($i = 0; $i < $list->length; $i++) {
                    $nameN = $xpath->query('./td[position()='.$npos.']', $list->item($i));
                    $rarityN = $xpath->query('./td[position()='.$rpos.']//b', $list->item($i));
                    if ($rarityN->length == 0) {
                        $rarityN = $xpath->query('./td[position()='.$rpos.']', $list->item($i));
                    }
                    $priceN = $xpath->query('./td[position()='.$ppos.']', $list->item($i));


                    if ($nameN->length > 0 && $rarityN->length > 0 && $priceN->length > 0) {
                        $name = $nameN->item(0)->textContent;
                        $cnt  = 1;

                        // cleanup name
                        $name = str_replace('"', '&quot;', $nameN->item(0)->textContent);
                        $name = preg_replace('/&.+;/', '', htmlentities($name));
                        $name = trim($name);

                        // split of any (number)
                        $a = array();
                        if (preg_match('/(.+) \(([0-9]+)\)/', $name, $a)) {
                            $name = trim($a[1]);
                            $cnt  = intval(trim($a[2]));
                        }

                        $rarity = str_replace('"', '&quot;', strtolower(trim($rarityN->item(0)->textContent)));
                        $price = get_price($priceN->item(0), $xpath);

                        foreach ($price as $k => $v) {
                            if ($v > 0 && $cnt > 0) {
                                $price[$k] = $v / $cnt;
                            }
                        }

                        $items[] = array('name' => $name, 'price' => $price, 'rarity' => $rarity);
                    }
                }
            }
        }

        return array('vendor' => $vendor, 'items' => $items);
    }
    return null;
}

$urls = array_merge(
    get_page_urls('http://wiki.guildwars2.com/index.php?title=Category:Renown_heart_NPCs'),
    get_page_urls('http://wiki.guildwars2.com/index.php?title=Category:Renown_heart_NPCs&from=Magister%20Kathryn'),
    get_page_urls2('http://wiki.guildwars2.com/wiki/Karma_merchant'),
    get_page_urls('http://wiki.guildwars2.com/wiki/Category:Vendor_inventory_tables')
);

var_dump($urls);

$stmt_coin = Propel::getConnection()->prepare("UPDATE item SET
                                              vendor_price = :vendor_price
                                          WHERE name = :name OR name = :names");
$stmt_karma = Propel::getConnection()->prepare("UPDATE item SET
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
            $field = '';
            $price = 0;

            if ($item['price']['karma']) {
                $stmt  = $stmt_karma;
                $field = 'karma_price';
                $price = $item['price']['karma'];
            } elseif ($item['price']['coin']) {
                $stmt  = $stmt_coin;
                $field = 'vendor_price';
                $price = $item['price']['coin'];
            } else {
                echo "!! NO COIN NOR KARMA FOUND FOR {$item['name']} !!";
                continue;
            }

            // if rounding makes the price become 0 we'll just make 1 to avoid fuck ups
            if (!round($price)) {
                $price = 1;
            }

            $stmt->bindValue('name', $item['name']);
            $stmt->bindValue('names', "{$item['name']}[s]");
            $stmt->bindValue($field, $price);

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
                if ($item['price']['karma']) {
                    $stmt->bindValue('karma_price', $item['price']['karma'] / 25);
                } else {
                    $stmt->bindValue('vendor_price', $item['price']['coin'] / 25);
                }

                $stmt->execute();

                if ($stmt->rowCount()) {
                echo "{$name} !!BULK!! \n";
                }
            }
        }
    }
}

\Propel::getConnection()->exec("UPDATE item SET karma_price = 0 AND vendor_price = 20000 WHERE data_id = 22334;"); # Grandmaster's Training Manual
\Propel::getConnection()->exec("UPDATE item SET karma_price = 0 AND vendor_price = 10000 WHERE data_id = 22333;"); # Master's Training Manual
\Propel::getConnection()->exec("UPDATE item SET karma_price = 0 AND vendor_price =  1000 WHERE data_id = 22332;"); # Adept's Training Manual
\Propel::getConnection()->exec("UPDATE item SET karma_price = 2100 WHERE data_id = 19925;");                       # Obsidian Shard

