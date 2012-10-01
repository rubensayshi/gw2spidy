var ore = {
    id : 1,
    name : 'Ore',
    href : '#click',
    rarity : 'common',
    img : 'https://dfach8bufmqqv.cloudfront.net/gw2/img/content/675da461.png',
    price : 2
};

var tin = {
    id : 2,
    name : 'Tin',
    href : '#click',
    rarity : 'common',
    img : 'https://dfach8bufmqqv.cloudfront.net/gw2/img/content/675da461.png',
    price : 6
};

var log = {
    id : 3,
    name : 'Log',
    href : '#click',
    rarity : 'common',
    img : 'https://dfach8bufmqqv.cloudfront.net/gw2/img/content/675da461.png',
    price : 10
};

var claw = {
    id : 4,
    name : 'Claw',
    href : '#click',
    rarity : 'fine',
    img : 'https://dfach8bufmqqv.cloudfront.net/gw2/img/content/675da461.png',
    price : 20
};

var ingot = {
    id : 5,
    name : 'Ingot',
    href : '#click',
    rarity : 'masterwork',
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
    id : 6,
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
    id : 7,
    name : 'Dowel',
    href : '#click',
    rarity : 'rare',
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
    id : 8,
    name : 'Inscription',
    href : '#click',
    rarity : 'exotic',
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
    id : 9,
    name : 'Gun',
    href : '#click',
    rarity : 'legendary',
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

var Crafting = function(container, summary, total, item) {
    var self       = this;
    var topentry   = null;
    var $container = $(container);
    var $summary   = $(summary);
    var $total     = $(total);

    var update = function() {
        $summary.html("");

        ingredients = {};
        $.each(topentry.ingredients(), function(k, ingredient) {
            if (ingredients[ingredient[1].id] == undefined) {
                ingredients[ingredient[1].id] = ingredient;
            } else {
                ingredients[ingredient[1].id][0] += ingredient[0];
            }
        });

        var total = 0;
        $.each(ingredients, function(id, ingredient) {
            var $row = $("<tr />");

            $row.append($('<td />').html(ingredient[0]));
            $row.append($('<td data-tooltip-href="'+ingredient[1].gw2db_href+'" />').html(ingredient[1].name).css('font-weight', 'bold').addClass('rarity-' + ingredient[1].rarity));
            $row.append($('<td />').html(formatGW2Money(ingredient[1].price)));
            $row.append($('<td />').html(formatGW2Money(ingredient[1].price * ingredient[0])));

            total += (ingredient[1].price * ingredient[0]);

            $summary.append($row);
        });

        $total.html(formatGW2Money(total));
    };

    var init = function() {
        topentry = new CraftEntry(item, 1, self, [], false);

        $container.append(topentry.render());

        update();
        WP_LoadTooltips($container);
        WP_LoadTooltips($summary);
    };

    this.update = update;

    init();
};

var CraftEntry = function(item, count, parent, path, last) {
    var self   = this;
    var item   = item;
    var parent = parent || null;
    var count  = count || 1;
    var price  = count * item.price;
    var last   = last || false;
    var path   = path ? path.slice() : [];
        path.push(item.name);
    var boxid  = path.join("-");

    var craftprice = 0;
    var children = [];

    var $item;
    var $craftcost;
    var $childList;

    var TP = 'TP', CRAFT = 'CRAFT';

    function calculateCraftPrice() {
        var craftprice = 0;

        $.each(children, function(k, child) {
            craftprice += child.price();
        });

        return craftprice;
    };

    function calculatePrice() {
        var selectedPrice = 0;
        if ($item.find('input:checked').val() == CRAFT) {
            selectedPrice = calculateCraftPrice();
        } else {
            selectedPrice = price;
        }

        return selectedPrice;
    };

    function update(propegate) {
        if ($craftcost) {
            $craftcost.html(formatGW2Money(calculateCraftPrice()));
        }

        if ($item.find('input:checked').val() == TP) {
            $childList.addClass('children-disabled');
        } else {
            $childList.removeClass('children-disabled');
        }

        if (propegate && parent) {
            parent.update(true);
        }
    };

    function getIngredients() {
        var ingredients = [];

        if ($item.find('input:checked').val() == TP) {
            ingredients.push([count, item]);
        } else {
            $.each(children, function(k, entry) {
                $.each(entry.ingredients(), function(k, ingredient) {
                    ingredients.push(ingredient);
                });
            });
        }

        return ingredients;
    };

    function render() {
        var $entry     = $('<div class="recipe-row">');
        var $itemWrap  = $('<div class="item-row-wrapper" />');
        $item          = $('<div class="item-row clearfix">');
        $childList     = $('<div class="children" />');

        $entry.append($itemWrap.append($item));
        var $struct = $('<div style="position: absolute; top: 0px; left: 0px;"></div>')
                        .appendTo($itemWrap);

        var $title = $('<div data-tooltip-href="'+item.gw2db_href+'" class="item" title="' + item.name + '">')
                        .html('<img width="24" src="'+item.img+'" /> '+count+'x <a href="'+item.href+'" class="rarity-'+item.rarity+'">'+item.name+'</a>')
                        .appendTo($item);

        var $price = $('<div class="options">')
                        .appendTo($item);

        var renderOption = function(text, price, val, checked, optimal) {
            var $span  = $('<span class="label" />'),
                $label = $('<label>&nbsp;<span class="label-text">' + text + '</span> (<span class="price">' + formatGW2Money(price) + '</span>)</label>'),
                $input = $('<input name="' + boxid + '" value="' + val + '" type="radio" />');

            $span.append($input).append($label);

            $span.click(function() { update(true); });

            return $span;
        };

        var $tpcost = renderOption('BUY', price, TP);

        $price.append($tpcost);

        if (item.recipe) {
            var crafts     = Math.ceil(count / item.recipe.count);
            var $ccwrapper = renderOption('CRAFT', 0, CRAFT);
            $craftcost     = $ccwrapper.find('span.price');

            $.each(item.recipe.ingredients, function(k, ingredient) {
                var ingre = ingredient[0];
                var count = crafts * ingredient[1];
                var price = ingre.price * count;

                craftprice += price;

                var entry = new CraftEntry(ingre, count, self, path, (k == item.recipe.ingredients.length-1));
                children.push(entry);

                $childList.append(entry.render());
            });

            $price.append($ccwrapper);
        }

        if (!$ccwrapper) {
            $tpcost.addClass('label-inverse label-not-craftable');
            $tpcost.find('label .label-text').html('NOT CRAFTABLE');
            $tpcost.find('input').attr('checked', true);
            $tpcost.find('input').attr('readonly', true);
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

        $struct.css('left', -1 * ((path.length-1) * 25));

        var step = self;
        for (var i = 1; i < path.length; i++) {
            var icon     = 'empty';
            var icontext = '';

            // deepest step, either K or L
            if (step == self) {
                if (step.last) {
                    icon     = 'last';
                    icontext = '└';
                } else {
                    icon     = 'split';
                    icontext = '├';
                }
            } else {
                if (step.last) {
                    icon     = 'empty';
                    icontext = '';
                } else {
                    icon     = 'cont';
                    icontext = '│';
                }
            }


            $struct.prepend($('<div class="folder-structure-icon folder-structure-icon-'+icon+'" />').html(icontext));

            step = step.parent;
        }

        if ($childList.children().length) {
            $entry.append($childList);
        }

        update(false);

        return $entry;
    };

    /*
     * expose some methods
     */
    this.parent      = parent;
    this.last        = last;
    this.render      = render;
    this.ingredients = getIngredients;
    this.price       = calculatePrice;
    this.update      = update;
};

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
