<?php

namespace GW2Spidy\GW2API;

use GW2Spidy\Util\CurlRequest;
use GW2Spidy\Util\CacheHandler;

class APIItem {
    protected $item_id;
    protected $name;
    protected $description;
    protected $type;
    protected $sub_type;
    protected $level;
    protected $rarity;
    protected $vendor_value;
    protected $icon_file_id;
    protected $icon_file_signature;
    protected $game_types;
    protected $flags;
    protected $restrictions;
    protected $image;
    protected $infusion_slots;
    protected $infix_upgrade;
    protected $suffix_item_id;
    
    protected function __construct($APIItem) {        
        $this->item_id = (int) $APIItem['item_id'];
        $this->name = $APIItem['name'];
        $this->description = $APIItem['description'];
        $this->type = $APIItem['type'];
        $this->sub_type = null;
        $this->level = $APIItem['level'];
        $this->rarity = $APIItem['rarity'];
        $this->vendor_value = (int) $APIItem['vendor_value'];
        $this->icon_file_id = $APIItem['icon_file_id'];
        $this->icon_file_signature = $APIItem['icon_file_signature'];
        $this->game_types = $APIItem['game_types'];
        $this->flags = $APIItem['flags'];
        $this->restrictions = $APIItem['restrictions'];
        
        $this->image = getAppConfig('gw2spidy.gw2render_url')."/file/{$this->icon_file_signature}/{$this->icon_file_id}.png";
        
        $this->infusion_slots = null;
        $this->infix_upgrade = array();
        $this->suffix_item_id = null;
    }
    
    public static function getItemByJSON($API_JSON) {
        $APIItem = json_decode($API_JSON, true);
        
        if (!isset($APIItem['type'])) {
            return null;
        }
        
        switch($APIItem['type']) {
            case "Armor": return new Armor($APIItem);
            case "Back": return new Back($APIItem);
            case "Bag": return new Bag($APIItem);
            case "Consumable": return new Consumable($APIItem);
            case "Container": return new Container($APIItem);
            case "CraftingMaterial": return new CraftingMaterial($APIItem);
            case "Gathering": return new Gathering($APIItem);
            case "Gizmo": return new Gizmo($APIItem);
            case "MiniPet": return new MiniPet($APIItem);
            case "Tool": return new Tool($APIItem);
            case "Trinket": return new Trinket($APIItem);
            case "Trophy": return new Trophy($APIItem);
            case "UpgradeComponent": return new UpgradeComponent($APIItem);
            case "Weapon": return new Weapon($APIItem);
            default: return null;
        }
    }
    
    public static function getItemById($itemID) {
        $cache = CacheHandler::getInstance('item_gw2api');
        $cacheKey = $itemID . "::" . substr(md5($itemID),0,10);
        $ttl      = 86400;
        
        if (!($API_JSON = $cache->get($cacheKey))) {
            try {
                $curl_item = CurlRequest::newInstance(getAppConfig('gw2spidy.gw2api_url')."/v1/item_details.json?item_id={$itemID}")
                    ->exec();
                $API_JSON = $curl_item->getResponseBody();
                
                $cache->set($cacheKey, $API_JSON, MEMCACHE_COMPRESSED, $ttl);
            } catch (\Exception $e){
                $ttl = 600;
                $cache->set($cacheKey, null, MEMCACHE_COMPRESSED, $ttl);
                
                return null;
            }
        }
        
        return APIItem::getItemByJSON($API_JSON);
    }
    
    public function getTooltip() {
        $tooltip = <<<HTML
        <div class="p-tooltip-a p-tooltip_gw2 db-tooltip">
            <div class="p-tooltip-image db-image">
                <img src="{$this->getImageURL()}" alt="{$this->getHTMLName()}" />
            </div>
            <div class="p-tooltip-description db-description">
                <dl class="db-summary">
                    {$this->getTooltipDescription()}
                </dl>
            </div>
        </div>
HTML;
        return $tooltip;
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
            <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
            <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            <dd class="db-itemDescription">{$this->getSoulboundStatus()}</dd>
HTML;
        return $tooltip;
    }
    
    protected function getFormattedSuffixItem() {
        $html = "";
        
        if (($Suffix_Item = $this->getSuffixItem()) !== null) {
            $buff = (method_exists($Suffix_Item, 'getBuffDescription')) ? $Suffix_Item->getBuffDescription() : null;
            $img = "<img alt='' src='{$Suffix_Item->getImageURL()}' height='16' width='16'>";
            
            $html .= "<dd class=\"db-slotted-item\">{$img} {$Suffix_Item->getHTMLName()}<br>{$buff}</dd>\n";
        }
        
        return $html;
    }
    
    protected function getFormattedAttributes() {
        $html = "";
        
        $this->cleanAttributes();
        $buffs_added = $this->addBuffsToAttributes();
        
        foreach ($this->getAttributes() as $attr) {
            $pct = ($attr['attribute'] == 'Critical Damage' || $attr['attribute'] == 'Critical Chance') ? '%' : null;
            $html .= "<dd class=\"db-stat\">+{$attr['modifier']}{$pct} {$attr['attribute']}</dd>\n";
        }
        
        //Add buffs if they haven't already been added to the attributes, but do exist.
        if (!$buffs_added) {
            $html .= "<dd class=\"db-stat\">{$this->getBuffDescription()}</dd>";
        }
        
        return $html;
    }
    
    protected function getFormattedLevel() {
        return ($this->level > 0) ? "<dd class=\"db-requiredLevel\">Required Level: {$this->level}</dd>" : null;
    }
        
    protected function cleanAttributes() {
        //Rename certain attributes to be in line with how they appear in game.
        if (isset($this->infix_upgrade['attributes'])) {
            array_walk($this->infix_upgrade['attributes'], function(&$attr){
                if ($attr['attribute'] == 'CritDamage')         $attr['attribute'] = 'Critical Damage';
                if ($attr['attribute'] == 'ConditionDamage')    $attr['attribute'] = 'Condition Damage';
                if ($attr['attribute'] == 'Healing')            $attr['attribute'] = 'Healing Power';
            });
        }
    }
    
    protected function addBuffsToAttributes() {
        if (isset($this->infix_upgrade['buff']['description'])) {
            //Certain items like the Major Sigil of bloodlust contain descriptions like:
            //Gain +7 power each time you kill a foe. (Max 25 stacks; ends on down.)
            //This will break on this style of formatting, so we ignore that kind of buff description 
            //and return false because this buff can't be added to attributes.
            if (strpos($this->infix_upgrade['buff']['description'], "+") !== 0) {
                return false;
            }
            
            $buffs = explode("\n", $this->infix_upgrade['buff']['description']);
            
            $attributes_exist = array();
            
            foreach ($this->infix_upgrade['attributes']as $attr) {
                $attributes_exist[] = $attr['attribute'];
            }
            
            foreach ($buffs as $buff) {
                list($modifier_stage1, $attribute) = explode(" ", $buff, 2);
                $modifier_stage2 = str_replace("+", "", $modifier_stage1);
                $modifier = (int) str_replace("%", "", $modifier_stage2);
                if (!in_array($attribute, $attributes_exist)) {
                    $this->infix_upgrade['attributes'][] = array('attribute' => $attribute, 'modifier' => $modifier);
                }
                else {
                    foreach ($this->infix_upgrade['attributes'] as &$attr) {
                        $attr['modifier'] += ($attr['attribute'] == $attribute) ? $modifier : 0;
                    }
                }
            }
        }
        
        //Return true to signify that buffs have either been added, or don't need to be added.
        return true;
    }
    
    protected function getBuffDescription() {
        if (isset($this->infix_upgrade['buff']['description'])) {
            return nl2br($this->infix_upgrade['buff']['description'], false);
        }
        
        return null;
    }
        
    public function getSoulboundStatus() {
        if (in_array("SoulBindOnUse", $this->flags)) {
            return "Soulbound On Use";
        }
        elseif (in_array("AccountBound", $this->flags)) {
            return "Account Bound";
        }
        elseif (in_array("SoulBindOnAcquire", $this->flags)) {
            return "Soulbound On Acquire";
        }
        
        return null;
    }
    
    public function isUnsellable() {
        if (count(array_intersect(array("SoulBindOnAcquire", "AccountBound"), $this->flags)) > 0) {
            return true;
        }
        
        return $this->isPvpOnly();
    }
    
    public function isPvpOnly() {
        if (in_array("Pvp", $this->game_types)) {
            if (count(array_intersect(array("Activity", "Dungeon", "Pve", "Wvw"), $this->game_types)) == 0) {
                return true;
            }
        }
        
        return false;
    } 
    
    public function getSuffixItem() {
        $APIItem = ($this->suffix_item_id != "") ? APIItem::getItemById($this->suffix_item_id) : null;
        
        return $APIItem;
    }
    
    public function getAttributes() {
        return isset($this->infix_upgrade['attributes']) ? $this->infix_upgrade['attributes'] : array();
    }
    
    public function getBuff() {
        return isset($this->infix_upgrade['buff']) ? $this->infix_upgrade['buff'] : null;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function getSubType() {
        return $this->sub_type;
    }
    
    public function getMarketType() {
        $itemtype = $this->getType();
        
        //Replace right value with left value.
        //The Trading Post has different names for item types than the official API does.
        $keys = array(
            'Crafting Material'     => 'CraftingMaterial',
            'Crafting Component'    => 'CraftingComponent',
            'Upgrade Component'     => 'UpgradeComponent',
            'Mini'                  => 'MiniPet'
        );

        if (in_array($itemtype, $keys)) {
            $a = array_keys($keys, $itemtype);
            $itemtype = $a[0];
        }
        
        return $itemtype;
    }
    
    public function getDBSubType() {
        $subtype = $this->getSubType();

        //Replace right value with left value. 
        //The Database has different names for these subtypes.
        $keys = array(
            'Harpoon Gun'   => 'Harpoon',
            'Aquatic Helm'  => 'HelmAquatic',
            'Spear'         => 'Speargun',
            'Short Bow'     => 'ShortBow',
            'Gift Box'      => 'GiftBox'
        );

        if (in_array($subtype, $keys)) {
            $a = array_keys($keys, $subtype);
            $subtype = $a[0];
        }
        
        return $subtype;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getHTMLDescription() {
        return htmlspecialchars(strip_tags($this->description));
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getHTMLName() {
        return htmlspecialchars($this->name);
    }
        
    public function getRarity() {
        return $this->rarity;
    }
    
    public function getRarityLower() {
        return strtolower($this->rarity);
    }
    
    public function getLevel() {
        return $this->level;
    }
    
    public function getImageURL() {
        return $this->image;
    }
    
    public function getVendorValue() {
        return $this->vendor_value;
    }
    
    public function getItemId() {
        return $this->item_id;
    }
}