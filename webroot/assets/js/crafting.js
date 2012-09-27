var ore = {
    name : 'Ore',
    price : 2
};

var tin = {
    name : 'Tin',
    price : 6
};

var log = {
    name : 'Log',
    price : 10
};

var claw = {
    name : 'Claw',
    price : 20
};

var ingot = {
    name : 'Ingot',
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

        if (parent) {
            parent.update();
        }
    };

    var render = function() {
        var $entry     = $("<div class='well' />");
        $item          = $("<div />").addClass('clearfix');
        var $childList = $("<div />");

        var $title = $("<div />")
                        .html(count + " x " + item.name)
                        .css('float', 'left')
                        .appendTo($item);

        var $price = $("<div />")
                        .css('float', 'left')
                        .css('margin-left', '10px')
                        .appendTo($item);

        var $tpcost = $("<div />")
                        .html(" ( " + formatGW2Money(item.price) + " x " + count + " = " + formatGW2Money(price) + " )")
                        .prepend($("<label> TP </label>").css('display', 'inline'))
                        .prepend($('<input name="' + boxid + '" value="' + TP + '" type="radio" />').click(update));

        $price.append($tpcost);

        if (item.recipe) {
            var crafts     = Math.ceil(count / item.recipe.count);
            var $ccwrapper = $("<div />")
                                .prepend($("<label> CRAFT </label>").css('display', 'inline'))
                                .prepend($('<input name="' + boxid + '" value="' + CRAFT + '" type="radio" />').click(update));

            $craftcost = $('<div style="display: inline-block;" />');
            $ccwrapper.append("( ", $craftcost, " )");

            $.each(item.recipe.ingredients, function(k, ingredient) {
                var item  = ingredient[0];
                var count = crafts * ingredient[1];
                var price = item.price * count;

                craftprice += price;

                var entry = new CraftEntry(item, count, self, path);
                children.push(entry);

                $childList.append(entry.render());
            });

            $craftcost.append(" = " + formatGW2Money(craftprice));

            $price.append($ccwrapper);
        }

        if (craftprice != 0 && craftprice < price) {
            $ccwrapper.find('input').attr('checked', true);
        } else {
            $tpcost.find('input').attr('checked', true);
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
