<?php

/**
 * This is a proxy services starter
 *
 * use CLI or cron for start needen service
 * for example:
 * # php starter.php proxy_grabber
 *
 * Proxy services configuration file you can find in src/services/config/config.xml
 * This grabber i'm use in REST API proxy services
 */

namespace Proxy;

set_time_limit(0);

if (isset($argv) === false)
    {
	$argv = [];
    } //end if

$name = md5(implode("_", $argv));

if (file_exists(__DIR__ . "/src/services/cron/" . $name) === true)
    {
	$pid = file_get_contents(__DIR__ . "/src/services/cron/" . $name);

	if (posix_kill($pid, 0))
	    {
		exit();
	    } //end if

    } //end if

$pid = posix_getpid();
file_put_contents(__DIR__ . "/src/services/cron/" . $name, $pid);

require_once __DIR__ . "/vendor/autoload.php";

$services = [
    "proxy_grabber" => \AM\ProxyServices\Grabber::class,
];

$configs = [
    "proxy_grabber" => __DIR__ . "/src/services/config/config.xml",
];

if (isset($argv[1]) === true)
    {
	$config = $configs[$argv[1]];
	$services[$argv[1]]::start($config);
    } //end if

?>
