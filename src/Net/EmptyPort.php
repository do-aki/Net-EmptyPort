<?php
namespace dooaki\Net;

/**
 * 空きポートを探す
 * オリジナルは Perl の Test::TCP に含まれる Net::EmptyPort
 *
 * @author do_aki <do.hiroaki@gmail.com>
 */
class EmptyPort
{

    /**
     * Dynamic and/or Private Ports (49152-65535) の中で、空いているポートを探す
     * 有効なポート番号が指定されている場合は 指定ポート番号から 65535 までを探す
     *
     * @param integer $port port
     * @param string $proto tcp or udp
     * @throws \UnexpectedValueException
     * @return integer 空いているポート番号
     */
    public static function find($port=null, $proto='tcp') 
    {
        if (null === $port || false === $port) {
            $port = 50000 + rand(0, 1000);
        } else {
            $port = intval($port);
            if ($port < 0 || 49152 < $port) {
                $port = 49152;
            }
        }
        $proto = strtolower($proto);

        do {
            if (self::isPortUsed($port, $proto)) {
                continue;
            }

            if ($proto === 'tcp') {
                $sock = self::_createSocket($proto);
                if (@socket_bind($sock, '127.0.0.1', $port) && @socket_listen($sock, 5)) {
                    socket_close($sock);
                    return $port;
                }
                socket_close($sock);
            } else {
                return $port;
            }

        } while (++$port <= 65535);

        throw new \UnexpectedValueException('empty port not found');
    }

    /**
     * 指定したポートが使われているかどうかを返す
     *
     * @param integer $port port
     * @param string $proto tcp or udp
     * @return boolean 使われている場合は true, そうでない場合は false
     */
    public static function isPortUsed($port, $proto='tcp')
    {
        $port = intval($port);
        $proto = strtolower($proto);

        $sock = self::_createSocket($proto);
        if ($proto === 'tcp') {
            $ret = @socket_connect($sock, '127.0.0.1', $port);
        } else {
            $ret = !socket_bind($sock, '127.0.0.1', $port);
        }
        socket_close($sock);

        return $ret;
    }

    /**
     * 指定ポートが接続可能な状態になるまで待つ
     *
     * @param integer $port port
     * @param float $max_wait_sec 最大待ち秒数
     * @param string $proto tcp or udp
     * @return boolean 時間内に接続可能になった場合は true, そうでない場合は false
     */
    public static function wait($port, $max_wait_sec, $proto='tcp')
    {
        $port = intval($port);
        $proto = strtolower($proto);

        $waiter = self::_makeWaiter($max_wait_sec * 1000000);

        while ($waiter()) {
            if (self::isPortUsed($port, $proto)) {
                return true;
            }
        }
        return false;
    }

    /**
     * TCP あるいは UDP のソケットを返す
     *
     * @param string $proto tcp or udp
     * @throws \UnexpectedValueException ソケットの生成に失敗した場合
     * @return resource socket
     */
    private static function _createSocket($proto)
    {
        $sock = ($proto === 'tcp') ?
            socket_create(AF_INET, SOCK_STREAM, SOL_TCP):
            socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)
        ;
        if (!$sock) {
            throw new \UnexpectedValueException("socket_create() failed: " . socket_strerror(socket_last_error()));
        }
        return $sock;
    }

    /**
     * 待ちクロージャを返す
     *
     * @param integer $max_wait 最大待ち時間(マイクロ秒)
     * @return closure
     */
    private static function _makeWaiter($max_wait)
    {
        $waited = 0;
        $sleep  = 1000;

        return function () use ($max_wait, &$waited, &$sleep) {
            if ($max_wait < $waited) {
                return false;
            }

            usleep($sleep);
            $waited += $sleep;
            $sleep *= 2;

            return true;
        };
    }
}

