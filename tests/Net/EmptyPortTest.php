<?php
namespace dooaki\Test\Net;

use dooaki\Net\EmptyPort;

class EmptyPortTest extends \PHPUnit_Framework_TestCase
{

    public function test_find_tcp() 
    {
        $this->assertNotEmpty(EmptyPort::find());
    }

    public function test_find_udp() 
    {
        $this->assertNotEmpty(EmptyPort::find(null, 'udp'));
    }

    public function test_wait_tcp() 
    {
        $port = EmptyPort::find();

        $this->assertFalse(EmptyPort::wait($port, 0.1), 'closed');

        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_bind($sock, '127.0.0.1', $port);
        socket_listen($sock, 5);

        $this->assertTrue(EmptyPort::wait($port, 2), 'open');

        socket_close($sock);
    }
}

