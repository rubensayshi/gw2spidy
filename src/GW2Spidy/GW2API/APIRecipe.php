<?php

namespace GW2Spidy\GW2API;

use GW2Spidy\GW2API\APIItem;
use GW2Spidy\Util\CurlRequest;
use GW2Spidy\Util\CacheHandler;

class APIRecipe {
    protected $recipe_id;
    protected $type;
    protected $output_item_id;
    protected $output_item_count;
    protected $output_item;
    protected $min_rating;
    protected $time_to_craft_ms;
    protected $disciplines;
    protected $flags;
    protected $ingredients;
    protected $ingredient_items;
    
    public function __construct($dataId) {
        $recipe = $this->getRecipe($dataId);
        if ($recipe !== null) {
            $this->recipe_id = $recipe['recipe_id'];
            $this->type = $recipe['type'];
            $this->output_item_id = $recipe['output_item_id'];
            $this->output_item_count = $recipe['output_item_count'];
            $this->output_item = APIItem::getItem($this->output_item_id);
            $this->min_rating = $recipe['min_rating'];
            $this->time_to_craft_ms = $recipe['time_to_craft_ms'];
            $this->disciplines = $recipe['disciplines'];
            $this->flags = $recipe['flags'];
            $this->ingredients = $recipe['ingredients'];
            $this->items = array();
            foreach ($this->ingredients as $ingredient) {
                $this->ingredient_items[] = APIItem::getItem($ingredient['item_id']);
            }
        }
    }
    
    protected function getRecipe ($dataId) {
        $cache = CacheHandler::getInstance('recipe_gw2api');
        $cacheKey = $dataId . "::" . substr(md5($dataId),0,10);
        $ttl      = 86400;
        
        if (!($API_JSON = $cache->get($cacheKey))) {
            try {
                $curl_item = CurlRequest::newInstance(getAppConfig('gw2spidy.gw2api_url')."/v1/recipe_details.json?recipe_id={$dataId}")
                    ->exec();
                $API_JSON = $curl_item->getResponseBody();
                
                $cache->set($cacheKey, $API_JSON, MEMCACHE_COMPRESSED, $ttl);
            } catch (\Exception $e){
                $ttl = 600;
                $cache->set($cacheKey, null, MEMCACHE_COMPRESSED, $ttl);
                
                return null;
            }
        }
        
        $APIItem = json_decode($API_JSON, true);
        
        return $APIItem;
    }
    
    protected function getFormattedIngredients() {
        $ingredients = '';
        
        for ($i = 0; $i < count($this->ingredients); $i++) {
            $ingredients .= <<<HTML
                <dd class="db-ingredient">
                    {$this->ingredients[$i]['count']}X 
                    <a data-tooltip-id="{$this->ingredients[$i]['item_id']}" href="/item/{$this->ingredients[$i]['item_id']}">
                        <span class="gwitem gwitem-{$this->ingredient_items[$i]->getRarityLower()}">
                            {$this->ingredient_items[$i]->getHTMLName()}
                        </span>
                    </a>
                </dd>
HTML;
        }
        
        return $ingredients;
    }
    
    public function getTooltip() {
        $tooltip = <<<HTML
            <div class="p-tooltip-a p-tooltip_gw2 db-tooltip db-tooltip-recipe">
                <div class="db-image">
                    <img src="{$this->output_item->getImageURL()}" alt="{$this->output_item->getHTMLName()}" />
                </div>
                <div class="db-description">
                    <dl class="db-summary">
                        <dt class="db-title">{$this->output_item->getHTMLName()}</dt>
                        <dd class="db-itemIngredients">Ingredients:</dd>
                        {$this->getFormattedIngredients()}
                    </dl>
                    <div class="db-itemCreate">Creates:</div>
                    {$this->output_item->getTooltipDescription()}
                </div>
            </div>
HTML;
        
        return $tooltip;
    }
}