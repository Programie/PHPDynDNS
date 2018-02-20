<?php
namespace com\selfcoders\phpdyndns;

class ErrorCode
{
    /**
     * The update was successful, and the hostname is now updated
     */
    const OK = "good";

    /**
     * The username and password pair do not match a registered user
     */
    const BADAUTH = "badauth";

    /**
     * The update changed no settings
     */
    const NO_CHANGE = "nochg";

    /**
     * The hostname specified does not exist in this user account
     */
    const INVALID_HOST = "nohost";

    /**
     * DNS error encountered
     */
    const DNSERROR = "dnserr";

    /**
     * IP address is invalid
     */
    const INVALID_IP = "iperror";
}