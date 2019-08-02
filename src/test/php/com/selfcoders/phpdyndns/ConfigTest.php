<?php
namespace com\selfcoders\phpdyndns;

use com\selfcoders\phpdyndns\config\Config;
use com\selfcoders\phpdyndns\config\Host;
use com\selfcoders\phpdyndns\config\User;
use JsonMapper;
use JsonMapper_Exception;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @throws JsonMapper_Exception
     */
    public function setUp(): void
    {
        $configFile = __DIR__ . "/../../../../../../config.sample.json";

        $mapper = new JsonMapper;
        $mapper->bExceptionOnMissingData = true;
        $mapper->bStrictObjectTypeChecking = true;

        $this->config = $mapper->map(json_decode(file_get_contents($configFile)), new Config);
    }

    public function testConfig(): void
    {
        $this->assertInstanceOf(Config::class, $this->config);

        $this->assertEquals("localhost", $this->config->server);
        $this->assertEquals(60, $this->config->ttl);
        $this->assertEquals("-k /path/to/keyfile", $this->config->nsupdateOptions);

        $user = $this->config->getUser("myuser");

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals("myuser", $user->username);
        $this->assertTrue($user->checkPassword("mypassword"));
        $this->assertEquals("nohup sudo /opt/some-script.sh %hostname% %ipaddress% %entrytype%", $user->postProcess);

        $host = $user->getHost("myhost.example.com");

        $this->assertInstanceOf(Host::class, $host);
        $this->assertEquals("myhost.example.com", $host->hostname);
        $this->assertEquals("example.com", $host->zone);
    }
}