<?php
/**
 * @var \GW2Spidy\DB\Item    $item
 */
?>
<h1><?php echo $item->getName(); ?> <small><?php echo $item->getRestrictionLevel(); ?></small></h1>
<div id="placeholder" style="width: 950px; height: 400px;"></div>

<script type="text/javascript">
$.ajax("/index.php?act=chart&id=<?php echo $item->getDataId() ?>", {
    success: function(chart) {
        chart = $.parseJSON(chart);

        $.each(chart, function(dataserie) {
            $.each(dataserie, function(entry) {
                var date = new Date(entry[0]);

                console.log("" + date + "");
            });
        });
        $.plot($("#placeholder"), chart, {
            xaxis: {
                mode:            "time",
                timeformat:      "%y-%0m-%0d %H:%S",
                twelveHourClock: false
            }
        });
    }
});
</script>