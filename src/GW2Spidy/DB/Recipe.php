<?php

namespace GW2Spidy\DB;

use GW2Spidy\DB\om\BaseRecipe;

use GW2Spidy\GW2API\API_Recipe;


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
    const FALSE_POSITIVE = 'FALSE_POSITIVE';

    public function save(\PropelPDO $con = null) {
        if ($this->isColumnModified(RecipePeer::SELL_PRICE) || $this->isColumnModified(RecipePeer::COST) || $this->isColumnModified(RecipePeer::PROFIT)) {
            $this->setUpdated(new \DateTime());
        }

        return parent::save($con);
    }

    public function calculatePrice($forceCrafted = false) {
        $total = array('gold' => 0, 'karma' => 0);

        /* @var $ingredient RecipeIngredient */
        foreach ($this->getIngredients() as $ingredient) {
            $item      = $ingredient->getItem();
            $buycost   = $ingredient->getCount() * $item->getBestPrice();
            $craftcost = null;
            $craftkarmacost = null;

            if ($item->getResultOfRecipes()->count() && $recipe = reset($item->getResultOfRecipes())) {
                $crafts = $ingredient->getCount() / $recipe->getCount();

                $subtotal = $recipe->calculatePrice($forceCrafted);
                $craftcost = ceil($subtotal['gold'] * $crafts);
                $craftkarmacost = ceil($subtotal['karma'] * $crafts);
            }

            if (!$craftcost) {
                $cost = $buycost;
            } else if (!$buycost || $forceCrafted) {
                $cost = $craftcost;
            } else {
                $cost = min($buycost, $craftcost);
            }

            if($craftkarmacost && $cost < $buycost) {
                $total['karma'] += $craftkarmacost;
            } else if (!$cost && $item->getKarmaPrice()) {
                $total['karma'] += $ingredient->getCount() * $item->getKarmaPrice();
            }

            $total['gold'] += $cost;
        }

        return $total;
    }
    
    public function getTooltip() {
        $API_Recipe = new API_Recipe($this->data_id);
        
        return ($API_Recipe !== null) ? $API_Recipe->getTooltip() : null;
    }
    
    public function getMargin() {
        $margin = ($this->getCost() == 0) ? 0 : $this->getProfit() / $this->getCost();

        return $margin;
    }
} // Recipe
