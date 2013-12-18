# PHP DynDNS

## Introduction

PHP DynDNS is a very simple DynDNS service. It allows you to update your bind name server via a simple HTTP request.

Authorization is done using HTTP Basic Auth or using the *username* and *password* URL variables.

The full URL looks like *http://dyndns.example.com/?hostname=myhost.example.com* (HTTP Basic Auth) or *http://dyndns.example.com/?hostname=myhost.example.com&username=myuser&password=mypassword* (URL variable auth).

## Requirements

   * A domain
   * A web server running PHP
   * bind

## Installation

   * Clone this repository to your web directory from where you want to serve the files (e.g. /var/www/dyndns)
   * Create a file *config.json* in the data directory (See the *config.json* section bellow)
   * Configure the user provider you want to use (e.g. XML or MySQL)

## User Configuration

All the user configuration stuff like allowed hostnames or post processing commands is configured via user providers.

The default user provider reads the user configuration from a XML file (See the *XML User Provider* section bellow).

## config.json

The config.json file (Stored in the data directory) is the main configuration file of PHP DynDNS.

All configuration stuff (Including user provider configuration) is done in this file.

### Structure

  {
    "userProvider" : "xml",// e.g. xml, mysql, json, ...
    "userProviderConfig" :// User provider specific configuration
    {
      "xml" :
      {
        "filename" : "data/users.xml"// Path to the XML file used by the XML User Provider
      }
    }
  }

## XML User Provider

The XML User Provider is the current default user provider. It reads the user configuration from a XML file.

The XML file must have the following structure:

  The XML file should be in the following format:
  <?xml version="1.0" ?>
  <users>
    <user name="name-of-the-user" password="password-of-the-user">
      <host zone="example.com">host1.example.com</host>
      <host zone="another.example.com">myhost.another.example.com</host>
      <postprocess>/opt/some-command-to-execute-after-successfull-update.sh</postprocess>
    </user>
    <user name="another-user" password="password-of-the-user">
      <host zone="example.com">anotheruser.example.com</host>
    </user>
  </users>

The default location of this XML file is *data/users.xml*. You can change the location in the config.json file.
