<?php declare(strict_types=1);

use ExEss\Bundle\CmsBundle\Dictionary\Format;

\umask(0);
// disable wsdl caching during tests
\ini_set('soap.wsdl_cache_enabled', "0");

\setlocale(\LC_CTYPE, 'en_US.UTF-8');
\date_default_timezone_set(Format::UTC_TIMEZONE);

\Hamcrest\Util::registerGlobalFunctions();
