<?php
namespace com\selfcoders\phpdyndns;

use PHPUnit\Framework\TestCase;

class NSUpdateTest extends TestCase
{
    public function testGetAllCommands(): void
    {
        $nsUpdate = new NSUpdate("some.host", "my.zone", "");

        $nsUpdate->delete("*.entry.my.zone", "CNAME");
        $nsUpdate->delete("entry.my.zone", "A");
        $nsUpdate->add("entry.my.zone", 300, "A", "1.2.3.4");
        $nsUpdate->add("*.entry.my.zone", 300, "CNAME", "entry.my.zone.");

        $expected = [
            "server some.host",
            "zone my.zone",
            "update delete *.entry.my.zone CNAME",
            "update delete entry.my.zone A",
            "update add entry.my.zone 300 A 1.2.3.4",
            "update add *.entry.my.zone 300 CNAME entry.my.zone.",
            "send"
        ];

        $this->assertEquals($expected, $nsUpdate->getAllCommands());
    }
}