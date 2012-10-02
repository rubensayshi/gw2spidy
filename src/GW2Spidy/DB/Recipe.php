<?php

namespace GW2Spidy\DB;

use GW2Spidy\Util\Functions;

use GW2Spidy\Util\CacheHandler;

use GW2Spidy\DB\om\BaseRecipe;


/**
 * Skeleton subclass for representing a row from the 'recipe' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gw2spidy
 */
class Recipe extends BaseRecipe {
    public function save(\PropelPDO $con = null) {
        if ($this->isColumnModified(RecipePeer::SELL_PRICE) || $this->isColumnModified(RecipePeer::COST) || $this->isColumnModified(RecipePeer::PROFIT)) {
            $this->setUpdated(new \DateTime());
        }

        return parent::save($con);
    }

    public function calculatePrice($forceCrafted = false) {
        $cost = 0;

        /* @var $ingredient RecipeIngredient */
        foreach ($this->getIngredients() as $ingredient) {
            $item      = $ingredient->getItem();
            $tpcost    = $ingredient->getCount() * $item->getMinSaleUnitPrice();
            $craftcost = null;

            if ($item->getResultOfRecipes()->count() && $recipe = reset($item->getResultOfRecipes())) {
                $crafts = ceil($ingredient->getCount() / $recipe->getCount());

                $craftcost = $recipe->calculatePrice() * $crafts;
            }

            if (!$craftcost) {
                $cost += $tpcost;
            } else if (!$tpcost || $forceCrafted) {
                $cost += $craftcost;
            } else {
                $cost += min($tpcost, $craftcost);
            }
        }

        return $cost;
    }

    public function getGW2DBTooltip($href = null) {
        $cache    = CacheHandler::getInstance('recipe_gw2db_tooltips');
        $cacheKey = $this->getDataId() . "::" . substr(md5($href),0,10);

        if (true || !($tooltip = $cache->get($cacheKey))) {
            $tooltip   = $this->getGW2DBTooltipFromGW2DB();
            $html      = str_get_html($tooltip);
            $gw2dbhref = Functions::getGW2DBLinkRecipe($this);

            if ($href) {
                $html->find('dt.db-title', 0)->innertext = <<<HTML
<a href="{$href}">{$html->find('dt.db-title', 0)->innertext}</a>
HTML;
            }

            $html->find('div.db-description', 0)->style = "position: relative; z-index: 1;";
            $html->find('div.db-description', 0)->innertext .= <<<HTML
<a href="{$gw2dbhref}" target="_blank" title="View this item on GW2DB" data-notooltip="true">
    <img src="/assets/img/powered_gw2db_onDark.png" width="80" style="position: absolute; bottom: 0px; right: 0px; opacity: 0.2;" />
</a>
HTML;
            $tooltip = (string)$html;

            $cache->set($cacheKey, $tooltip, MEMCACHE_COMPRESSED, 86400);
        }

        return $tooltip;
    }

    public function getGW2DBTooltipFromGW2DB() {
        $js = file_get_contents("http://www.gw2db.com/recipes/{$this->getGW2DBExternalId()}/tooltip");

        $js = preg_replace("/^(WP_OnTooltipLoaded)?\(/", '', $js);
        $js = preg_replace("/\)$/", '', $js);

        $data = json_decode($js, true);
        $html = $data['Tooltip'];

        return stripslashes($html);
    }
} // Recipe
