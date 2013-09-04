<?php 

namespace Acme\Cache;

class Redis implements CacheInterface
{
    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function get($key)
    {
        return $this->client->get($key);
    }

    public function add($key, $value)
    {
        $add = $this->client->set($key, $value);

        // automatic expires the key in 2 hours
        $this->client->expire($key, 7200);
        
        return $add;
    }

    public function expire($key)
    {
        return $this->client->del($key);
    }

    public function expirePattern($key, $pattern)
    {
        $keys = $this->client->keys($key.'*');

        foreach ($keys as $key) {
            if (preg_match(sprintf('@%s@', $pattern), $key)) {
                $this->expire($key);
            }
        }
    }
}