class nginx {
  file { 'vagrant-nginx':
      path => '/etc/nginx/sites-available/gwspidy',
      ensure => file,
      require => Package['nginx'],
      source => 'puppet:///modules/nginx/gwspidy.txt',
  }

  file { 'default-nginx-disable':
      path => '/etc/nginx/sites-enabled/default',
      ensure => absent,
      require => Package['nginx'],
  }

  file { 'vagrant-nginx-enable':
      path => '/etc/nginx/sites-enabled/vagrant',
      target => '/etc/nginx/sites-available/gwspidy',
      ensure => link,
      notify => Service['nginx'],
      require => [
          File['vagrant-nginx'],
          File['default-nginx-disable'],
      ],
  }
}
