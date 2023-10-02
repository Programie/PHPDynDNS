# PHP DynDNS

[![DockerHub](https://img.shields.io/badge/download-DockerHub-blue?logo=docker)](https://hub.docker.com/r/programie/phpdyndns)
[![GitHub release](https://img.shields.io/github/v/release/Programie/PHPDynDNS)](https://github.com/Programie/PHPDynDNS/releases/latest)

## Introduction

PHP DynDNS is a very simple DynDNS service. It allows you to update your DNS server via a simple HTTP request.

Authorization is done using HTTP Basic Auth or using the `username` and `password` URL variables.

The full URL looks like `https://dyndns.example.com/?hostname=myhost.example.com` (HTTP Basic Auth) or `https://dyndns.example.com/?hostname=myhost.example.com&username=myuser&password=mypassword` (URL variable auth).

You may also specify the IPv4 and IPv6 address using GET variables (`myip=your.ipv4.address.here` and `myipv6=your:ipv6:address:here`).

Examples:

* IPv4 only: `https://dyndns.example.com/?hostname=myhost.example.com&myip=79.206.99.18`
* IPv6 only: `https://dyndns.example.com/?hostname=myhost.example.com&myipv6=2003:66:ef5d:9300:5899:991b:8542:b19f`
* IPv4 + IPv6: `https://dyndns.example.com/?hostname=myhost.example.com&myip=79.206.99.18&myipv6=2003:66:ef5d:9300:5899:991b:8542:b19f`

## Requirements

   * A domain
   * A web server running PHP 7.2 or newer
   * A DNS server (e.g. bind)

Or use the ready to run Docker image (see section [Installation using Docker](#installation-using-docker)).

## Installation

There are two methods for getting the latest release:

* (recommended) Download the [latest release](https://github.com/Programie/PHPDynDNS/releases/latest) and extract it to your web directory from where you want to serve the files (e.g. `/var/www/dyndns`)
* Clone this repository to your web directory from where you want to serve the files (e.g. `/var/www/dyndns`)

Directly cloning the repository requires you to download the required dependencies using [Composer](https://getcomposer.org): `composer install`

Once downloaded, continue with the following steps:

* Copy `config.sample.json` to `config.json`
* Edit `config.json` to fit your needs (see [Configuration section](#configuration) bellow for details)
* Configure your DNS server to allow update requests from the webserver (e.g. `allow-update { localhost; }` in bind)
* [Configure your router](#configure-your-router) to automatically request the URL of your DynDNS service after each reconnect (or create a cronjob with curl/wget).

## Installation using Docker

PHPDynDNS is also provided as a Docker image. Just pull it from [Docker Hub](https://hub.docker.com/r/programie/phpdyndns).

Mount your `config.json` to `/app/config.json`

Example command to start the container:

```bash
docker run -d --name phpdyndns -p 80:80 -v /path/to/config.json:/app/config.json:ro programie/phpdyndns
```

## Important

Make sure the config.json is not readable via HTTP! On Apache this is already done using the `.htaccess` file.

## Configuration

The configuration is done using JSON stored in the `config.json` file which looks like the following:

```json
{
    "server": "localhost",
    "ttl": 60,
    "users": {
        "myuser": {
            "password_hash": "$5$1IekWfmq$yVTjQcWsX/qK.TIws0NWAj0mmlyDFsSMw6nSFYHcyH8",
            "hosts": {
                "myhost.example.com": {
                    "zone": "example.com"
                },
                "anotherhost.example.com": {
                    "zone": "anotherhost.example.com"
                }
            },
            "post_process": "nohup sudo /opt/some-script.sh %hostname% %ipv4address%"
        }
    }
}
```

### Properties

* `server`: The DNS server to connect to (default: `localhost`)
* `ttl`: The TTL (time to live) for all DNS entries managed by PHPDynDNS (default: `60`)
* `users`: A map listing all users (the key of each entry is the username)
   * `password_hash`: The hashed password of the user (e.g. created with `mkpasswd -m sha-256`)
   * `hosts`: A map listing all hosts this user is able to update (the key of each entry is the hostname to update)
      * `zone`: The zone which contains this hostname
   * `post_process`: A command which should be executed after successfully updating the DNS entry (can contain placeholders, see note below)

### Placeholders for post_process option

There are a few placeholders which can be used in the `post_process` option to be replaced on execution.

* `%username%`: The username
* `%hostname%`: The hostname
* `%ipv4address%`: The new IPv4 address (if available)
* `%ipv6address%`: The new IPv6 address (if available)

## Configure your router

### Fritz!Box

* DynDNS Provider: `Custom`
* Update URL: `https://your.domain/path/to/phpdyndns?username=<username>&password=<pass>&hostname=<domain>&myip=<ipaddr>`
* Domain: `your.configured.host.of.your.domain`
* Username: `your configured username`
* Password: `your configured password`

### Cronjob (crontab)

Use this variant if your router does not support sending updates to (custom) DynDNS services. Every request will cause an update to your DNS zone!

```
* * * * * curl https://your.domain/path/to/phpdyndns?username=your-username&password=your-password&hostname=your.domain.tld
```

This will update your domain `your.domain.tld` every minute.

Replace `your-username`, `your-password` and `your.domain.tld` with your configured username, password and domain.

## Post Processing

PHP DynDNS can trigger user defined commands after the DynDNS hostname has been updated successfully.

A command might be `/opt/reload-iptables.sh` which automatically reloads iptables using the new IP-Address of the DynDNS hostname.

The post-processing command can be individually configured for each user in the `config.json`.

### Example: Dynamic firewall using iptables

PHP DynDNS can execute commands after the hostname has been updated successfully. And such a command might reload your iptables rules from a file which also forces iptables to re-lookup your dynamic hostname.

The post-processing script might look like the following:

```sh
#! /bin/sh
/sbin/iptables-restore < /path/to/your/iptables.rules
```

The `iptables.rules` might look like the following:

```
*filter

# Set defaults
:INPUT DROP [0:0]
:FORWARD DROP [0:0]
:OUTPUT ACCEPT [0:0]

# Allow already established connections
-A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
-A OUTPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
-A FORWARD -m state --state ESTABLISHED,RELATED -j ACCEPT

# Allow local connections
-A INPUT -i lo -j ACCEPT
-A OUTPUT -o lo -j ACCEPT
-A FORWARD -i lo -o lo -j ACCEPT

# Allow HTTP
-A INPUT -p tcp --dport 80 -j ACCEPT

# DynDNS
-A INPUT -p tcp --dport 22 -s yourhost.example.com -j ACCEPT

COMMIT
```

**Note:** You have to call the script with root permissions (e.g. sudo)! Simply allow the user running the webserver (e.g. www-data) to execute the script as root (e.g. add `www-data ALL=(ALL) NOPASSWD:/opt/scripts/update_dyndns_iptables.sh` to your `/etc/sudoers` file).