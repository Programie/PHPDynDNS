# PHP DynDNS

[![pipeline status](https://gitlab.com/Programie/PHPDynDNS/badges/master/pipeline.svg)](https://gitlab.com/Programie/PHPDynDNS/commits/master)
[![coverage report](https://gitlab.com/Programie/PHPDynDNS/badges/master/coverage.svg)](https://gitlab.com/Programie/PHPDynDNS/commits/master)
## Introduction

PHP DynDNS is a very simple DynDNS service. It allows you to update your DNS server via a simple HTTP request.

Authorization is done using HTTP Basic Auth or using the *username* and *password* URL variables.

The full URL looks like *https://dyndns.example.com/?hostname=myhost.example.com* (HTTP Basic Auth) or *https://dyndns.example.com/?hostname=myhost.example.com&username=myuser&password=mypassword* (URL variable auth).

You may also specify the IPv4 and IPv6 address using GET variables (myip=your.ipv4.address.here and myipv6=your:ipv6:address:here).

Examples:

* IPv4 only: https://dyndns.example.com/?hostname=myhost.example.com&myip=79.206.99.18
* IPv6 only: https://dyndns.example.com/?hostname=myhost.example.com&myipv6=2003:66:ef5d:9300:5899:991b:8542:b19f
* IPv4 + IPv6: https://dyndns.example.com/?hostname=myhost.example.com&myip=79.206.99.18&myipv6=2003:66:ef5d:9300:5899:991b:8542:b19f

## Requirements

   * A domain
   * A web server running PHP 7.2 or newer
   * A DNS server (e.g. bind)

Or use the ready to run Docker image (see section "Installation using Docker")

## Installation

There are two methods for getting the latest release:

* (recommended) Download the [latest release](https://gitlab.com/Programie/PHPDynDNS/tags) and extract it to your web directory from where you want to serve the files (e.g. /var/www/dyndns)
* Clone this repository to your web directory from where you want to serve the files (e.g. /var/www/dyndns)

Directly cloning the repository requires you to download the required dependencies using [Composer](https://getcomposer.org): `composer install`

Once downloaded, continue with the following steps:

* Copy *config.sample.json* to *config.json*
* Edit config.json to fit your needs (see [wiki](https://gitlab.com/Programie/PHPDynDNS/wikis/Configuration) for details)
* Configure your DNS server to allow update requests from the webserver (e.g. `allow-update { localhost; }` in bind)
* [Configure your router](https://gitlab.com/Programie/PHPDynDNS/wikis/Configure-your-router) to automatically request the URL of your DynDNS service after each reconnect (or create a cronjob with curl/wget).

## Installation using Docker

PHPDynDNS is also provided as a Docker image. Just pull it from [Docker Hub](https://hub.docker.com/r/programie/phpdyndns).

Mount your config.json to */app/config.json*

Example command to start the container:

```
docker run -d --name phpdyndns -p 80:80 -v /path/to/config.json:/app/config.json:ro programie/phpdyndns
```

## Important

   Make sure the config.json is not readable via HTTP! On Apache this is already done using the *.htaccess* file.
