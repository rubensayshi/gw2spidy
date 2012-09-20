var GW2SpidyItemHistory = (function() {
    var $container = null,
        $ul = null,
        itemHistory  = {},
        itemSnippets = {},
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
        $ul = $('<ul class="nav nav-pills nav-stacked item-history-list">').appendTo($container);

        buildList();
    };

    var initData = function() {
        if (json = window.localStorage.getItem(HISTORY_KEY)) {
            itemHistory = JSON.parse(json);
        }

        if (json = window.localStorage.getItem(SNIPPET_KEY)) {
            itemSnippets = JSON.parse(json);
        }

        itemSnippets = itemSnippets || {};
        itemHistory  = itemHistory  || {};

        cleanData();
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
        $.each(itemHistory, function(visited, itemID) {
            if (itemSnippets[itemID]) {
                $(itemSnippets[itemID]).prependTo($ul);
            }
        });
    };

    var init = function() {
        if (typeof window.localStorage === 'undefined') {
            return;
        }

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

        window.localStorage.setItem(SNIPPET_KEY, JSON.stringify(itemSnippets));
        window.localStorage.setItem(HISTORY_KEY, JSON.stringify(itemHistory));
    };

    init();

    $(document).ready(function() {
        setup();
    });

    return {
        addItem : addItem
    };
})();