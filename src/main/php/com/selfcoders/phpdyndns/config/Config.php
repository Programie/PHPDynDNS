<?php
namespace com\selfcoders\phpdyndns\config;

class Config
{
    /**
     * @var string
     * @required
     */
    public $server;
    /**
     * @var int
     */
    public $ttl = 60;
    /**
     * @var User[]
     * @required
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