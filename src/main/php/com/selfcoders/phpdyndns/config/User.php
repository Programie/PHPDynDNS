<?php
namespace com\selfcoders\phpdyndns\config;

class User
{
    /**
     * @var string
     * @required
     */
    public $username;
    /**
     * @var string
     * @required
     */
    public $passwordHash;
    /**
     * @var Host[]
     * @required
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