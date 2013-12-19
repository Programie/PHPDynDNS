<?php
require_once __DIR__ . "/AbstractUserProvider.class.php";

/**
 * User Provider to read users from XML file
 *
 * Required configuration (In config.json):
 * - filename: The path to the XML file
 *
 * The XML file should be in the following format:
 * <?xml version="1.0" ?>
 * <users>
 *   <user name="name-of-the-user" password="password-of-the-user">
 *     <host zone="example.com">host1.example.com</host>
 *     <host zone="another.example.com">myhost.another.example.com</host>
 *     <postprocess>/opt/some-command-to-execute-after-successfull-update.sh</postprocess>
 *   </user>
 *   <user name="another-user" password="password-of-the-user">
 *     <host zone="example.com">anotheruser.example.com</host>
 *   </user>
 * </users>
 */
class UserProvider_xml extends AbstractUserProvider
{
	private $users;

	public function __construct($configData)
	{
		$document = new DOMDocument;
		$document->load($configData->filename);

		$this->users = array();

		$userNodes = $document->getElementsByTagName("user");
		foreach ($userNodes as $userNode)
		{
			$hosts = array();
			$hostNodes = $userNode->getElementsByTagName("host");
			foreach ($hostNodes as $hostNode)
			{
				$host = new StdClass;
				$host->zone = $hostNode->getAttribute("zone");
				$hosts[$hostNode->nodeValue] = $host;
			}

			$postProcessCommands = array();
			$postProcessNodes = $userNode->getElementsByTagName("postprocess");
			foreach ($postProcessNodes as $postProcessNode)
			{
				$postProcessCommand = new StdClass;
				$postProcessCommand->command = $postProcessNode->nodeValue;
				$postProcessCommand->noWait = $postProcessNode->getAttribute("nowait") == "true" ? true : false;
				$postProcessCommands[] = $postProcessCommand;
			}

			$user = new StdClass;
			$user->password = $userNode->getAttribute("password");
			$user->hosts = $hosts;
			$user->postProcessCommands = $postProcessCommands;
			$this->users[$userNode->getAttribute("name")] = $user;
		}
	}

	public function checkAuth($username, $password)
	{
		return isset($this->users[$username]) and $this->users[$username]->password == $password;
	}

	public function checkHost($username, $hostname)
	{
		return isset($this->users[$username]->hosts[$hostname]);
	}

	public function getPostProcessCommands($username)
	{
		return $this->users[$username]->postProcessCommands;
	}

	public function getZoneOfHost($username, $hostname)
	{
		return $this->users[$username]->hosts[$hostname]->zone;
	}
}