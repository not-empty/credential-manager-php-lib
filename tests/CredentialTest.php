<?php

namespace CredentialManager;

use Exception;
use Mockery;
use PHPUnit\Framework\TestCase;
use Predis\Client as Redis;

class CredentialTest extends TestCase
{
    /**
     * @covers \CredentialManager\Credential::__construct
     */
    public function testCreateCredential()
    {
        $credential = new Credential();
        $this->assertInstanceOf(Credential::class, $credential);
    }

    /**
     * @covers \CredentialManager\Credential::getCredential
     */
    public function testGetCredential()
    {
        $origin = 'originTest';
        $service = 'serviceTest';

        $redisMock = Mockery::mock(Redis::class)
            ->shouldReceive('get')
            ->with("token-{$origin}-{$service}")
            ->once()
            ->andReturn('token')
            ->getMock();

        $credential = Mockery::mock(Credential::class)->makePartial();
        $credential->shouldReceive('validateConnection')
            ->withNoArgs()
            ->once()
            ->andReturn($redisMock);

        $getCredential = $credential->getCredential($origin, $service);

        $this->assertEquals($getCredential, 'token');
    }

    /**
     * @covers \CredentialManager\Credential::getCredential
     */
    public function testGetCredentialException()
    {
        $origin = 'originTest';
        $service = 'serviceTest';

        $redisMock = Mockery::mock(Redis::class)
            ->shouldReceive('get')
            ->with("token-{$origin}-{$service}")
            ->once()
            ->andThrow(new Exception('err', 500))
            ->getMock();

        $credential = Mockery::mock(Credential::class)->makePartial();
        $credential->shouldReceive('validateConnection')
            ->withNoArgs()
            ->once()
            ->andReturn($redisMock);

        $getCredential = $credential->getCredential($origin, $service);

        $this->assertEquals($getCredential, null);
    }

    /**
     * @covers \CredentialManager\Credential::setCredential
     */
    public function testSetCredential()
    {
        $origin = 'originTest';
        $service = 'serviceTest';
        $value = 'token';

        $redisMock = Mockery::mock(Redis::class)
            ->shouldReceive('set')
            ->with("token-{$origin}-{$service}", $value)
            ->once()
            ->andReturn(true)
            ->getMock();

        $credential = Mockery::mock(Credential::class)->makePartial();
        $credential->shouldReceive('validateConnection')
            ->withNoArgs()
            ->once()
            ->andReturn($redisMock);

        $setCredential = $credential->setCredential($origin, $service, $value);

        $this->assertEquals($setCredential, true);
    }

    /**
     * @covers \CredentialManager\Credential::setCredential
     */
    public function testSetCredentialException()
    {
        $origin = 'originTest';
        $service = 'serviceTest';
        $value = 'token';

        $redisMock = Mockery::mock(Redis::class)
            ->shouldReceive('set')
            ->with("token-{$origin}-{$service}", $value)
            ->once()
            ->andThrow(new Exception('err', 500))
            ->getMock();

        $credential = Mockery::mock(Credential::class)->makePartial();
        $credential->shouldReceive('validateConnection')
            ->withNoArgs()
            ->once()
            ->andReturn($redisMock);

        $setCredential = $credential->setCredential($origin, $service, $value);

        $this->assertEquals($setCredential, false);
    }

    /**
     * @covers \CredentialManager\Credential::delCredential
     */
    public function testDelCredential()
    {
        $origin = 'originTest';
        $service = 'serviceTest';

        $redisMock = Mockery::mock(Redis::class)
            ->shouldReceive('del')
            ->with("token-{$origin}-{$service}")
            ->once()
            ->andReturn(true)
            ->getMock();

        $credential = Mockery::mock(Credential::class)->makePartial();
        $credential->shouldReceive('validateConnection')
            ->withNoArgs()
            ->once()
            ->andReturn($redisMock);

        $delCredential = $credential->delCredential($origin, $service);

        $this->assertEquals($delCredential, true);
    }

    /**
     * @covers \CredentialManager\Credential::delCredential
     */
    public function testDelCredentialException()
    {
        $origin = 'originTest';
        $service = 'serviceTest';

        $redisMock = Mockery::mock(Redis::class)
            ->shouldReceive('del')
            ->with("token-{$origin}-{$service}")
            ->once()
            ->andThrow(new Exception('err', 500))
            ->getMock();

        $credential = Mockery::mock(Credential::class)->makePartial();
        $credential->shouldReceive('validateConnection')
            ->withNoArgs()
            ->once()
            ->andReturn($redisMock);

        $delCredential = $credential->delCredential($origin, $service);

        $this->assertEquals($delCredential, false);
    }

    /**
     * @covers \CredentialManager\Credential::validateConnection
     */
    public function testValidateConnection()
    {
        $redisSpy = Mockery::spy(Redis::class);

        $credential = Mockery::mock(Credential::class)->makePartial();
        $credential->shouldReceive('connectRedis')
            ->withNoArgs()
            ->once()
            ->andReturn($redisSpy);

        $validateConnection = $credential->validateConnection();

        $this->assertInstanceOf(Redis::class, $validateConnection);
    }

    /**
     * @covers \CredentialManager\Credential::validateConnection
     */
    public function testValidateConnectionAndPassConnection()
    {
        $redisSpy = Mockery::spy(Redis::class);

        $credential = Mockery::mock(Credential::class)->makePartial();
        $credential->shouldReceive('connectRedis')
            ->withNoArgs()
            ->never()
            ->andReturn($redisSpy);

        $credential->redis = $redisSpy;

        $validateConnection = $credential->validateConnection();

        $this->assertInstanceOf(Redis::class, $validateConnection);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
