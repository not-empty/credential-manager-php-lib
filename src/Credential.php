<?php

namespace CredentialManager;

use Exception;
use Predis\Client as Redis;

class Credential
{
    public $redis;
    private $redisConfig;

    /**
     * construct class with redis config if pass
     * @param array $redisConfig
     * @param array $redisOptions
     * @return void
     */
    public function __construct(
        array $redisConfig = [],
        array $redisOptions = []
    ) {
        $this->redisConfig = $redisConfig;
        $this->redisOptions = $redisOptions;
    }

    /**
     * search and return if found a service credential in redis
     * @param string $origin
     * @param string $service
     * @return string
     */
    public function getCredential(
        string $origin,
        string $service
    ): ?string {
        try {
            $redis = $this->validateConnection();
            return $redis->get("token-{$origin}-{$service}");
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * search and return if found a service credential in redis
     * @param string $origin
     * @param string $service
     * @param string $credential
     * @return bool
     */
    public function setCredential(
        string $origin,
        string $service,
        string $credential
    ): bool {
        try {
            $redis = $this->validateConnection();
            $redis->set("token-{$origin}-{$service}", $credential);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * remove credential from redis
     * @param string $origin
     * @param string $service
     * @return bool
     */
    public function delCredential(
        string $origin,
        string $service
    ): bool {
        try {
            $redis = $this->validateConnection();
            $redis->del("token-{$origin}-{$service}");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * verify if already exists connection
     * @return Redis
     */
    public function validateConnection(): Redis
    {
        if ($this->redis) {
            return $this->redis;
        }

        return $this->connectRedis();
    }

    /**
     * @codeCoverageIgnore
     * return predis client object
     * @return Redis
     */
    public function connectRedis(): Redis
    {
        $defaultConfig = [
            'scheme' => 'tcp',
            'host' => 'localhost',
            'port' => 6379,
        ];

        $this->redisConfig = array_merge($defaultConfig, $this->redisConfig);
        $this->redis = new Redis($this->redisConfig, $this->redisOptions);

        return $this->redis;
    }
}
