var ore = {
    name : 'Ore',
    href : '#click',
    rarity : 'fine',
    img : 'https://dfach8bufmqqv.cloudfront.net/gw2/img/content/675da461.png',
    price : 2
};

var tin = {
    name : 'Tin',
    href : '#click',
    rarity : 'fine',
    img : 'https://dfach8bufmqqv.cloudfront.net/gw2/img/content/675da461.png',
    price : 6
};

var log = {
    name : 'Log',
    href : '#click',
    rarity : 'fine',
    img : 'https://dfach8bufmqqv.cloudfront.net/gw2/img/content/675da461.png',
    price : 10
};

var claw = {
    name : 'Claw',
    href : '#click',
    rarity : 'fine',
    img : 'https://dfach8bufmqqv.cloudfront.net/gw2/img/content/675da461.png',
    price : 20
};

var ingot = {
    name : 'Ingot',
    href : '#click',
    rarity : 'fine',
    img : 'https://dfach8bufmqqv.cloudfront.net/gw2/img/content/675da461.png',
    price : 12,
    recipe : {
        count : 5,
        ingredients : [
            [ore, 10],
            [tin, 1]
        ]
    }
};

var plank = {
    name : 'Plank',
    href : '#click',
    rarity : 'fine',
    img : 'https://dfach8bufmqqv.cloudfront.net/gw2/img/content/675da461.png',
    price : 13,
    recipe : {
        count : 1,
        ingredients : [
            [log, 2]
        ]
    }
};

var dowel = {
    name : 'Dowel',
    href : '#click',
    rarity : 'fine',
    img : 'https://dfach8bufmqqv.cloudfront.net/gw2/img/content/675da461.png',
    price : 20,
    recipe : {
        count : 1,
        ingredients : [
            [plank, 4]
        ]
    }
};

var inscription = {
    name : 'Inscription',
    href : '#click',
    rarity : 'fine',
    img : 'https://dfach8bufmqqv.cloudfront.net/gw2/img/content/675da461.png',
    price : 40,
    recipe : {
        count : 1,
        ingredients : [
            [dowel, 1],
            [claw, 4]
        ]
    }
};

var gun = {
    name : 'Gun',
    href : '#click',
    rarity : 'fine',
    img : 'https://dfach8bufmqqv.cloudfront.net/gw2/img/content/675da461.png',
    price : 500,
    recipe : {
        count : 1,
        ingredients : [
            [inscription, 1],
            [ingot, 10],
            [plank, 3]
        ]
    }
};

var CraftEntry = function(item, count, parent, path) {
    var self   = this;
    var item   = item;
    var parent = parent || null;
    var count  = count || 1;
    var price  = count * item.price;
    var path   = path || [];
        path.push(item.name);
    var boxid  = path.join("-");

    var craftprice = 0;
    var children = [];

    var $item;
    var $craftcost;
    var $childList;

    var TP = 'TP', CRAFT = 'CRAFT';

    var calculateCraftPrice = function() {
        var craftprice = 0;

        $.each(children, function(k, child) {
            craftprice += child.price();
        });

        return craftprice;
    };

    var calculatePrice = function() {
        var selectedPrice = 0;
        if ($item.find('input:checked').val() == CRAFT) {
            selectedPrice = calculateCraftPrice();
        } else {
            selectedPrice = price;
        }

        return selectedPrice;
    };

    var update = function() {
        if ($craftcost) {
            $craftcost.html(formatGW2Money(calculateCraftPrice()));
        }

        if ($item.find('input:checked').val() == TP) {
            $childList.addClass('children-disabled');
        } else {
            $childList.removeClass('children-disabled');
        }

        if (parent) {
            parent.update();
        }
    };

    var render = function() {
        var $entry     = $('<div class="recipe-row">');
        $item          = $('<div class="item-row clearfix">');
        $childList     = $('<div class="children" />');

        var $title = $('<div class="item">')
                        .html('<img width="24" src="'+item.img+'" /> '+count+'x <a href="'+item.href+'" class="rarity-'+item.rarity+'">'+item.name+'</a>')
                        .appendTo($item);

        var $price = $('<div class="options">')
                        .appendTo($item);

        /*
        <span class="label label-important"><input type="radio" /> BUY ( {{ 237436 | gw2money }} )</span>
        <span class="label label-success"><input type="radio" checked="true" /> CRAFT ( {{ 3266  | gw2money }} )</span>
       */

        var renderOption = function(text, price, val, checked, optimal) {
            var $span  = $('<span class="label" />'),
                $label = $('<label>&nbsp;<span class="label-text">' + text + '</span> ( <span class="price">' + formatGW2Money(price) + '</span> ) </label>'),
                $input = $('<input name="' + boxid + '" value="' + val + '" type="radio" />');

            $span.append($input).append($label);

            $span.click(update);

            return $span;
        };

        var $tpcost = renderOption('BUY', price, TP);

        $price.append($tpcost);

        if (item.recipe) {
            var crafts     = Math.ceil(count / item.recipe.count);
            var $ccwrapper = renderOption('CRAFT', 0, CRAFT);
            $craftcost     = $ccwrapper.find('span.price');

            $.each(item.recipe.ingredients, function(k, ingredient) {
                var item  = ingredient[0];
                var count = crafts * ingredient[1];
                var price = item.price * count;

                craftprice += price;

                var entry = new CraftEntry(item, count, self, path);
                children.push(entry);

                $childList.append(entry.render());
            });

            $price.append($ccwrapper);
        }

        if (!$ccwrapper) {
            $tpcost.addClass('label-not-craftable');
            $tpcost.find('label .label-text').html('NOT CRAFTABLE');
        } else {
            if (craftprice != 0 && craftprice < price) {
                $ccwrapper.find('input').attr('checked', true);
                $ccwrapper.addClass('label-success');
                $tpcost.addClass('label-important');
            } else {
                $tpcost.find('input').attr('checked', true);
                $ccwrapper.addClass('label-important');
                $tpcost.addClass('label-success');
            }
        }

        $entry.append($item);
        if ($childList.children().length) {
            $entry.append($childList);
        }

        update();

        return $entry;
    };

    /*
     * expose some methods
     */
    this.render = render;
    this.price  = calculatePrice;
    this.update = update;
};

$(document).ready(function() {
    var $container = null;

    var init = function() {
        $container = $("#recipe_container");

        var $list = $("<div />");
        var entry = new CraftEntry(gun);

        $list.append(entry.render());

        $container.append($list);
    };

    init();
});

var formatGW2Money = function(copper) {
    var string = "";

    var gold = Math.floor(copper / 10000);
    if (gold) {
        copper = copper % (gold * 10000);
        string += gold + "g ";
    }

    var silver = Math.floor(copper / 100);
    if (silver) {
        copper = copper % (silver * 100);
        if (silver) string += silver + "s ";
    }

    if (copper) {
        // round by 2 digits
        copper = Math.round(copper * 100) / 100;

        string += copper + "c ";
    }

    string = string.replace(/( )+$/, '');

    if (!string) {
        return "0c";
    } else {
        return string;
    }
};
