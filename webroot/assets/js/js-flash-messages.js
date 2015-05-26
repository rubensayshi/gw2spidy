var GW2SpidyFlashMessages = (function() {
    var $wrapper    = null,
        $container  = null,
        $library    = null,
        flashed     = {},
        FLASHED_KEY = "GW2SPIDY-FLASH-MESSAGES";

    var setup = function() {
        $wrapper   = $("#flash-message-wrapper");
        $container = $("#flash-message-container");
        $library   = $("#flash-message-library");

        showFlashMessages();
    };

    var showFlashMessages = function() {
        $library.children().each(function(k, flash) {
            var flashId = $(flash).attr('id');

            if (flashed[flashId]) {
                return;
            }

            $container.append($(flash).show());
            $wrapper.show();
            $(flash).find('.close').click(function() {
                flashed[flashId] = true;

                setTimeout(function() {
                    if ($container.children().length == 0) {
                        $wrapper.fadeOut();
                    }
                },1);

                localforage.setItem(FLASHED_KEY, JSON.stringify(flashed));
            });
        });
    };

    var initData = function() {
        localforage.getItem(FLASHED_KEY).then(function(json) {
            console.log(json);

            flashed = JSON.parse(json);

            flashed = flashed || {};
        })
        .then(void 0, function(err) { console.error(err); });
    };

    var init = function() {
        initData();
    };

    init();

    $(document).ready(function() {
        setup();
    });
})();
