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
    private $passwordHash;
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

    /**
     * @param string $hash
     * @required
     */
    public function setPasswordHash(string $hash)
    {
        $this->passwordHash = $hash;
    }

    /**
     * @param Host[] $hosts
     */
    public function setHosts(array $hosts)
    {
        foreach ($hosts as $hostname => $host) {
            if ($host->hostname === null) {
                $host->hostname = $hostname;
            }

            $this->hosts[] = $host;
        }
    }
}