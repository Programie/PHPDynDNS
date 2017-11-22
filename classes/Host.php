<?php

class Host
{
    /**
     * @var string
     */
    public $hostname;
    /**
     * @var string
     */
    public $zone;

    /**
     * @param string $hostname
     * @param array $source
     * @return Host
     */
    public static function fromArray(string $hostname, array $source)
    {
        $host = new self;

        $host->hostname = $hostname;

        if (!isset($source["zone"])) {
            throw new ConfigException("Property 'users.{username}.hosts.{hostname}.zone' is required");
        }

        $host->zone = $source["zone"];

        return $host;
    }
}