
backend default {
    .host = "localhost";
    .port = "8080";
}

sub vcl_recv {
    if (!req.http.host ~ "gw2spidy.com$") {
            return(pipe);    
    }

#   error 500 "More downtime ... TP is down anyway ... need to get my shit sorted sorry ...";

    unset req.http.cookie;
    
    if (req.url ~ "no_cache") {
        return(pipe);
    }
}

sub vcl_fetch {
    set beresp.ttl = 1m;
    set beresp.grace = 1h;

    unset beresp.http.expires;

    # homepage, trending and gem data
    if (req.url ~ "^/$") {
        set beresp.ttl = 3m;
    }

    # queue and slot status
    if (req.url ~ "^/status") {
        set beresp.ttl = 0s;
    }

    # versioned assets
    if (req.url ~ "^/assets/v.*") {
        set beresp.http.cache-control = "max-age=31536000";
        set beresp.ttl = 365d;
    }

    # item lists - search and types
    if (req.url ~ "^/type" || req.url ~ "^/search") {
        set beresp.ttl = 1h;
    }

    # item and recipe detail pages
    if (req.url ~ "^/item" || req.url ~ "^/recipe") {
        set beresp.ttl = 15m;
    }

    # item charts, gem detail and gem charts
    if (req.url ~ "^/chart" || req.url ~ "^/gem" || req.url ~ "^/gem_chart") {
        set beresp.ttl = 10m;
    }
    
    # API
    if (req.url ~ "^/api") {
    	# the old invite-only API
	    if (!(req.url ~ "^/api/v0.9")) {
	        set beresp.ttl = 15m;
	    } else {
	        set beresp.ttl = 1h;
	        
	        if (req.url ~ "^api/v.*/.+/types" || req.url ~ "^api/v.*/.+/disciplines" || req.url ~ "^api/v.*/.+/rarities") {
	            set beresp.ttl = 24h;
	        }
	        if (req.url ~ "^api/v.*/.+/items" || req.url ~ "^api/v.*/.+/recipes") {
	            set beresp.ttl = 24h;
	        }
            if (req.url ~ "^api/v.*/.+/item/" || req.url ~ "^api/v.*/.+/recipe/") {
                set beresp.ttl = 3m;
            }
	        if (req.url ~ "^api/v.*/.+/listings/") {
	            set beresp.ttl = 15m;
	        }
	        if (req.url ~ "^api/v.*/.+/item-search/") {
	            set beresp.ttl = 15m;
	        }
	        if (req.url ~ "^api/v.*/.+/gem-price" || req.url ~ "^api/v.*/.+/gem-history/") {
	            set beresp.ttl = 15m;
	        }
	    }
    }

    if (req.http.host ~ "^beta.gw2spidy.com$" && !(req.url ~ "api")) {
        set beresp.ttl = 0s;
    }
}

sub vcl_hit {

}

sub vcl_miss {

}


sub vcl_deliver {
    if (obj.hits > 0) {
        set resp.http.X-Varnish-Cache = "HIT";
    } else {
        set resp.http.X-Varnish-Cache = "MISS";
    }
}
