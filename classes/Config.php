<?php

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
     * @param string $file
     * @return Config
     */
    public static function fromFile(string $file)
    {
        $json = json_decode(file_get_contents($file), true);

        if ($json === null) {
            new RuntimeException(sprintf("Unable to read configuration from file '%s'", $file));
        }

        return self::fromArray($json);
    }

    /**
     * @param array $source
     * @return Config
     */
    public static function fromArray(array $source)
    {
        $config = new self;

        $config->server = $source["server"] ?? "localhost";
        $config->ttl = (int)($source["ttl"] ?? 60);

        if (!isset($source["users"]) or !is_array($source["users"])) {
            throw new ConfigException("Property 'users' is required and must be a map");
        }

        foreach ($source["users"] as $username => $userSource) {
            $user = User::fromArray($username, $userSource);

            $config->users[$user->username] = $user;
        }

        return $config;
    }

    /**
     * @param string $username
     * @return null|User
     */
    public function getUser(string $username)
    {
        return $this->users[$username] ?? null;
    }
}