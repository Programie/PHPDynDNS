# Changelog

## [3.3] - 2022-05-26

Push image to Docker Hub

## [3.2] - 2021-10-02

Push image to Docker Hub

## [3.1] - 2021-06-11

Build Docker Image with GitLab CI

## [3.0] - 2020-06-11

Support for updating IPv4 and IPv6 address in a single request.

**Note:** This change replaces the `ipaddress` field in the query string as well as the `post_process` config option. Use the query parameters `myip` and `myipv6` to specify the IP address. In the `post_process` command, `%ipv4address%` and `%ipv6address%` are now used as placeholders.

## [2.1] - 2019-09-19

Added Docker image

## [2.0] - 2019-09-19

* Do not store passwords in plain text (use hash)
* Changed structure of config.json a bit (see config.sample.json)
* Added ability to configure arguments passed to nsupdate call
* Added support for reading client IP from X-Forwarded-For header (requires list of trusted proxy IPs)

## [1.0] - 2015-01-15

Initial release