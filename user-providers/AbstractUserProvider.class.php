<?php
/**
 * This file should be included by all user providers
 */

interface IUserProvider
{
	/**
	 * Check whether the username and the password is correct
	 * @param string username The name of the user
	 * @param string password The password of the user
	 * @return boolean true if the username and password is correct, false if not
	 */
	public function checkAuth($username, $password);

	/**
	 * Check whether the given user is allowed to update the given hostname
	 * @param string username The name of the user
	 * @param string hostname The hostname to check
	 * @return boolean true if the user is allowed to update the hostname, false if not
	 */
	public function checkHost($username, $hostname);

	/**
	 * Get a list of post process commands of the given user
	 * Those commands were executed after a successful update
	 * @param string username The name of the user
	 * @return array An array containing the commands
	 *
	 * Each array element is an object of type StdClass containing the following properties:
	 * - command (string): The full command line which should be executed
	 * - noWait (boolean): true to not wait for the command to end, false to wait
	 */
	public function getPostProcessCommands($username);

	/**
	 * Get the zone of the given host (e.g. myhost.example.com -> example.com)
	 * @param string username The name of the user
	 * @param string hostname The hostname to get the zone from
	 * @return string The zone of the host
	 */
	public function getZoneOfHost($username, $password);
}

abstract class AbstractUserProvider implements IUserProvider
{
	// Some common stuff might be added later here
}