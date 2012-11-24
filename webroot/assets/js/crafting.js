var Crafting = function(item, container, summarycontainer) {
    var self       = this;
    var topentry   = null;
    var $container = $(container);
    var $sumcont   = $(summarycontainer);
    var $summary   = $sumcont.find(".recipe_summary");

    var update = function() {
        var total = 0, sellprice = 0, listingfee = 0, transactionfee = 0, profit = 0;

        sellprice      = $sumcont.find('.recipe_summary_sell_price').data('sell-price');
        transactionfee = Math.round(sellprice * 0.10);
        listingfee     = Math.round(sellprice * 0.05);

        $summary.html("");

        ingredients = {};
        $.each(topentry.ingredients(), function(k, ingredient) {
            if (ingredients[ingredient[1].id] == undefined) {
                ingredients[ingredient[1].id] = ingredient;
            } else {
                ingredients[ingredient[1].id][0] += ingredient[0];
            }
        });

        $.each(ingredients, function(id, ingredient) {
            var $row = $("<tr />");

            $row.append($('<td />').html(ingredient[0]));
            $row.append($('<td data-tooltip-href="'+ingredient[1].gw2db_href+'" />').html(ingredient[1].name).css('font-weight', 'bold').addClass('rarity-' + ingredient[1].rarity));
            $row.append($('<td />').html(formatGW2Money(ingredient[1].price)));
            $row.append($('<td />').html(formatGW2Money(ingredient[1].price * ingredient[0])));

            total += (ingredient[1].price * ingredient[0]);

            $summary.append($row);
        });

        profit = sellprice - total - transactionfee - listingfee;

        $sumcont.find('.recipe_summary_total').html(formatGW2Money(total));
        $sumcont.find('.recipe_summary_sell_price').html(formatGW2Money(sellprice));
        $sumcont.find('.recipe_summary_profit').html(formatGW2Money(profit));
        $sumcont.find('.recipe_summary_transaction_fee').html(formatGW2Money(transactionfee));
        $sumcont.find('.recipe_summary_listing_fee').html(formatGW2Money(listingfee));
    };

    var init = function() {
        topentry = new CraftEntry(item, item.recipe.count, self, [], false);

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
        var $itemWrap  = $('<div class="item-row-wrapper" style="clear: both;" />');
        $item          = $('<div class="item-row">');
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

        var $tpcost = renderOption(item.vendor ? 'VENDOR' : 'TP', price, TP);
        $price.append($tpcost);

        var $ccwrapper = renderOption('CRAFT', 0, CRAFT);
        $price.append($ccwrapper);

        if (item.recipe) {
            var crafts     = Math.ceil(count / item.recipe.count);
            $craftcost     = $ccwrapper.find('span.price');

            $.each(item.recipe.ingredients, function(k, ingredient) {
                var ingre = ingredient[0];
                var count = crafts * ingredient[1];
                var price = ingre.price * count;

                var entry = new CraftEntry(ingre, count, self, path, (k == item.recipe.ingredients.length-1));
                children.push(entry);

                $childList.append(entry.render());
            });
        }

        craftprice = calculateCraftPrice();

        var craftable = (craftprice > 0);
        var buyable   = (price > 0);

        var should_craft = (craftable && !buyable);
        var should_buy   = (buyable && !craftable);

        if (!should_buy && !should_craft) {
            should_craft = craftable && craftprice < price;
            should_buy   = buyable && !should_craft;
        }

        var craft = craftable && should_craft;
        var buy   = buyable && !craft;

        if (path.length == 1 && craftable) {
            craft = true;
            buy   = !craft;
        }

        if (!buyable && !craftable) {
            $ccwrapper.remove();

            $tpcost.addClass('label-warning label-wide');
            $tpcost.find('label .label-text').html('NOT CRAFTED, NOT SOLD');
            $tpcost.find('input').attr('disabled', true);
        } else if (buyable && !craftable) {
            $ccwrapper.addClass('label-inverse label-not-crafted');
            $ccwrapper.find('label .label-text').html('NOT CRAFTED');
            $ccwrapper.find('input').attr('disabled', true);
        }  else if (craftable && !buyable) {
            $tpcost.find('label .label-text').html('NOT SOLD');
            $tpcost.addClass('label-inverse label-not-crafted');
            $tpcost.find('input').attr('disabled', true);
        }
        if (should_craft) {
            $ccwrapper.addClass('label-success');
            $tpcost.addClass('label-important');
        } else if(should_buy) {
            $ccwrapper.addClass('label-important');
            $tpcost.addClass('label-success');
        }

        if (!craft && !buy) {
            $tpcost.find('input').attr('checked', true);
        } else if (craft) {
            $ccwrapper.find('input').attr('checked', true);
        } else if(buy) {
            $tpcost.find('input').attr('checked', true);
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
