#! /bin/bash

set -e

function test_record()
{
    address=$(dig @bind +short $1 myhost.example.com)

    if [[ ${address} != $2 ]]; then
        echo "Expected $2 but got ${address}"
        exit 1
    fi
}

printf "server bind\nzone example.com\nupdate delete myhost.example.com A\nupdate delete myhost.example.com AAAA\nsend" | nsupdate

curl -s "http://app/?hostname=myhost.example.com&myip=1.2.3.4" -u myuser:mypassword
test_record A "1.2.3.4"
test_record AAAA ""

curl -s "http://app/?hostname=myhost.example.com&myipv6=f410:a02c:a197:2bdf:b9b:e5ac:5c85:f5bd" -u myuser:mypassword
test_record A "1.2.3.4"
test_record AAAA "f410:a02c:a197:2bdf:b9b:e5ac:5c85:f5bd"

curl -s "http://app/?hostname=myhost.example.com&myip=10.20.30.40&myipv6=cbf0:84cb:61a5:139f:ba91:358c:15c5:aca4" -u myuser:mypassword
test_record A "10.20.30.40"
test_record AAAA "cbf0:84cb:61a5:139f:ba91:358c:15c5:aca4"