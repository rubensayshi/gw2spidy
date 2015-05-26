var GW2SpidyItemHistory = (function() {
    var $container = null,
        $ul = null,
        itemHistory  = {},
        itemSnippets = {},
        VERSION     = "20121003v1",
        VERSION_KEY = "GW2SPIDY-ITEM-HISTORY-VERSION",
        HISTORY_KEY = "GW2SPIDY-ITEM-HISTORY",
        SNIPPET_KEY = "GW2SPIDY-ITEM-SNIPPETS",
        TIMEOUT_SEC = 3600;

    var getObjectLength = function() {
        if(Object.keys) {
            return Object.keys(itemHistory).length;
        } else {
            var l = 0;
            $.each(itemHistory, function(visited, itemID) {
                l++;
            });

            return l;
        }
    };

    var setup = function() {
        $container = $("#item-history-placeholder");

        $('<h3>Item history</h3>').appendTo($container);
        $ul = $('<ul class="nav nav-stacked item-history-list">').appendTo($container);

        buildList();
    };

    var initData = function() {
        localforage.getItem(HISTORY_KEY).then(function(json) {
            itemHistory = JSON.parse(json);

            return localforage.getItem(SNIPPET_KEY).then(function(json) {
                itemSnippets = JSON.parse(json);

                return localforage.getItem(VERSION_KEY).then(function(version) {
                    if (!version || version != VERSION) {
                        localforage.setItem(VERSION_KEY, VERSION);
                        localforage.setItem(HISTORY_KEY, JSON.stringify({}));
                        localforage.setItem(SNIPPET_KEY, JSON.stringify({}));

                        itemSnippets = null;
                        itemHistory = null;
                    }

                    itemSnippets = itemSnippets || {};
                    itemHistory = itemHistory || {};

                    cleanData();
                });
            });
        })
        .then(void 0, function(err) { console.error(err); });
    };

    var cleanData = function() {
        var l = getObjectLength(itemHistory);
        var c = 0;
        $.each(itemHistory, function(visited, itemID) {
            if (visited < (new Date()).getTime() - (TIMEOUT_SEC * 1000) || l - c > 10) {
                itemSnippets[itemID] = undefined;
                itemHistory[visited] = undefined;
            }

            c++;
        });
    };

    var buildList = function() {
        var hasItems = false;

        $.each(itemHistory, function(visited, itemID) {
            if (itemSnippets[itemID]) {
                $(itemSnippets[itemID]).prependTo($ul);
                hasItems = true;
            }
        });

        if (hasItems) {
            _gaq.push(['_trackEvent', 'item-history', 'has-items']);
            $container.on('click', function() {
                _gaq.push(['_trackEvent', 'item-history', 'clicked']);
            });
        }
    };

    var init = function() {
        initData();
    };

    var addItem = function(itemID, snippet) {
        $.each(itemHistory, function(_visited, _itemID) {
            if (_itemID == itemID) {
                itemHistory[_visited] = undefined;
            }
        });

        itemHistory[(new Date()).getTime()] = itemID;
        itemSnippets[itemID] = snippet;

        cleanData();

        localforage.setItem(SNIPPET_KEY, JSON.stringify(itemSnippets));
        localforage.setItem(HISTORY_KEY, JSON.stringify(itemHistory));
    };

    init();

    $(document).ready(function() {
        setup();
    });

    return {
        addItem : addItem
    };
})();
