<?php

namespace AM\ProxyServices;

use \AdService\Container;
use \AM\ProxyServices\Proxy;
use \AM\ProxyServices\Interfaces\Storage;

class Sender implements Storage
    {

	/**
	 * Proxy
	 *
	 * @var Proxy Proxy data
	 */
	protected $proxy;

	/**
	 * Grabber
	 *
	 * @var array Grabber config
	 */
	protected $grabber;

	/**
	 * Construct storage
	 *
	 * @param Proxy $proxy   Proxy to save or send
	 * @param array $grabber Grabber configuration
	 *
	 * @return void
	 */

	public function __construct(Proxy $proxy, array $grabber)
	    {
		$this->proxy   = $proxy;
		$this->grabber = $grabber;
	    } //end __construct()


	/**
	 * Send proxy to container
	 *
	 * @return void
	 */

	public function send()
	    {
		$out = new Container($this->grabber["output"], $this->grabber["parallels"]);

		if ($this->proxy->validate() === true)
		    {
			$out->add((string) $this->proxy);
		    } //end if

	    } //end send()


    } //end class

?>
