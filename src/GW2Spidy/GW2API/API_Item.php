<?php

namespace GW2Spidy\GW2API;

use GW2Spidy\Util\CurlRequest;
use GW2Spidy\Util\CacheHandler;

class API_Item {
    protected $item_id;
    protected $name;
    protected $description;
    protected $type;
    protected $level;
    protected $rarity;
    protected $vendor_value;
    protected $icon_file_id;
    protected $icon_file_signature;
    protected $game_types;
    protected $flags;
    protected $restrictions;
    protected $image;
    
    protected function __construct($API_item) {        
        $this->item_id = (int) $API_item['item_id'];
        $this->name = $API_item['name'];
        $this->description = $API_item['description'];
        $this->type = $API_item['type'];
        $this->level = $API_item['level'];
        $this->rarity = $API_item['rarity'];
        $this->vendor_value = (int) $API_item['vendor_value'];
        $this->icon_file_id = $API_item['icon_file_id'];
        $this->icon_file_signature = $API_item['icon_file_signature'];
        $this->game_types = $API_item['game_types'];
        $this->flags = $API_item['flags'];
        $this->restrictions = $API_item['restrictions'];
        
        $this->image = getAppConfig('gw2spidy.gw2render_url')."/file/{$this->icon_file_signature}/{$this->icon_file_id}.png";
    }
    
    public static function getItem($itemID) {
        $cache = CacheHandler::getInstance('item_gw2api');
        $cacheKey = $itemID . "::" . substr(md5($itemID),0,10);
        $ttl      = 86400;
        
        if (!($API_JSON = $cache->get($cacheKey))) {
            try {
                $curl_item = CurlRequest::newInstance(getAppConfig('gw2spidy.gw2api_url')."/v1/item_details.json?item_id={$itemID}")
                    ->exec();
                $API_JSON = $curl_item->getResponseBody();
                
                $cache->set($cacheKey, $API_JSON, MEMCACHE_COMPRESSED, $ttl);
            } catch (Exception $e){
                $ttl = 600;
                $cache->set($cacheKey, null, MEMCACHE_COMPRESSED, $ttl);
                
                return null;
            }
        }
        
        $API_item = json_decode($API_JSON, true);
        
        switch($API_item['type']) {
            case "Armor": return new Armor($API_item);  
            case "Back": return new Back($API_item);
            case "Bag": return new Bag($API_item);
            case "Consumable": return new Consumable($API_item);
            case "Container": return new Container($API_item);
            case "CraftingMaterial": return new CraftingMaterial($API_item);
            case "Gathering": return new Gathering($API_item);
            case "Gizmo": return new Gizmo($API_item);
            case "MiniPet": return new MiniPet($API_item);
            case "Tool": return new Tool($API_item);
            case "Trinket": return new Trinket($API_item);
            case "Trophy": return new Trophy($API_item);
            case "UpgradeComponent": return new UpgradeComponent($API_item);
            case "Weapon": return new Weapon($API_item);
            default: return null;
        }
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getHTMLDescription() {
        return htmlspecialchars($this->description);
    }
    
    public function getRarity() {
        return $this->rarity;
    }
    
    public function getHTMLName() {
        return htmlspecialchars($this->name);
    }
    
    public function getRarityLower() {
        return strtolower($this->rarity);
    }
    
    public function getLevel() {
        return $this->level;
    }
}