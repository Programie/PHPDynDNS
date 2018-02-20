# PHP DynDNS

[![Build Status](https://travis-ci.org/Programie/PHPDynDNS.svg)](https://travis-ci.org/Programie/PHPDynDNS)
[![Coverage Status](https://coveralls.io/repos/github/Programie/PHPDynDNS/badge.svg?branch=master)](https://coveralls.io/github/Programie/PHPDynDNS?branch=master)
## Introduction

PHP DynDNS is a very simple DynDNS service. It allows you to update your DNS server via a simple HTTP request.

Authorization is done using HTTP Basic Auth or using the *username* and *password* URL variables.

The full URL looks like *https://dyndns.example.com/?hostname=myhost.example.com* (HTTP Basic Auth) or *https://dyndns.example.com/?hostname=myhost.example.com&username=myuser&password=mypassword* (URL variable auth).

You may also specify the IP address using a GET variable (ipaddress=your.ip.address.here). Example: *https://dyndns.example.com/?hostname=myhost.example.com&ipaddress=79.206.99.18* or *https://dyndns.example.com/?hostname=myhost.example.com&ipaddress=2003:66:ef5d:9300:5899:991b:8542:b19f*

PHP DynDNS also supports IPv6! To update both, the IPv4 and IPv6 address, just make two requests (one with the IPv4 and one with the IPv6 address).

## Requirements

   * A domain
   * A web server running PHP 7
   * A DNS server (e.g. bind)

## Installation

There are two methods for getting the latest release:

* (recommended) Download the [latest release](https://github.com/Programie/PHPDynDNS/releases/latest) and extract it to your web directory from where you want to serve the files (e.g. /var/www/dyndns)
* Clone this repository to your web directory from where you want to serve the files (e.g. /var/www/dyndns)

Directly cloning the repository requires you to download the required dependencies using [Composer](https://getcomposer.org): `composer install`

Once downloaded, continue with the following steps:

* Copy *config.sample.json* to *config.json*
* Edit config.json to fit your needs (see [wiki](https://github.com/Programie/PHPDynDNS/wiki/Configuration) for details)
* Configure your DNS server to allow update requests from the webserver (e.g. `allow-update { localhost; }` in bind)
* [Configure your router](https://github.com/Programie/PHPDynDNS/wiki/Configure-your-router) to automatically request the URL of your DynDNS service after each reconnect (or create a cronjob with curl/wget).

## Important

   Make sure the config.json is not readable via HTTP! On Apache this is already done using the *.htaccess* file.
