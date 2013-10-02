Net-EmptyPort
=============

[![Build Status](https://travis-ci.org/do-aki/Net-EmptyPort.png?branch=master)](https://travis-ci.org/do-aki/Net-EmptyPort)
[![Coverage Status](https://coveralls.io/repos/do-aki/Net-EmptyPort/badge.png?branch=master)](https://coveralls.io/r/do-aki/Net-EmptyPort?branch=master)

Net-EmptyPort is php library finding an empty TCP/UDP port.

Original is cpan module [Net::EmptyPort](https://metacpan.org/module/Net::EmptyPort)

Requirements
-------------
* PHP 5.3 or later
* Sockets Support enabled (--enable-sockets)

Installation
-------------

you can install the script with [Composer](http://getcomposer.org/).

in your `composer.json` file:
```
{
    "require": {
        "dooaki/net-empty_port": "dev-master"
    }
}
```

```
composer.phar install
```

Methods
-------------

### find([$port, $proto])
 find free TCP port
```
  use dooaki\Net\EmptyPort;

  $port = EmptyPort::find(5963); // 5963..65535
```

 find free UDP port
```
  $port = EmptyPort::find(null, 'udp'); // 1024..65535
```

### isPortUsed($port, [$proto])
checks the given port is already used. 
also works for UDP
```
  $dns_udp_used = EmptyPort::isPortUsed(53, 'udp');
```

### wait($port, $max_wait_sec[, $protol])
wait for a particular port is available for connect.

Author
-------------
do_aki <do.hiroaki at gmail.com>

License
-------------
MIT License

