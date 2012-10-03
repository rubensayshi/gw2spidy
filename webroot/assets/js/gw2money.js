
var formatGW2Money = function(copper) {
    var goldImg   = '<i class="gw2money-gold"></i>';
    var silverImg = '<i class="gw2money-silver"></i>';
    var copperImg = '<i class="gw2money-copper"></i>';

    var string = "";

    if (negative = copper < 0) {
        copper *= -1;
    }

    var gold = Math.floor(copper / 10000);
    if (gold) {
        copper = copper % (gold * 10000);
        gold = negative ? gold*-1 : gold;

        string += gold + " " + goldImg + " ";
    }

    var silver = Math.floor(copper / 100);
    if (silver) {
        copper = copper % (silver * 100);
        silver = negative ? silver*-1 : silver;

        silver + " " + silverImg + " ";
    }

    if (copper) {
        // round copper by 2 digits
        copper = Math.round(copper * 100) / 100;
        copper = negative ? copper*-1 : copper;

        string += copper + " " + copperImg + " ";
    }

    string = string.replace(/( )+$/, '');

    if (!string) {
        return "0 " + copperImg;
    } else {
        return string;
    }
};