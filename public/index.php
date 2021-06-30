<?php

use ExEss\Bundle\CmsBundle\Dictionary\Format;
use ExEss\Cms\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/vendor/autoload.php';

// Activating XHProf can be done by creating a '.xhprof' file at project's root,
// or per request, by either having an "xhprof=" parameter in query string or by setting the "xhprof" cookie.
if (
    \function_exists("xhprof_enable") && (
        \file_exists("../.xhprof") || isset($_GET["xhprof"]) || isset($_COOKIE["xhprof"])
    )
) {
    \register_shutdown_function(function (): void {
        $xhprofData = \xhprof_disable();
        \file_put_contents(\sys_get_temp_dir() . "/" . \uniqid() . ".xhprof", \serialize($xhprofData));
    });
    \xhprof_enable(\XHPROF_FLAGS_MEMORY | \XHPROF_FLAGS_CPU);
}

\setlocale(\LC_CTYPE, 'en_US.UTF-8');
\date_default_timezone_set(Format::UTC_TIMEZONE);

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(
        \explode(',', $trustedProxies),
        Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST
    );
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
