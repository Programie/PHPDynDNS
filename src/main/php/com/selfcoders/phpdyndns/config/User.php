<?php
namespace com\selfcoders\phpdyndns\config;

class User
{
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $passwordHash;
    /**
     * @var Host[]
     */
    public $hosts = [];
    /**
     * @var string|null
     */
    public $postProcess;

    /**
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password)
    {
        return password_verify($password, $this->passwordHash);
    }

    /**
     * @param string $hostname
     * @return Host|null
     */
    public function getHost(string $hostname)
    {
        return $this->hosts[$hostname] ?? null;
    }
}