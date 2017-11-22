<?php

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
     * @param string $username
     * @param array $source
     * @return User
     */
    public static function fromArray(string $username, array $source)
    {
        $user = new self;

        $user->username = $username;

        if (isset($source["password_hash"])) {
            $user->passwordHash = $source["password_hash"];
        } elseif (isset($source["password"])) {
            $user->passwordHash = password_hash($source["password"], PASSWORD_DEFAULT);
        } else {
            throw new ConfigException("Property 'users.{username}.password_hash' is is required");
        }

        if (!isset($source["hosts"]) or !is_array($source["hosts"])) {
            throw new ConfigException("Property 'users.{username}.hosts' is required and must be a map");
        }

        foreach ($source["hosts"] as $hostname => $hostSource) {
            $host = Host::fromArray($hostname, $hostSource);

            $user->hosts[$host->hostname] = $host;
        }

        $user->postProcess = $source["post_process"] ?? $source["postprocess"] ?? null;

        return $user;
    }

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