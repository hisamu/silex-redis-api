<?php 

namespace Acme\Cache;

interface CacheInterface
{
    public function get($key);
    public function add($key, $value);
    public function expire($key);
    public function expirePattern($key, $pattern);
}