<?php

namespace Snowdog\DevTest\Model;

/**
 * Class Varnish
 * @package Snowdog\DevTest\Model
 */
class Varnish
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var int
     */
    private $user_id;

    /**
     * Varnish constructor.
     */
    public function __construct()
    {
        $this->user_id = intval($this->user_id);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIP(): string
    {
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }
}