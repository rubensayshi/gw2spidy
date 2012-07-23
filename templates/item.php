<?php
/**
 * @var \GW2Spidy\DB\Item    $item
 */
?>
<h2><?php echo $item->getName(); ?> <small><?php echo $item->getRestrictionLevel(); ?></small></h2>
<div id="placeholder" style="width:600px;height:300px;"></div>

<script type="text/javascript">
$.ajax("/chart.php?id=<?php echo $item->getDataId() ?>", {
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
                mode: "time",
                timeformat: "%y-%0m-%0d %H:%S"
            }
        });
    }
});
</script>