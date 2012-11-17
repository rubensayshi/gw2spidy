backend default {
    .host = "localhost";
    .port = "8080";
}

sub vcl_recv {
    if (!req.http.host ~ "gw2spidy") {
            return(pass);    
    }

    set req.backend = default;
    if (req.http.X-Forwarded-Proto == "https" ) {
        set req.http.X-Forwarded-Port = "443";
    } else {
        set req.http.X-Forwarded-Port = "80";
    }
        
    if (req.url ~ "^/login" || req.url ~ "^/logout" || req.url ~ "^/watchlist") {
        return(pass);
    } else {
        unset req.http.cookie;
    }
    
    # uncomment to have varnish serve an error page
	# error 500 "Some simple error message here";

    if (req.url ~ "^/tmp/.*\.sql\.gz$") {
        return(pipe);
    }

    if (req.url ~ "no_cache") {
        return(pipe);
    }

    return(lookup);
}

sub vcl_fetch {
    set beresp.ttl = 1m;
    set beresp.grace = 1h;

    unset beresp.http.expires;
    if (req.url ~ "^/login" || req.url ~ "^/logout" || req.url ~ "^/watchlist") {
        return(deliver);
    } else {
        unset beresp.http.set-cookie;   
    }
    
    if (beresp.http.x-varnish-no-cache) {
       set beresp.ttl = 0s;
       return (deliver);
    }
    
    if (beresp.status > 404 || beresp.status == 301) {
       set beresp.ttl = 1m;
       return (deliver);
    }

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
            set beresp.ttl = 2h;
            
            if (req.url ~ "^/api/v.*/.+/types" || req.url ~ "^/api/v.*/.+/disciplines" || req.url ~ "^/api/v.*/.+/rarities") {
                set beresp.ttl = 24h;
            }
            if (req.url ~ "^/api/v.*/.+/all-items") {
                set beresp.ttl = 5m;
            }
            if (req.url ~ "^/api/v.*/.+/items" || req.url ~ "^/api/v.*/.+/recipes") {
                set beresp.ttl = 15m;
            }
            if (req.url ~ "^/api/v.*/.+/item/" || req.url ~ "^/api/v.*/.+/recipe/") {
                set beresp.ttl = 5m;
            }
            if (req.url ~ "^/api/v.*/.+/listings/") {
                set beresp.ttl = 15m;
            }
            if (req.url ~ "^/api/v.*/.+/item-search/") {
                set beresp.ttl = 15m;
            }
            if (req.url ~ "^/api/v.*/.+/gem-price" || req.url ~ "^/api/v.*/.+/gem-history/") {
                set beresp.ttl = 15m;
            }
        }
    }

    set beresp.http.X-Varnish-TTL = beresp.ttl;


    if (req.http.host ~ "^beta.gw2spidy.com$" && !(req.url ~ "api")) {
        set beresp.ttl = 0s;
    }
}

# The data on which the hashing will take place
sub vcl_hash {
    hash_data(req.url);

    if (req.http.host) {
        hash_data(req.http.host);
    } else {
        hash_data(server.ip);
    }

    # hash cookies for object with auth
    if (req.http.Cookie) {
        hash_data(req.http.Cookie);
    }

    # If the client supports compression, keep that in a different cache
    if (req.http.Accept-Encoding) {
        hash_data(req.http.Accept-Encoding);
    }

    return (hash);
}
 
# The routine when we deliver the HTTP request to the user
# Last chance to modify headers that are sent to the client
sub vcl_deliver {
    if (obj.hits > 0) { 
        set resp.http.X-Varnish-Cache = "HIT";
    } else {
        set resp.http.X-Varnish-Cache = "MISS";
    }

    # Remove some headers: PHP version
    unset resp.http.X-Powered-By;
    # Remove some headers: Apache version & OS
    unset resp.http.Server;
    unset resp.http.X-Drupal-Cache;
    unset resp.http.X-Varnish;
    unset resp.http.Via;
    unset resp.http.Link;

    return (deliver);
}

