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
     * @var string
     */
    public $nsupdateOptions = "";
    /**
     * @var string[]
     */
    public $trustedProxies = [];

    /**
     * @param string $username
     * @return null|User
     */
    public function getUser(string $username)
    {
        return $this->users[$username] ?? null;
    }

    /**
     * @param User[] $users
     */
    public function setUsers(array $users)
    {
        foreach ($users as $username => $user) {
            if ($user->username === null) {
                $user->username = $username;
            }

            $this->users[$user->username] = $user;
        }
    }

    /**
     * @param string $options
     */
    public function setNsupdateOptions(string $options)
    {
        $this->nsupdateOptions = $options;
    }

    /**
     * @param string[] $ips
     */
    public function setTrustedProxies(array $ips)
    {
        $this->trustedProxies = $ips;
    }
}