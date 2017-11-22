<?php
require_once __DIR__ . "/classes/Config.php";
require_once __DIR__ . "/classes/ConfigException.php";
require_once __DIR__ . "/classes/Host.php";
require_once __DIR__ . "/classes/NSUpdate.php";
require_once __DIR__ . "/classes/User.php";

// Standard error codes (As used by dyn.com)
define("ERROR_OK", "good");// The update was successful, and the hostname is now updated
define("ERROR_BADAUTH", "badauth");// The username and password pair do not match a registered user
define("ERROR_NO_CHANGE", "nochg");// The update changed no settings
define("ERROR_INVALID_HOST", "nohost");// The hostname specified does not exist in this user account
define("ERROR_DNSERROR", "dnserr");// DNS error encountered

// Custom error codes
define("ERROR_INVALID_IP", "iperror");// IP address is invalid

// Check whether the configuration file exists
$configFile = __DIR__ . "/config.json";
if (!file_exists($configFile)) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Application not configured!";
    exit;
}

try {
    $config = Config::fromFile($configFile);
} catch (ConfigException $exception) {
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
$ipAddress = $_GET["ipaddress"] ?? $_SERVER["REMOTE_ADDR"];

if ($username === null or $password === null) {
    header("WWW-Authenticate: Basic realm=\"DynDNS Update\"");
    header("HTTP/1.0 401 Unauthorized");
    echo ERROR_BADAUTH;
    exit;
}

if ($hostname === null) {
    header("HTTP/1.1 400 Bad Request");
    echo ERROR_INVALID_HOST;
    exit;
}

$user = $config->getUser($username);

if ($user === null or !$user->checkPassword($password)) {
    header("WWW-Authenticate: Basic realm=\"DynDNS Update\"");
    header("HTTP/1.0 401 Unauthorized");
    echo ERROR_BADAUTH;
    exit;
}

$host = $user->getHost($hostname);

if ($host === null) {
    header("HTTP/1.1 400 Bad Request");
    echo ERROR_INVALID_HOST;
    exit;
}

// Check whether the given IP address is valid
if (!$ipAddress or !filter_var($ipAddress, FILTER_VALIDATE_IP)) {
    header("HTTP/1.1 400 Bad Request");
    echo ERROR_INVALID_IP;
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
    echo ERROR_DNSERROR;
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

echo ERROR_OK . " " . $ipAddress;