<?php

namespace AppBundle\Service;

use Predis;

/**
* Here you have to implement a CacheService with the operations above.
* It should contain a failover, which means that if you cannot retrieve
* data you have to hit the Database.
**/
class CacheService
{

    protected $redis;

    public function __construct($host, $port, $prefix)
    {
       $this->redis = $predisClient = new Predis\Client(array(
            "scheme" => "tcp",
            "host" => $host,
            "port" => $port));

    }

    public function get($key)
    {
        if ( $this->redis->exists($key) && $this->redis->get($key) ) {
            return json_decode($this->redis->get($key));
        }else {
            return false;
        }
    }

    public function set($key, $value)
    {
        $this->redis->set($key, json_encode($value));
    }

    public function del($key)
    {
        $this->redis->del($key);
    }


}
