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
     * @var string
     */
    private $options;
    /**
     * @var string[]
     */
    private $commands = [];

    /**
     * @param string $server
     * @param string $zone
     * @param string $options
     */
    public function __construct(string $server, string $zone, string $options)
    {
        $this->server = $server;
        $this->zone = $zone;
        $this->options = $options;
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

    public function getAllCommands()
    {
        $allCommands = [];

        $allCommands[] = sprintf("server %s", $this->server);
        $allCommands[] = sprintf("zone %s", $this->zone);

        $allCommands = array_merge($allCommands, $this->commands);

        $allCommands[] = "send";

        return $allCommands;
    }

    /**
     * @return bool
     */
    public function send()
    {
        $nsUpdateCommand = "nsupdate";

        if ($this->options !== "") {
            $nsUpdateCommand .= " " . $this->options;
        }

        exec(sprintf("echo \"%s\" | %s", implode("\n", $this->getAllCommands()), escapeshellcmd($nsUpdateCommand)), $output, $exitCode);

        $this->commands = [];

        return $exitCode === 0;
    }
}