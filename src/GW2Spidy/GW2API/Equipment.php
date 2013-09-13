<?php

namespace GW2Spidy\GW2API;

abstract class Equipment extends API_Item {
    protected $infusion_slots;
    protected $infix_upgrade;
    protected $suffix_item_id;
    
    public function __construct($API_Item) {
        parent::__construct($API_Item);
        
        $this->infusion_slots = null;
        $this->infix_upgrade = array();
        $this->suffix_item_id = null;
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
    
    public function getBuff() {
        return isset($this->infix_upgrade['buff']) ? $this->infix_upgrade['buff'] : null;
    }
    
    public function getBuffDescription() {
        if (isset($this->infix_upgrade['buff']['description'])) {
            return nl2br($this->infix_upgrade['buff']['description'], false);
        }
        
        return null;
    }
    
    protected function addBuffsToAttributes() {
        $buffs = explode("\n", $this->infix_upgrade['buff']['description']);
        
        $attributes_exist = (count($this->infix_upgrade['attributes']) > 0);
        
        foreach ($buffs as $buff) {
            list($modifier_stage1, $attribute) = explode(" ", $buff, 2);
            $modifier_stage2 = str_replace("+", "", $modifier_stage1);
            $modifier = (int) str_replace("%", "", $modifier_stage2);
            
            if (!$attributes_exist) {
                $this->infix_upgrade['attributes'][] = array('attribute' => $attribute, 'modifier' => $modifier);
            }
            else {
                foreach ($this->infix_upgrade['attributes'] as &$attr) {
                    $attr['modifier'] += ($attr['attribute'] == $attribute) ? $modifier : 0;
                }
            }
        }
    }
}