<?php

namespace Tests;

use \AM\ProxyServices\Proxy;
use \PHPUnit\Framework\TestCase;

class ProxyTest extends TestCase
    {

	/**
	 * Should construct from json
	 *
	 * @return void
	 */

	public function testShouldConstructFromJson()
	    {
		$types = array(
			  "SOCKS4" => CURLPROXY_SOCKS4,
			  "SOCKS5" => CURLPROXY_SOCKS5,
			  "HTTP"   => CURLPROXY_HTTP,
			 );

		$json  = json_decode(file_get_contents(__DIR__ . "/datasets/1.json"));
		$proxy = new Proxy($json);
		$this->assertEquals($json->proxy, $proxy->proxy);
		$this->assertEquals($types[$json->type], $proxy->type);
		$this->assertTrue($proxy->validate());
		$this->assertEquals(json_encode($json), (string) $proxy);


	    } //end testShouldConstructFromJson()


	/**
	 * Should validate invalid proxy
	 *
	 * @return void
	 */

	public function testShouldValidateInvalidProxy()
	    {
		$json  = json_decode(file_get_contents(__DIR__ . "/datasets/invalid.json"));
		$proxy = new Proxy($json);
		$this->assertFalse($proxy->validate());
	    } //end testShouldValidateInvalidProxy()


    } //end class

?>
