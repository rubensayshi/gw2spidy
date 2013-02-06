<?php

use \DateTime;
use GW2Spidy\DB\RecipeQuery;

require dirname(__FILE__) . '/../autoload.php';

$offset = 20;
$i = 0;
$q = RecipeQuery::create();
$q->limit($offset)
  ->offset($i);

if (isset($argv[1])) {
    $q->filterByDataId($argv[1]);
}

while (($recipes = $q->find()) && $recipes->count()) {
    var_dump($i);

    /* @var $recipe GW2Spidy\DB\Recipe */
    foreach ($recipes as $recipe) {
    	if(is_object($recipe->getResultItem())) {
			$price = $recipe->calculatePrice();

			$recipe->setCost($price);
			$recipe->setSellPrice($recipe->getResultItem()->getMinSaleUnitPrice() * $recipe->getCount());
			$recipe->setProfit(($recipe->getSellPrice() * 0.85) - $price);

			$recipe->save();
	    } else {
	    	echo "Error with recipe (no resultitem): {$recipe->getName()}\n";
	    }

        if (in_array('--dev', $argv)) {
            break;
        }
    }

    $q->offset(($i+=$offset));
    sleep(2);
}
