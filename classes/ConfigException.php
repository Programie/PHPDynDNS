<?php

class ConfigException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct(sprintf("Invalid config options: %s", $message));
    }
}