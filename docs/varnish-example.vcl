backend default {
        .host = "localhost";
        .port = "8080";
}

sub vcl_recv {
        unset req.http.cookie;
}

sub vcl_fetch {
        set beresp.ttl = 1m;
        set beresp.grace = 1m;

        unset beresp.http.expires;

        if (req.url ~ "^/$") {
                set beresp.ttl = 1d;
        }

        if (req.url ~ "^/status") {
                set beresp.ttl = 0s;
        }

        if (req.url ~ "^/assets/v.*") {
                set beresp.http.cache-control = "max-age=31536000";
                set beresp.ttl = 365d;
        }

        if (req.url ~ "^/item" || req.url ~ "^/type" || req.url ~ "^/search") {
                set beresp.ttl = 1d;
        }

        if (req.url ~ "^/chart" || req.url ~ "^/gem" || req.url ~ "^/gem_chart") {
                set beresp.ttl = 10m;
        }

        if (req.url ~ "^/type") {
                set beresp.ttl = 1d;
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
