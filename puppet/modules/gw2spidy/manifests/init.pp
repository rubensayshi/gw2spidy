class gw2spidy {

  exec { "apt-get update 1":
    command => "sudo apt-get update",
  }

  package { "python-software-properties":
    ensure => "installed",
    require => Exec["apt-get update 1"]
  }

  exec { "add current node.js repo":
    command => "sudo add-apt-repository -y ppa:chris-lea/node.js",
    require => Package["python-software-properties"],
  }

  exec { "apt-get update 2":
    command => "sudo apt-get update",
    require => Exec["add current node.js repo"]
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
    "mysql-server",
    "nodejs"
  ]

  package {
    $packages: ensure => "installed",
    require => Exec["apt-get update 2"]
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

  exec { "npm-install":
    command => "sudo npm install -g grunt-cli",
    cwd => "/vagrant",
    require => Package["nodejs"]
  }

  exec { "do-grunt":
    command => "sudo bin/update.sh",
    cwd => "/vagrant",
    require => [Exec["npm-install"], Package["varnish"] ]
  }

  service { 'nginx':
      ensure => running,
      require => Package['nginx']
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

  service { 'php5-fpm':
      ensure => running,
      require => Package['php5-fpm']
  }

}
