<?php

namespace AM\ProxyServices\Interfaces;

use \AM\ProxyServices\Proxy;

interface Storage
    {

	/**
	 * Construct storage
	 *
	 * @param Proxy $proxy   Proxy to save or send
	 * @param array $grabber Grabber configuration
	 *
	 * @return void
	 */

	public function __construct(Proxy $proxy, array $grabber);


	/**
	 * Add proxy to storage or data transporter
	 *
	 * @return void
	 */

	public function send();


    } //end interface


?>