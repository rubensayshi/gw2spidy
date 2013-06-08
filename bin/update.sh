#!/bin/sh

if [ -z "$ROOT" ]; then
    ROOT=`php -r "echo dirname(dirname(realpath('$(pwd)/$0')));"`
    export ROOT
fi

flush_twig_cache() {
	rm -rf $ROOT/tmp/twig-cache/*
}

flush_varnish() {
    varnishadm ban.url "^"
}

flush_apc() {
    php $ROOT/tools/purge-cache.php -a
}

flush_twig_cache
flush_apc
flush_varnish
