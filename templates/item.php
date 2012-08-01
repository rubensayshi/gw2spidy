<?php

use GW2Spidy\Application;

/**
 * @var \GW2Spidy\DB\Item    $item
 */
?>
<?php echo Application::getInstance()->render("item_snippet", array('item' => $item, 'tag' => 'div')) ?>

<div id="placeholder" style="width: 950px; height: 400px;"></div>
<p>
You can zoom into the chart by drawing a selection (xaxis) and use the button below to reset. <br />
<button class="btn" id="clear-selection">Reset Zoom</button>
</p>


<script type="text/javascript">
$.ajax("/index.php?act=chart&id=<?php echo $item->getDataId() ?>", {
    success: function(chartdata) {
        var chartdata   = $.parseJSON(chartdata);
        var placeholder = $("#placeholder");
        var options     = {
            series: {
                lines:  { show: true },
                points: { show: true }
            },
            xaxis: {
                mode:            "time",
                timeformat:      "%y-%0m-%0d %H:%S",
                twelveHourClock: false
            },
            selection: { mode: "x" }
        };
        var xaxis_default = null;

        placeholder.bind("plotselected", function (event, ranges) {
            if (xaxis_default === null) {
                xaxis_default = {
                    from: plot.getAxes().xaxis.datamin,
                    to:   plot.getAxes().xaxis.datamax
                };
            }

            plot = $.plot(placeholder, chartdata,
                          $.extend(true, {}, options, {
                              xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
                          }));
        });

        var plot = $.plot(placeholder, chartdata, options);

        $("#clear-selection").click(function () {
            plot.clearSelection();

            plot = $.plot(placeholder, chartdata,
                          $.extend(true, {}, options, {
                              xaxis: { min: xaxis_default.from, max: xaxis_default.to }
                          }));
        });
    }
});
</script>