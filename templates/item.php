<?php
/**
 * @var \GW2Spidy\DB\Item    $item
 */
?>
<?php echo Application::getInstance()->render("item_snippet", array('item' => $item, 'tag' => 'div')) ?>

<div id="placeholder" style="width: 950px; height: 400px;"></div>

<script type="text/javascript">
$.ajax("/index.php?act=chart&id=<?php echo $item->getDataId() ?>", {
    success: function(chart) {
        chart = $.parseJSON(chart);

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