<?php

namespace Tests;

use \AdService\Container;
use \AM\ProxyServices\Grabber;
use \AM\ProxyServices\Proxy;
use \Logics\Tests\InternalWebServer;
use \PHPUnit_Framework_TestCase;
use \SimpleXMLElement;
use \AdService\XMLGenerator;
use \DOMDocument;
use \DOMXPath;
use \Exception;

/**
 * @runTestsInSeparateProcesses
 */

class GrabberTest extends PHPUnit_Framework_TestCase
    {

	use InternalWebServer;

	/**
	 * Name folder which should be removed after tests
	 *
	 * @var string
	 */
	protected $remotepath;

	/**
	 * Testing host
	 *
	 * @var string
	 */
	protected $host;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */

	protected function setUp()
	    {
		define("CONTAINER_DIR", __DIR__ . "/container");

		$c = new Container("proxy_grabber");
		$c->clear();

		$this->remotepath = $this->webserverURL();
		$this->host       = $this->remotepath . "/HTTPclientResponder.php";
	    } //end setUp()


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */

	protected function tearDown()
	    {
		$c = new Container("proxy_grabber");
		$c->clear();

		unset($this->object);
	    } //end tearDown()


	/**
	 * Should get proxy list from server
	 *
	 * @return void
	 */

	public function testShouldGetProxyListFromServer()
	    {
		$c = new Container("proxy_grabber");
		$this->assertEquals(0, count($c));
		$config  = __DIR__ . "/config/config.xml";

		$doc = new DOMDocument("1.0", "utf-8");
		$doc->load($config);
		$xpath    = new DOMXPath($doc);
		$parent   = $xpath->query('//sources/source');
		$next     = $xpath->query('//sources/source/request');
		$new_item = $doc->createElement('request', $this->host);
		$attr = $new_item->appendChild($doc->createAttribute("method"));
		$attr->appendChild($doc->createTextNode("GET"));
		$parent->item(0)->insertBefore($new_item, $next->item(0));
		$next[0]->parentNode->removeChild($next[0]);
		$doc->save($config);


		Grabber::start($config);

		$expected = [
		    "111.111.111.111:11",
		    "222.222.222.222:22",
		    "333.333.333.333:33",
		    "444.444.444.444:44",
		];

		$c = new Container("proxy_grabber");
		$this->assertEquals(4, count($c));
		foreach ($c as $element)
		    {
			$proxy = new Proxy(json_decode($element["data"]));
			$this->assertRegExp("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}:[0-9]{1,6}$/ui", $proxy->proxy);
		    } //end foreach

	    } //end testShouldGetProxyListFromServer()


	/**
	 * Should get proxy list from server by post method
	 *
	 * @return void
	 */

	public function testShouldGetProxyListFromServerByPostMethod()
	    {
		$c = new Container("proxy_grabber");
		$this->assertEquals(0, count($c));
		$config  = __DIR__ . "/config/postconfig.xml";

		$doc = new DOMDocument("1.0", "utf-8");
		$doc->load($config);
		$xpath    = new DOMXPath($doc);
		$parent   = $xpath->query('//sources/source');
		$next     = $xpath->query('//sources/source/request');
		$new_item = $doc->createElement('request', $this->host);
		$attr = $new_item->appendChild($doc->createAttribute("method"));
		$attr->appendChild($doc->createTextNode("POST"));
		$parent->item(0)->insertBefore($new_item, $next->item(0));
		$next[0]->parentNode->removeChild($next[0]);
		$doc->save($config);


		Grabber::start($config);

		$expected = [
		    "111.111.111.111:11",
		    "222.222.222.222:22",
		    "333.333.333.333:33",
		    "444.444.444.444:44",
		];

		$c = new Container("proxy_grabber");
		$this->assertEquals(4, count($c));
		foreach ($c as $element)
		    {
			$proxy = new Proxy(json_decode($element["data"]));
			$this->assertRegExp("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}:[0-9]{1,6}$/ui", $proxy->proxy);
		    } //end foreach

	    } //end testShouldGetProxyListFromServerByPostMethod()


	/**
	 * Should not allow to set invalid config file
	 *
	 * @return void
	 *
	 * @exceptioncode EXCEPTION_INVALID_CONFIG_FILE
	 */

	public function testShouldNotAllowToSetInvalidConfigFile()
	    {
		define("EXCEPTION_INVALID_CONFIG_FILE", 1);
		$config  = __DIR__ . "/config/invalidconfig.xml";
		$this->expectException(Exception::class);
		$this->expectExceptionCode(EXCEPTION_INVALID_CONFIG_FILE);
		Grabber::start($config);
	    } //end testShouldNotAllowToSetInvalidConfigFile()


    } //end class

?>
