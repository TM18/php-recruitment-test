<?php

namespace Snowdog\DevTest\Component;

use Old_Legacy_CacheWarmer_Resolver_Interface;

/**
 * Class CacheWarmerResolver
 * @package Snowdog\DevTest\Component
 */
class CacheWarmerResolver implements Old_Legacy_CacheWarmer_Resolver_Interface
{
    /**
     * @var string
     */
    protected $ip;

    /**
     * CacheWarmerResolver constructor.
     * @param string $ip
     */
    public function __construct(string $ip)
    {
        $this->ip = $ip;
    }

    /**
     * @param $hostname
     * @return string
     */
    public function getIp($hostname): string
    {
        return $this->ip;
    }
}