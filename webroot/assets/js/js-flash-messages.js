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

                window.localStorage.setItem(FLASHED_KEY, JSON.stringify(flashed));
            });
        });
    };

    var initData = function() {
        if (json = window.localStorage.getItem(FLASHED_KEY)) {
            flashed = JSON.parse(json);
        }

        flashed = flashed || {};
    };

    var init = function() {
        if (typeof window.localStorage === 'undefined') {
            return;
        }

        initData();
    };

    init();

    $(document).ready(function() {
        setup();
    });
})();