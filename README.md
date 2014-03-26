# PHP DynDNS

## Introduction

PHP DynDNS is a very simple DynDNS service. It allows you to update your bind name server via a simple HTTP request.

Authorization is done using HTTP Basic Auth or using the *username* and *password* URL variables.

The full URL looks like *http://dyndns.example.com/?hostname=myhost.example.com* (HTTP Basic Auth) or *http://dyndns.example.com/?hostname=myhost.example.com&username=myuser&password=mypassword* (URL variable auth).

You may also specify the IP address using a GET variable (ipaddress=your.ip.address.here). Example: *http://dyndns.example.com/?hostname=myhost.example.com&ipaddress=79.206.99.18*

PHP DynDNS also supports IPv6! To update both, the IPv4 and IPv6 address, just make two requests (one with the IPv4 and one with the IPv6 address).

## Requirements

   * A domain
   * A web server running PHP
   * bind

## Installation

   * Clone this repository to your web directory from where you want to serve the files (e.g. /var/www/dyndns)
   * Copy *config.sample.json* to *config.json* (See the *config.json* wiki page for details)
   * Edit config.json to fit your needs
   * Configure your router to automatically do a request to the URL of your DynDNS service after reconnect (Or create a cronjob with curl/wget).

## Important

   Make sure the config.json is not readable via HTTP! On Apache this is already done using the .htaccess file.