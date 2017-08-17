# PHP DynDNS

## Introduction

PHP DynDNS is a very simple DynDNS service. It allows you to update your bind name server via a simple HTTP request.

Authorization is done using HTTP Basic Auth or using the *username* and *password* URL variables.

The full URL looks like *https://dyndns.example.com/?hostname=myhost.example.com* (HTTP Basic Auth) or *https://dyndns.example.com/?hostname=myhost.example.com&username=myuser&password=mypassword* (URL variable auth).

You may also specify the IP address using a GET variable (ipaddress=your.ip.address.here). Example: *https://dyndns.example.com/?hostname=myhost.example.com&ipaddress=79.206.99.18* or *https://dyndns.example.com/?hostname=myhost.example.com&ipaddress=2003:66:ef5d:9300:5899:991b:8542:b19f*

PHP DynDNS also supports IPv6! To update both, the IPv4 and IPv6 address, just make two requests (one with the IPv4 and one with the IPv6 address).

## Requirements

   * A domain
   * A web server running PHP
   * bind

## Installation

   * Clone this repository to your web directory from where you want to serve the files (e.g. /var/www/dyndns)
   * Copy *config.sample.json* to *config.json*
   * Edit config.json to fit your needs
   * Configure your router to automatically request the URL of your DynDNS service after each reconnect (Or create a cronjob with curl/wget).

## Important

   Make sure the config.json is not readable via HTTP! On Apache this is already done using the *.htaccess* file.
