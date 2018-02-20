<?php
use com\selfcoders\phpdyndns\config\Config;
use com\selfcoders\phpdyndns\ErrorCode;
use com\selfcoders\phpdyndns\NSUpdate;

require_once __DIR__ . "/vendor/autoload.php";

// Check whether the configuration file exists
$configFile = __DIR__ . "/config.json";
if (!file_exists($configFile)) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Application not configured!";
    exit;
}

try {
    $mapper = new JsonMapper;
    $mapper->bExceptionOnMissingData = true;
    $mapper->bStrictObjectTypeChecking = true;
    /**
     * @var $config Config
     */
    $config = $mapper->map(json_decode(file_get_contents($configFile)), new Config);
} catch (JsonMapper_Exception $exception) {
    error_log($exception);

    header("HTTP/1.1 500 Internal Server Error");
    echo "An error occurred while parsing configuration (see error log)!";
    exit;
}

var_dump($config);

// Allow Icinga/Nagios to check this application
if (preg_match("/^check_http/", $_SERVER["HTTP_USER_AGENT"])) {
    exit;
}

// Read user provided data
$username = $_GET["username"] ?? $_SERVER["PHP_AUTH_USER"] ?? null;
$password = $_GET["password"] ?? $_SERVER["PHP_AUTH_PW"] ?? null;
$hostname = $_GET["hostname"] ?? null;
$ipAddress = $_GET["ipaddress"] ?? $_SERVER["REMOTE_ADDR"];

if ($username === null or $password === null) {
    header("WWW-Authenticate: Basic realm=\"DynDNS Update\"");
    header("HTTP/1.0 401 Unauthorized");
    echo ErrorCode::BADAUTH;
    exit;
}

if ($hostname === null) {
    header("HTTP/1.1 400 Bad Request");
    echo ErrorCode::INVALID_HOST;
    exit;
}

$user = $config->getUser($username);

if ($user === null or !$user->checkPassword($password)) {
    header("WWW-Authenticate: Basic realm=\"DynDNS Update\"");
    header("HTTP/1.0 401 Unauthorized");
    echo ErrorCode::BADAUTH;
    exit;
}

$host = $user->getHost($hostname);

if ($host === null) {
    header("HTTP/1.1 400 Bad Request");
    echo ErrorCode::INVALID_HOST;
    exit;
}

// Check whether the given IP address is valid
if (!$ipAddress or !filter_var($ipAddress, FILTER_VALIDATE_IP)) {
    header("HTTP/1.1 400 Bad Request");
    echo ErrorCode::INVALID_IP;
    exit;
}

// Is IPv4 address?
if (preg_match("/^\d{1,3}(\.\d{1,3}){3,3}$/", $ipAddress)) {
    $entryType = "A";
} else {
    $entryType = "AAAA";
}

$nsUpdate = new NSUpdate($config->server, $host->zone);

$nsUpdate->delete($host->hostname, $entryType);
$nsUpdate->add($host->hostname, $config->ttl, $entryType, $ipAddress);

if (!$nsUpdate->send()) {
    echo ErrorCode::DNSERROR;
    exit;
}

// Run post process command (if configured)
if (isset($user->postProcess)) {
    $commandLine = $user->postProcess;
    $commandLine = str_replace("%username%", $user->username, $commandLine);
    $commandLine = str_replace("%hostname%", $host->hostname, $commandLine);
    $commandLine = str_replace("%ipaddress%", $ipAddress, $commandLine);
    $commandLine = str_replace("%entrytype%", $entryType, $commandLine);

    exec($commandLine);
}

echo ErrorCode::OK . " " . $ipAddress;