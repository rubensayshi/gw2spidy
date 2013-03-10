class gw2spidy {
  exec { "apt-get update":
    command => "sudo apt-get update"
  }

  $packages = [
    "vim",
    "curl",
    "tmux",
    "git",
    "nginx",
    "varnish",
    "php5",
    "php5-cli",
    "php5-fpm",
    "php5-curl",
    "php5-mysql",
    "php5-memcache",
    "php5-gd",
    "php-pear",
    "redis-server",
    "memcached",
    "mysql-server"
  ]

  package {
    $packages: ensure => "installed",
    require => Exec["apt-get update"]
  }

  $password = "root"
  exec { "mysql root password":
    require => Package["mysql-server"],
    unless => "mysqladmin -uroot -p$password status",
    command => "mysqladmin -uroot password $password",
  }

  exec { "setup-project-database":
    command => "mysqladmin -uroot -p$password create gw2spidy; mysql -uroot -p$password gw2spidy < config/schema.sql",
    cwd => "/vagrant",
    require => Exec["mysql root password"]
  }

  file { "/vagrant/config/cnf/env":
    content => template("gw2spidy/conf.erb"),
    ensure => file
  }

  file { "/vagrant/config/cnf/myenv.json":
    content => template("gw2spidy/example-custom-cnf.json"),
    ensure => file
  }

  exec { "pear-channel-discovery":
    command => "sudo pear channel-discover pear.phing.info",
    require => Package["php-pear"],
    onlyif => "test `pear list-channels | grep 'pear.phing.info' | wc -l` -eq 0"
  }

  exec { "fetch-php-extensions":
    command => "sudo pear install phing/phing Log",
    require => Exec["pear-channel-discovery"]
  }

#  exec { "purge-caches":
#    command => "php tools/purge-cache.php; php tools/setup-request-slots.php; php daemons/worker-types.php; php tools/purge-cache.php",
#    cwd => "/vagrant",
#    require => Package["php5-cli"]
#  }

#  exec { "exercise-daemons":
#    command => "php daemons/fill-queue-item-db.php; php daemons/fill-queue-item-listing-db.pp",
#    cwd => "/vagrant",
#    require => Exec["purge-caches"]
#  }

  service { 'nginx':
      ensure => running,
      require => Package['nginx']
  }

  service { 'php5-fpm':
      ensure => running,
      require => Package['php5-fpm']
  }

  service { 'varnish':
    ensure => running,
    require => Package['varnish']
  }

  service { 'redis-server':
    ensure => running,
    require => Package['redis-server']
  }

  service { 'memcached':
    ensure => running,
    require => Package['memcached']
  }

  service { 'mysql':
    ensure => running,
    require => Package['mysql-server']
  }
}
