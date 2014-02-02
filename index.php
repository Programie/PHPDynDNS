<?php
// Standard error codes (As used by dyn.com)
define("ERROR_OK", "good");// The update was successful, and the hostname is now updated
define("ERROR_BADAUTH", "badauth");// The username and password pair do not match a registered user
define("ERROR_NO_CHANGE", "nochg");// The update changed no settings
define("ERROR_INVALID_HOST", "nohost");// The hostname specified does not exist in this user account
define("ERROR_DNSERROR", "dnserr");// DNS error encountered

// Custom error codes
define("ERROR_INVALID_IP", "iperror");// IP address is invalid

// Check whether the configuration file exists
$configFile = __DIR__ . "/data/config.json";
if (!file_exists($configFile))
{
	header("HTTP/1.1 500 Internal Server Error");
	echo "System not configured!";
	exit;
}

// Read configuration file
$config = json_decode(file_get_contents($configFile));

// Read user provided data
$username = $_SERVER["PHP_AUTH_USER"] ?: $_GET["username"];
$password = $_SERVER["PHP_AUTH_PW"] ?: $_GET["password"];
$hostname = $_GET["hostname"];
$ipAddress = $_GET["ipaddress"] ?: $_SERVER["REMOTE_ADDR"];

// Check whether the configured user provider exists
$userProviderFile = __DIR__ . "/user-providers/" . $config->userProvider . ".class.php";
if (!file_exists($userProviderFile))
{
	header("HTTP/1.1 500 Internal Server Error");
	echo "No such user provider: " . $config->userProvider;
	exit;
}

// Create user provider instance
require_once $userProviderFile;

$userProviderClass = "UserProvider_" . $config->userProvider;

$userProvider = new $userProviderClass($config->userProviderConfig->{$config->userProvider});

// Check whether the given username and password match to an account
if (!$userProvider->checkAuth($username, $password))
{
	header("WWW-Authenticate: Basic realm=\"DynDNS Update\"");
	header("HTTP/1.0 401 Unauthorized");
	echo ERROR_BADAUTH;
	exit;
}

// Check whether the given hostname is valid
if (!$hostname or !$userProvider->checkHost($username, $hostname))
{
	header("HTTP/1.1 400 Bad Request");
	echo ERROR_INVALID_HOST;
	exit;
}

// Check whether the given IP address is valid
if (!$ipAddress or !filter_var($ipAddress, FILTER_VALIDATE_IP))
{
	header("HTTP/1.1 400 Bad Request");
	echo ERROR_INVALID_IP;
	exit;
}

// Build nsupdate commands
$nsupdateCommands = array();
$nsupdateCommands[] = "server localhost";
$nsupdateCommands[] = "zone " . $userProvider->getZoneOfHost($username, $hostname);
$nsupdateCommands[] = "update delete " . $hostname;
$nsupdateCommands[] = "update add " . $hostname . " 60 A " . $ipAddress;
$nsupdateCommands[] = "send";

// Execute nsupdate
exec("echo \"" . implode("\n", $nsupdateCommands) . "\" | nsupdate", $output, $returnCode);

// Check whether nsupdate responded with an error
if ($returnCode)
{
	echo ERROR_DNSERROR;
}
else
{
	// Run post process commands
	$postProcessCommands = $userProvider->getPostProcessCommands($username);
	foreach ($postProcessCommands as $postProcessCommand)
	{
		$commandLine = $postProcessCommand->command;
		$commandLine = str_replace("%username%", $username, $commandLine);
		$commandLine = str_replace("%hostname%", $hostname, $commandLine);
		$commandLine = str_replace("%ipaddress%", $ipAddress, $commandLine);

		if ($postProcessCommand->noWait)
		{
			$commandLine .= " > /dev/null 2>&1 &";
		}

		exec($commandLine);
	}

	echo ERROR_OK . " " . $ipAddress;
}