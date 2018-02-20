<?php
namespace com\selfcoders\phpdyndns;

class NSUpdate
{
    /**
     * @var string
     */
    private $server;
    /**
     * @var string
     */
    private $zone;
    /**
     * @var string[]
     */
    private $commands = [];

    /**
     * @param string $server
     * @param string $zone
     */
    public function __construct(string $server, string $zone)
    {
        $this->server = $server;
        $this->zone = $zone;
    }

    /**
     * @param string $entry
     * @param string $type
     */
    public function delete(string $entry, string $type)
    {
        $this->commands[] = sprintf("update delete %s %s", $entry, $type);
    }

    /**
     * @param string $entry
     * @param int $ttl
     * @param string $type
     * @param string $value
     */
    public function add(string $entry, int $ttl, string $type, string $value)
    {
        $this->commands[] = sprintf("update add %s %d %s %s", $entry, $ttl, $type, $value);
    }

    /**
     * @return bool
     */
    public function send()
    {
        $allCommands = [];

        $allCommands[] = sprintf("server %s", $this->server);
        $allCommands[] = sprintf("zone %s", $this->zone);

        $allCommands = array_merge($allCommands, $this->commands);

        $allCommands[] = "send";

        exec(sprintf("echo \"%s\" | nsupdate", implode("\n", $allCommands)), $output, $exitCode);

        $this->commands = [];

        return $exitCode === 0;
    }
}