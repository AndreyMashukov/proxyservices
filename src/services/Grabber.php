<?php

namespace AM\ProxyServices;

use \Logics\Foundation\HTTP\HTTPclient;
use \AM\ProxyServices\Proxy;
use \AM\ProxyServices\Sender;
use \SimpleXMLElement;
use \Exception;
use \DOMDocument;

class Grabber
    {

	/**
	 * Start grabber service
	 *
	 * @param string $config Grabber configuration path
	 *
	 * @return void
	 */

	static public function start(string $config)
	    {
		$grabber = Grabber::configure($config);

		foreach ($grabber["sources"] as $data)
		    {
			$http = new HTTPClient($data["url"], $data["params"]);
			if ($data["method"] === "GET")
			    {
				$result = $http->get();
			    }
			else
			    {
				$result = $http->post();
			    } //end if

			if ($http->lastcode() === 200)
			    {
				$proxies = preg_split($grabber["delimiter"], $result);
				foreach ($proxies as $proxydata)
				    {
					$proxy = [
					    "proxy" => trim(preg_replace("/[^0-9^:^.]+/ui", "", $proxydata)),
					    "type"  => mb_strtoupper($data["params"]["type"]),
					];

					$object = new Proxy(json_decode(json_encode($proxy)));

					$sender = new Sender($object, $grabber);
					$sender->send();
				    } //end foreach

			    } //end if

		    } //end foreach

	    } //end start()


	/**
	 * Configure grabber for work
	 *
	 * @param string $config Config file
	 *
	 * @return array Grabber configuration
	 */

	protected function configure(string $config):array
	    {
		Grabber::validateConfig($config);
		$config  = new SimpleXMLElement(file_get_contents($config));
		$grabber = [];
		$grabber["output"]    = (string) $config->output;
		$grabber["delimiter"] = (string) $config->delimiter;
		$grabber["parallels"] = (int) $config->output["parallels"];
		$grabber["sources"]   = [];

		foreach ($config->sources->source as $source)
		    {
			$parameters = [];

			foreach ($source->parameters->parameter as $param)
			    {
				$parameters[(string) $param["name"]] = (string) $param;
			    } //end foreach

			$grabber["sources"][] = [
			    "url"    => (string) $source->request,
			    "method" => mb_strtoupper((string) $source->request["method"]),
			    "params" => $parameters,
			];
		    } //end foreach

		return $grabber;
	    } //end _configure()


	/**
	 * Validate config
	 *
	 * @param string $config Config file
	 *
	 * @return void
	 *
	 * @throws Exception Invalid configuration file
	 *
	 * @exceptioncode EXCEPTION_INVALID_CONFIG_FILE
	 */

	protected function validateConfig(string $config)
	    {
		libxml_use_internal_errors(true);
		$doc = new DOMDocument("1.0", "utf-8");
		$doc->loadXML(file_get_contents($config));
		if ($doc->schemaValidate(__DIR__ . "/schemas/config.xsd") === false)
		    {
			throw new Exception("Invalid configuration file", EXCEPTION_INVALID_CONFIG_FILE);
		    } //end if

	    } //end _validateConfig()


    } //end class

?>
