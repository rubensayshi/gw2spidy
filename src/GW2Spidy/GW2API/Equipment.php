<?php

namespace GW2Spidy\GW2API;

abstract class Equipment extends API_Item {
    protected $infusion_slots;
    protected $infix_upgrade;
    protected $suffix_item_id;
    
    public function __construct($API_Item) {
        parent::__construct($API_Item);
    }
    
    public function cleanAttributes() {
        //Rename certain attributes to be in line with how they appear in game.
        if (isset($this->infix_upgrade['attributes'])) {
            array_walk($this->infix_upgrade['attributes'], function(&$attr){
                if ($attr['attribute'] == 'CritDamage')         $attr['attribute'] = 'Critical Damage';
                if ($attr['attribute'] == 'ConditionDamage')    $attr['attribute'] = 'Condition Damage';
                if ($attr['attribute'] == 'Healing')            $attr['attribute'] = 'Healing Power';
            });
        }
    }
    
    public function getAttributes() {
        return isset($this->infix_upgrade['attributes']) ? $this->infix_upgrade['attributes'] : array();
    }
    
    public function getFormattedAttributes() {
        $html = "";
        
        foreach ($this->getAttributes() as $attr) {
            $pct = ($attr['attribute'] == 'Critical Damage') ? '%' : null;
            $html .= "<dd class=\"db-stat\">+{$attr['modifier']}{$pct} {$attr['attribute']}</dd>\n";
        }
        
        return $html;
    }
    
    public function getSuffixItem() {
        $API_Item = ($this->suffix_item_id != "") ? API_Item::getItem($this->suffix_item_id) :  null;
        
        return $API_Item;
    }
    
    public function getFormattedSuffixItem() {
        $html = "";
        
        if (($Suffix_Item = $this->getSuffixItem()) !== null) {
            $buff = (method_exists($Suffix_Item, 'getBuffDescription')) ? $Suffix_Item->getBuffDescription() : null;
            $img = "<img alt='' src='{$Suffix_Item->getImageURL()}' height='16' width='16'>";
            
            $html .= "<dd class=\"db-slotted-item\">{$img} {$Suffix_Item->getHTMLName()}<br>{$buff}</dd>\n";
        }
        
        return $html;
    }
}