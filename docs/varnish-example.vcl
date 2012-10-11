
backend default {
    .host = "localhost";
    .port = "8080";
}

sub vcl_recv {
    if (!req.http.host ~ "gw2spidy.com$") {
        return(pipe);    
    }
    
    unset req.http.cookie;

# uncomment to have varnish serve a downtime page
#   error 500 "More downtime ... TP is down anyway ... need to get my shit sorted sorry ...";

    if (req.url ~ "no_cache") {
        return(pipe);
    }
}

sub vcl_fetch {
    set beresp.ttl = 1m;
    set beresp.grace = 1m;

    unset beresp.http.expires;

    if (req.url ~ "^/$") {
        set beresp.ttl = 3m;
    }

    if (req.url ~ "^/status") {
        set beresp.ttl = 0s;
    }

    if (req.url ~ "^/assets/v.*") {
        set beresp.http.cache-control = "max-age=31536000";
        set beresp.ttl = 365d;
    }

    if (req.url ~ "^/type" || req.url ~ "^/search") {
        set beresp.ttl = 1h;
    }

    if (req.url ~ "^/item") {
        set beresp.ttl = 1d;
    }

    if (req.url ~ "^/chart" || req.url ~ "^/gem" || req.url ~ "^/gem_chart") {
        set beresp.ttl = 10m;
    }

    if (req.http.host ~ "beta") {
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
