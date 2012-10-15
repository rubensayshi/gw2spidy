Highcharts.setOptions({global: { useUTC: false }});

var GW2SpidyChart = function(url, container, set_options) {
    var self       = this;
    var url        = url;
    var $container = $(container);
    var container  = $container[0];

    var roundNumber = function(num, dec) {
        var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
        return result;
    };

    var options = {
        chart : {
            type: 'line',
            renderTo : container,
            height   : 500
        },
        exporting : {
            enabled: false
        },
        tooltip: {
            formatter: function() {
                var pThis = this,
                items     = pThis.points || splat(pThis),
                series    = items[0].series,
                s;

                // build the header
                s = [series.tooltipHeaderFormatter(items[0].key)];

                // build the values
                $.each(items, function (key, item) {
                    var series = item.series;
                    var y = series.options.gw2money ? formatGW2Money(item.y) : roundNumber(item.y, 2);
                    s.push('<span style="color:'+ series.color +'">' + series.name + '</span>: <b>' + y + '</b> <br />');
                });

                // footer
                // s.push(options.footerFormat || ''); // todo how to get the options xD

                return s.join('');
            },
            useHTML: true
        },
        legend : {
            enabled: true,
            padding: 6,
            floating: false,
            verticalAlign: "top",
            width: 800,
            itemWidth: 200
        },
        rangeSelector : {
            buttons: [
                {type: 'day',   count: 1,  text: '1d'},
                {type: 'day',   count: 3,  text: '3d'},
                {type: 'week',  count: 1,  text: '1w'},
                {type: 'month', count: 1,  text: '1m'},
                {type: 'all',             text: 'all'}
            ],
            selected : 2
        },
        yAxis : [
             {
                 title : { text : 'coin', style : { color : '#AA4643'} },
                 labels : {
                     formatter: function() {
                         return formatGW2Money(this.value);
                     },
                     useHTML: true
                 },
             },
             {
                 height: 200,
                 top: 205,
                 gridLineWidth : 0,
                 offset: 0,
                 title : { text : 'volume', style : { color : '#4572A7'} },
                 opposite : true
             },
        ]
    };

    options = $.extend(true, {}, options, set_options);

    var init = function() {
        update();
    };

    var update = function() {
        $.getJSON(url, render);
    };

    var render = function(data) {
        $.each(data, function(k, serie) {
            if (serie.type == 'column') {
                vserie = $.extend(true, {}, serie, {
                   dataGrouping : {
                       approximation : 'average'
                   }
                });
            }
        });

        // Create the chart
        var chart = new Highcharts.StockChart($.extend(true, {}, options, {
            series : data
        }));
    };

    init();

    this.update = update;
    this.render = render;
};