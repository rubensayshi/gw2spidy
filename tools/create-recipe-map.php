<?php
use GW2Spidy\Util\CurlRequest;

ini_set('memory_limit', '1G');

require dirname(__FILE__) . '/../autoload.php';

$recipe_list = new  ArrayObject();
$max = null; //Set the maximum number of recipes to retrieve

//Allows one-liner ingredient list adding.
class Ingredient {
    public $ItemID;
    public $Count;
    
    public function __construct($ItemID, $Count) {
        $this->ItemID = $ItemID;
        $this->Count = $Count;
    }
}

/*
Target output style

{"1":{
 "ID":73902,
 "ExternalID":9137,
 "DataID":3083,
 "Name":"Pile[s] of Pumpkin Pie Spice",
 "Rating":225,
 "Type":8,
 "Count":1,
 "CreatedItemId":504736,
 "Ingredients":[{"ItemID":504475,"Count":1},{"ItemID":504751,"Count":1},{"ItemID":504466,"Count":1},{"ItemID":504545,"Count":1}]
 }
}

{
    "0":{
        "ID":null,
        "ExternalID":null,
        "DataID":1275,
        "Name":"Resilient Seeker Coat",
        "Rating":25,
        "Type":5,
        "Count":1,
        "CreatedItemId":11541,
        "RequiresRecipeItem":false,
        "Ingredients":[{"ItemID":19797,"Count":1},{"ItemID":13094,"Count":1},{"ItemID":13093,"Count":1}]
    },
    "1":{
        "ID":null,
        "ExternalID":null,
        "DataID":3219,
        "Name":"Feast of Veggie Pizzas",
        "Rating":125,
        "Type":8,
        "Count":1,
        "CreatedItemId":12602,
        "RequiresRecipeItem":true,
        "Ingredients":[{"ItemID":12346,"Count":10}]}
    }
}

*/

//Quick and dirty discipline name to ID translation.
$disciplines = array(
    'Huntsman' => 1,
    'Artificer' => 2,
    'Weaponsmith' => 3,
    'Armorsmith' => 4,
    'Leatherworker' => 5,
    'Tailor' => 6,
    'Jeweler' => 7,
    'Chef' => 8
);

$curl = CurlRequest::newInstance("https://api.guildwars2.com/v1/recipes.json") ->exec();
$data = json_decode($curl->getResponseBody(), true);

foreach($data['recipes'] as $recipe_id) {
    $curl_recipe = CurlRequest::newInstance("https://api.guildwars2.com/v1/recipe_details.json?recipe_id={$recipe_id}") ->exec();
    $recipe_details = json_decode($curl_recipe->getResponseBody(), true);
    
    $curl_item = CurlRequest::newInstance("https://api.guildwars2.com/v1/item_details.json?item_id={$recipe_details['output_item_id']}") ->exec();
    $created_item = json_decode($curl_item->getResponseBody(), true);
    
    foreach($recipe_details['disciplines'] as $discipline) {    
        $recipe = new stdClass();
        $recipe->ID = null; //Gw2dbId
        $recipe->ExternalID = null; //Gw2dbExternalId
        $recipe->DataID = $recipe_id;
        $recipe->Name = $created_item['name'];
        $recipe->Rating = (int) $recipe_details['min_rating'];
        $recipe->Type = $disciplines[$discipline];
        $recipe->Count = (int) $recipe_details['output_item_count'];
        $recipe->CreatedItemId = (int) $recipe_details['output_item_id'];
        $recipe->RequiresRecipeItem = in_array("LearnedFromItem", $recipe_details['flags']);
        $recipe->Ingredients = array();
        
        foreach($recipe_details['ingredients'] as $ingredient) {
            $recipe->Ingredients[] = new Ingredient((int) $ingredient['item_id'], (int) $ingredient['count']);
        }
        
        $recipe_list->append($recipe);
    }
    
    if ($max && count($recipe_list) >= $max) 
        break;
}

echo json_encode($recipe_list) . "\n";