var GW2SpidyWatchlist = (function() {
    var VERSION       = "20121117v2",
    	VERSION_KEY   = "GW2SPIDY-WATCHLIST-VERSION",
    	USER_ID_KEY   = "GW2SPIDY-WATCHLIST-USER-ID",
        WATCHLIST_KEY = "GW2SPIDY-WATCHLIST",
        watchlist;

    var setup = function() {
        $('.js-logout').on('click', function() {
        	watchlist = [];
        	store();
        });
    	
        $(".js-watchlist-remove").each(function() {
        	var id = $(this).closest('[data-id]').data('id');
        	
        	if (watchlist.indexOf(id) != -1) {
        		$(this).removeClass('hide');
        		$(this).closest('[data-id]').find('.js-watchlist-add').addClass('hide');
        	}
        	
        	if (!$(this).hasClass('hide')) {
        		addItem(id, true);
        	}
        });
        
        $(".js-watchlist-add").on('click', function(e) {
        	var id = $(this).closest('[data-id]').data('id');
        	addItem(id);
        });
        
        $(".js-watchlist-remove").on('click', function(e) {
        	var id = $(this).closest('[data-id]').data('id');
        	while ((k = watchlist.indexOf(id)) != -1) {
        		watchlist[k] = null;
        	}
        	
        	store();
        });
        
        store();
    };
    
    var addItem = function(id, storeLater) {
    	storeLater = storeLater || false;
    	
    	if (watchlist.indexOf(id) == -1) {
    		watchlist.push(id);
    	}
    	
    	if (!storeLater) {    	
    		store();
    	}
    };
    
    var store = function() {
        window.localStorage.setItem(WATCHLIST_KEY, JSON.stringify(watchlist));
    };
    
    var initData = function() {
        if (json = window.localStorage.getItem(WATCHLIST_KEY)) {
        	watchlist = JSON.parse(json);
        }

        if (!(version = window.localStorage.getItem(VERSION_KEY)) || version != VERSION) {
            window.localStorage.setItem(VERSION_KEY, VERSION);

            watchlist = null;
        }

        watchlist = watchlist || [];
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