<?php 

use Mockery as m;
use Acme\Manager\Manager;
use Acme\Cache\Redis;
use Silex\WebTestCase;

class RedisTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../../../src/app.php';
        $app['session.test'] = true;
 
        return $app;
    }

    public function tearDown()
    {
        m::close();
    }

    public function testAddGetAndExpire()
    {
        $key = '/foo/bar';
        $value = 'testing';
        $expire = 7200;

        $r = new Redis($this->getAddExpireMock($key, $value, $expire));

        $this->assertTrue($r->add($key, $value));
        $this->assertEquals($r->get($key), $value);

        $r->expire($key);
        $this->assertNull($r->get($key));
    }

    public function testExpirePattern()
    {
        $key = '/foo/bar';
        $pattern = 'foo-123';

        $values = array(
            $key . '/' . $pattern,
            $key . '/xyz/' . $pattern,
            $key . '/' . $pattern . '/xyz',
            $key . '/abc/' . $pattern . '/xyz',
        );

        $r = new Redis($this->getPatternMock($key, $pattern, $values));

        $r->expirePattern($key, $pattern);

        $this->assertNull($r->get($key.'/'.$pattern));
        $this->assertNull($r->get($key.'/xyz/'.$pattern));
        $this->assertNull($r->get($key.'/'.$pattern.'/xyz'));
        $this->assertNull($r->get($key.'/abc/'.$pattern.'/xyz'));
    }

    protected function getAddExpireMock($key, $value, $expire)
    {
        $redisMock = m::mock('Predis\\Client');
        $redisMock->shouldReceive('set')
            ->with($key, $value)
            ->once()
            ->ordered('reset')
            ->andReturn(true);
        $redisMock->shouldReceive('expire')
            ->with($key, $expire)
            ->once()
            ->ordered('reset');
        $redisMock->shouldReceive('get')
            ->with($key)
            ->once()
            ->ordered('reset')
            ->andReturn($value);
        $redisMock->shouldReceive('del')
            ->with($key)
            ->once()
            ->ordered('reset');
        $redisMock->shouldReceive('get')
            ->with($key)
            ->once()
            ->ordered('reset')
            ->andReturn(null);

        return $redisMock;
    }

    protected function getPatternMock($key, $pattern, $values)
    {
        $redisMock = m::mock('Predis\\Client');
        $redisMock->shouldReceive('keys')
            ->with($key.'*')
            ->once()
            ->ordered('reset')
            ->andReturn($values);
        $redisMock->shouldReceive('del')
            ->with($values[0])
            ->once()
            ->ordered('reset');
        $redisMock->shouldReceive('del')
            ->with($values[1])
            ->once()
            ->ordered('reset');
        $redisMock->shouldReceive('del')
            ->with($values[2])
            ->once()
            ->ordered('reset');
        $redisMock->shouldReceive('del')
            ->with($values[3])
            ->once()
            ->ordered('reset');
        $redisMock->shouldReceive('get')
            ->with($values[0])
            ->once()
            ->ordered('reset')
            ->andReturn(null);
        $redisMock->shouldReceive('get')
            ->with($values[1])
            ->once()
            ->ordered('reset')
            ->andReturn(null);
        $redisMock->shouldReceive('get')
            ->with($values[2])
            ->once()
            ->ordered('reset')
            ->andReturn(null);
        $redisMock->shouldReceive('get')
            ->with($values[3])
            ->once()
            ->ordered('reset')
            ->andReturn(null);

        return $redisMock;
    }
}