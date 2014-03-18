# PHP DynDNS

## Introduction

PHP DynDNS is a very simple DynDNS service. It allows you to update your bind name server via a simple HTTP request.

Authorization is done using HTTP Basic Auth or using the *username* and *password* URL variables.

The full URL looks like *http://dyndns.example.com/?hostname=myhost.example.com* (HTTP Basic Auth) or *http://dyndns.example.com/?hostname=myhost.example.com&username=myuser&password=mypassword* (URL variable auth).

You may also specify the IP addresses using a GET variable (ipaddresses=address1,ipaddress2,...). Example: *http://dyndns.example.com/?hostname=myhost.example.com&ipaddresses=79.206.99.18,2003:66:ef47:3400:cb0:7f2c:6c50:567d*

## Requirements

   * A domain
   * A web server running PHP
   * bind

## Installation

   * Clone this repository to your web directory from where you want to serve the files (e.g. /var/www/dyndns)
   * Create a file *config.json* in the data directory (See the *config.json* wiki page)
   * Configure the user provider you want to use (e.g. XML)
   * Configure your router to automatically do a request to the URL of your DynDNS service after reconnect (Or create a cronjob with curl/wget).

## User Configuration

All the user configuration stuff like allowed hostnames or post processing commands is configured via user providers.

The default user provider reads the user configuration from a XML file (See the *XML User Provider* wiki page).
