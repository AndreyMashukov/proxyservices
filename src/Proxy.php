<?php

namespace AM\ProxyServices;

use \stdClass;

class Proxy
    {

	/**
	 * Proxy
	 *
	 * @var string
	 */
	public $proxy = null;

	/**
	 * Type
	 *
	 * @int Type
	 */
	public $type = null;

	/**
	 * Prepare class to work
	 *
	 * @param stdClass $json Proxy json
	 *
	 * @return void
	 */

	public function __construct(stdClass $json)
	    {
		if (isset($json->proxy) === true && isset($json->type) === true)
		    {
			$proxy = trim(preg_replace("/[^0-9^:^.]+/ui", "", $json->proxy));
			if (preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}:[0-9]{1,6}$/ui", $proxy) > 0)
			    {
				$this->proxy = $proxy;
				$this->type  = $this->_getType($json->type);
			    } //end if

		    } //end if

	    } //end __construct()


	/**
	 * Get proxy type
	 *
	 * @param string $type Proxy type
	 *
	 * @return int Type
	 */

	private function _getType(string $type):int
	    {
		$types = array(
			  "SOCKS4" => CURLPROXY_SOCKS4,
			  "SOCKS5" => CURLPROXY_SOCKS5,
			  "HTTP"   => CURLPROXY_HTTP,
			  "HTTPS"  => CURLPROXY_HTTP,
			 );

		return $types[$type];
	    } //end _getType()


	/**
	 * Reverse proxy type
	 *
	 * @param int $type Proxy type
	 *
	 * @return string Type
	 */

	private function _reverseType(int $type):string
	    {
		$types = array(
			  CURLPROXY_SOCKS4 => "SOCKS4",
			  CURLPROXY_SOCKS5 => "SOCKS5",
			  CURLPROXY_HTTP   => "HTTP",
			 );

		return $types[$type];
	    } //end _reverseType()


	/**
	 * Validate
	 *
	 * @return bool Status
	 */

	public function validate()
	    {
		$types = [CURLPROXY_SOCKS4, CURLPROXY_SOCKS5, CURLPROXY_HTTP];
		if (in_array($this->type, $types) === true && $this->proxy !== null)
		    {
			return true;
		    }
		else
		    {
			return false;
		    } //end if

	    } //end validate()


	/**
	 * To string, return json
	 *
	 * @return string Encoded json
	 */

	public function __toString():string
	    {
		return json_encode([
		    "proxy" => $this->proxy,
		    "type"  => $this->_reverseType($this->type),
		]);
	    } //end __toString()


    } //end class


?>
