<?php
namespace ExEss\Bundle\CmsBundle\Soap;

class SoapClient extends \SoapClient
{
    protected int $requestTimeout = 30;

    protected array $options;

    // @codingStandardsIgnoreStart
    public function __construct($wsdl, $options = [])
    {
        // @codingStandardsIgnoreEnd
        $this->options = $options;

        if (isset($this->options['connection_timeout']) && (int)$this->options['connection_timeout'] > 0) {
            $this->requestTimeout = (int)$this->options['connection_timeout'];
        }

        // Soap and xdebug don't work nicely together when it fails to initialise the SoapClient
        // See: https://bugs.php.net/bug.php?id=47584
        $xdebug = false;
        if (\function_exists('xdebug_disable')) {
            /** @noinspection ForgottenDebugOutputInspection */
            $xdebug = \xdebug_is_enabled();
            \xdebug_disable();
        }

        // set the configured request timeout
        $timeout = $this->overrideSocketTimeout();

        parent::__construct($wsdl, $options);

        // reset request timeout
        $this->resetSocketTimeout($timeout);

        // Re-enable xdebug if it was enabled before the constructor.
        if ($xdebug && \function_exists('xdebug_enable')) {
            /** @noinspection ForgottenDebugOutputInspection */
            \xdebug_enable();
        }
    }

    // @codingStandardsIgnoreStart
    public function __doRequest(
        $request,
        $location,
        $action,
        $version,
        $one_way = 0
    ): string {

        $timeout = $this->overrideSocketTimeout();

        $response = parent::__doRequest($request, $location, $action, $version, $one_way);

        $this->resetSocketTimeout($timeout);

        return $response;
    }

    private function overrideSocketTimeout(): string
    {
        $timeout = \ini_get('default_socket_timeout');
        \ini_set('default_socket_timeout', $this->requestTimeout);

        return $timeout;
    }

    private function resetSocketTimeout(string $timeout): void
    {
        \ini_set('default_socket_timeout', $timeout);
    }
}
