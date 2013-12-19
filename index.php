<?php
$config = json_decode(file_get_contents(__DIR__ . "/data/config.json"));

$username = $_SERVER["PHP_AUTH_USER"] ?: $_GET["username"];
$password = $_SERVER["PHP_AUTH_PW"] ?: $_GET["password"];
$hostname = $_GET["hostname"];

require_once __DIR__ . "/user-providers/" . $config->userProvider . ".class.php";

$userProviderClass = "UserProvider_" . $config->userProvider;

$userProvider = new $userProviderClass($config->userProviderConfig->{$config->userProvider});

if (!$userProvider->checkAuth($username, $password))
{
	header("WWW-Authenticate: Basic realm=\"DynDNS Update\"");
	header("HTTP/1.0 401 Unauthorized");
	exit;
}

if (!$hostname)
{
	echo "A hostname is required!";
	exit;
}

if (!$userProvider->checkHost($username, $hostname))
{
	header("HTTP/1.1 403 Forbidden");
	echo "You are not allowed to update the host '" . $hostname . "'!";
	exit;
}

exec("echo \"server localhost\nzone " . $userProvider->getZoneOfHost($username, $hostname) . "\nupdate delete " . $hostname . ".\nupdate add " . $hostname . ". 60 A " . $_SERVER["REMOTE_ADDR"] . "\nsend\" | nsupdate", $output, $returnCode);

if ($returnCode)
{
	echo "error";
}
else
{
	$postProcessCommands = $userProvider->getPostProcessCommands($username);
	foreach ($postProcessCommands as $postProcessCommand)
	{
		$commandLine = $postProcessCommand->command;
		$commandLine = str_replace("%username%", $username, $commandLine);
		$commandLine = str_replace("%hostname%", $hostname, $commandLine);

		if ($postProcessCommand->noWait)
		{
			$commandLine .= " > /dev/null 2>&1 &";
		}

		exec($commandLine);
	}

	echo "good " . $_SERVER["REMOTE_ADDR"];
}