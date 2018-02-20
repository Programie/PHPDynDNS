<?php
namespace com\selfcoders\phpdyndns\config;

class Config
{
    /**
     * @var string
     */
    public $server;
    /**
     * @var int
     */
    public $ttl;
    /**
     * @var User[]
     */
    public $users = [];

    /**
     * @param string $username
     * @return null|User
     */
    public function getUser(string $username)
    {
        return $this->users[$username] ?? null;
    }
}