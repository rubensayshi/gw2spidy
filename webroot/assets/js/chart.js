Highcharts.setOptions({global: { useUTC: false }});

var GW2SpidyChart = function(url, container, set_options) {
    var self       = this;
    var url        = url;
    var $container = $(container);
    var container  = $container[0];

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
                    var y = series.options.gw2money ? formatGW2Money(item.y) : item.y;
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
                {type: 'day',  count: 1,  text: '1d'},
                {type: 'day',  count: 3,  text: '3d'},
                {type: 'week', count: 1,  text: '1w'},
                {type: 'all',             text: 'all'}
            ],
            selected : 2
        },
        yAxis : {
            labels : {
                formatter: function() {
                    return formatGW2Money(this.value);
                },
                useHTML: true
            }
        }
    };

    options = $.extend(true, {}, options, set_options);

    var init = function() {
        update();
    };

    var update = function() {
        $.getJSON(url, render);
    };

    var render = function(data) {
        // Create the chart
        var chart = new Highcharts.StockChart($.extend(true, {}, options, {
            series : data
        }));
    };

    init();

    this.update = update;
    this.render = render;
};