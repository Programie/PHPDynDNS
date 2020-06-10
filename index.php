<?php
use com\selfcoders\phpdyndns\config\Config;
use com\selfcoders\phpdyndns\ErrorCode;
use com\selfcoders\phpdyndns\IPUtils;
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

// Allow Icinga/Nagios to check this application
if (preg_match("/^check_http/", $_SERVER["HTTP_USER_AGENT"])) {
    exit;
}

// Read user provided data
$username = $_GET["username"] ?? $_SERVER["PHP_AUTH_USER"] ?? null;
$password = $_GET["password"] ?? $_SERVER["PHP_AUTH_PW"] ?? null;
$hostname = $_GET["hostname"] ?? null;
$ipV4Address = $_GET["myip"] ?? null;
$ipV6Address = $_GET["myipv6"] ?? null;

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

$clientIP = IPUtils::getClientIP($config->trustedProxies);

if (!$ipV4Address and filter_var($clientIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    // Use client IP address if no IPv4 given
    $ipV4Address = $clientIP;
} elseif (!$ipV6Address and filter_var($clientIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
    // Use client IP address if no IPv6 given
    $ipV6Address = $clientIP;
}

if (!$ipV4Address and !$ipV6Address) {
    header("HTTP/1.1 400 Bad Request");
    echo ErrorCode::INVALID_IP;
    exit;
}

// Check whether the given IPv4 address is valid
if ($ipV4Address and !filter_var($ipV4Address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    header("HTTP/1.1 400 Bad Request");
    echo ErrorCode::INVALID_IP;
    exit;
}

// Check whether the given IPv6 address is valid
if ($ipV6Address and !filter_var($ipV6Address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
    header("HTTP/1.1 400 Bad Request");
    echo ErrorCode::INVALID_IP;
    exit;
}

$nsUpdate = new NSUpdate($config->server, $host->zone, $config->nsupdateOptions);

if ($ipV4Address) {
    $nsUpdate->delete($host->hostname, "A");
    $nsUpdate->add($host->hostname, $config->ttl, "A", $ipV4Address);
}

if ($ipV6Address) {
    $nsUpdate->delete($host->hostname, "AAAA");
    $nsUpdate->add($host->hostname, $config->ttl, "AAAA", $ipV6Address);
}

if (!$nsUpdate->send()) {
    echo ErrorCode::DNSERROR;
    exit;
}

// Run post process command (if configured)
if (isset($user->postProcess)) {
    $commandLine = $user->postProcess;
    $commandLine = str_replace("%username%", $user->username, $commandLine);
    $commandLine = str_replace("%hostname%", $host->hostname, $commandLine);
    $commandLine = str_replace("%ipv4address%", $ipV4Address, $commandLine);
    $commandLine = str_replace("%ipv6address%", $ipV6Address, $commandLine);

    exec($commandLine);
}

$response = [];

if ($ipV4Address) {
    $response[] = $ipV4Address;
}

if ($ipV6Address) {
    $response[] = $ipV6Address;
}

echo ErrorCode::OK . " " . implode(" ", $response);