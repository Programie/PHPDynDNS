# PHP DynDNS

## Introduction

PHP DynDNS is a very simple DynDNS service. It allows you to update your bind name server via a simple HTTP request.

Authorization is done using HTTP Basic Auth or using the *username* and *password* URL variables.

The full URL looks like *http://dyndns.example.com/?hostname=myhost.example.com* (HTTP Basic Auth) or *http://dyndns.example.com/?hostname=myhost.example.com&username=myuser&password=mypassword* (URL variable auth).

You may also specify the IP address using a GET variable (ipaddress=<address>). Example: *http://dyndns.example.com/?hostname=myhost.example.com&ipaddress=90.123.50.43*

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
