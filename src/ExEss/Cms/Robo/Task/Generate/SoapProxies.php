<?php

namespace ExEss\Cms\Robo\Task\Generate;

use ExEss\Cms\DependencyInjection\Compiler\SoapServicesPass;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Robo\Result;
use Robo\Task\BaseTask;
use ExEss\Cms\Robo\Task\TaskHelper;
use ExEss\Cms\Soap\AbstractSoapClientBase;
use Symfony\Component\Console\Style\SymfonyStyle;
use WsdlToPhp\PackageGenerator\ConfigurationReader\GeneratorOptions;
use WsdlToPhp\PackageGenerator\Generator\Generator;

class SoapProxies extends BaseTask
{
    use TaskHelper;

    public const PROXY_DIR = 'src/ExternalApi';

    private SymfonyStyle $io;

    private bool $storeWsdl;

    public function __construct(SymfonyStyle $io, bool $storeWsdl)
    {
        $this->io = $io;
        $this->storeWsdl = $storeWsdl;
    }

    public function run(): Result
    {
        $container = $this->getContainer();
        $services = $container->getParameter(SoapServicesPass::SOAP_PROXY_SERVICES);

        foreach ($services as $serviceName => $configParameter) {
            $options = $container->getParameter($configParameter);
            if (!isset($options['wsdl'], $options['namespace'])) {
                return new Result(
                    $this,
                    Result::EXITCODE_ERROR,
                    "$serviceName is a generated soap service, but is missing the wsdl or namespace option"
                );
            }
            $wsdl = $options['wsdl'];
            $serviceName = $options['namespace'];

            $this->io->title("Regenerating for $serviceName");

            // test if wsdl is reachable, if not, try to use a local copy
            $wsdlContent = $this->getWsdlContents($wsdl);
            if ($wsdlContent === null) {
                if (!\file_exists($wsdl = $this->getLocalCopyFileName($serviceName))) {
                    return Result::error(
                        $this,
                        "Could not regenerate for options $serviceName: wsdl not reachable and no local copy found."
                    );
                }
                $this->io->text("Using local copy $wsdl for $serviceName (options unreachable)");
            } else {
                $this->io->text("Using $wsdl");
            }

            // regenerate wrappers
            $this->generateWrapper(
                $serviceName,
                $wsdl,
                $options['proxy_host'] ?? null,
                $options['proxy_port'] ?? null,
                $options['stream_context'] ?? []
            );

            if ($this->storeWsdl) {
                $this->storeWsdlContents($serviceName, $wsdl, $wsdlContent);
            }
        }

        return Result::success($this);
    }

    private function storeWsdlContents(string $serviceName, string $wsdl, ?string $wsdlContent): void
    {
        // store wsdl contents to local file in case next time we build we're offline
        if (empty($wsdlContent)) {
            return;
        }

        $this->io->text("Downloading and storing WSDL/XSD for $serviceName");

        // for services that import XSD's we need to download those as well
        $wsdlContent = $this->storeXsdImports($serviceName, $wsdl, $wsdlContent);

        // store wsdl file
        $this->saveFile(self::PROXY_DIR . "/$serviceName.wsdl", $wsdlContent);
    }

    private function storeXsdImports(string $serviceName, string $wsdl, ?string $wsdlContent): string
    {
        // search for xsd imports
        $matches = [];
        \preg_match_all('/schemaLocation="(.*?)"/', $wsdlContent, $matches);

        if (empty($matches[0])) {
            return $wsdlContent ?? '';
        }

        $parts = \parse_url($wsdl);
        $path = "$parts[scheme]://$parts[host]" . \dirname($parts['path']);

        // prefix all local file names with the service name
        $localFileNames = [];
        foreach ($matches[1] as $match) {
            $fileName = \basename($match);
            if (\strpos($fileName, '=') !== false) {
                $queryStringParts = \explode('=', $fileName);
                $fileName = \array_pop($queryStringParts);
            }
            if (\substr($fileName, \strlen($fileName) - 4) !== '.xsd') {
                $fileName .= '.xsd';
            }
            $fileName = \ucfirst($fileName);
            $localFileNames[] = "{$serviceName}{$fileName}";
        }

        // replace the original xsd path with our own
        $wsdlContent = \str_replace($matches[1], $localFileNames, $wsdlContent);

        // download xsd's
        foreach ($matches[1] as $idx => $match) {
            // parse content in case there are nested XSD's
            $subWsdl = \strpos($match, 'http') !== 0 ? "$path/$match" : $match;
            $subWsdlContent = $this->storeXsdImports(
                $serviceName,
                $subWsdl,
                $this->getWsdlContents($subWsdl)
            );
            $this->saveFile(self::PROXY_DIR . "/$localFileNames[$idx]", $subWsdlContent);
        }

        return $wsdlContent;
    }

    private function saveFile(string $fileName, string $contents): void
    {
        \file_put_contents($fileName, $contents);
        $this->io->text("Stored $fileName");
    }

    private function getWsdlContents(string $url): ?string
    {
        $parts = \parse_url($url);

        $client = new Client([
            'base_uri' => "$parts[scheme]://$parts[host]" . (isset($parts['port']) ? ":$parts[port]" : ''),
            'timeout' => 3,
            'connect_timeout' => 3,
        ]);
        try {
            return $client
                ->request(
                    'GET',
                    ($parts['path'] ?? '') . (isset($parts['query']) ? "?$parts[query]" : '')
                )
                ->getBody()
                ->getContents()
            ;
        } catch (ConnectException $e) {
            return null;
        }
    }

    private function getLocalCopyFileName(string $service): string
    {
        return self::PROXY_DIR . "/$service.wsdl";
    }

    private function generateWrapper(
        string $serviceName,
        string $wsdl,
        ?string $proxyHost,
        ?string $proxyPort,
        array $streamContext
    ): void {
        $options = GeneratorOptions::instance();
        $options
            ->setOrigin($wsdl)
            ->setDestination('./' . self::PROXY_DIR . "/$serviceName")
            ->setNamespace("ExternalApi\\$serviceName")
            ->setStandalone(false)
            ->setSoapClientClass(AbstractSoapClientBase::class)
            ->setSrcDirname('')
            ->setSoapOptions([
                AbstractSoapClientBase::WSDL_CACHE_WSDL => 0,
                AbstractSoapClientBase::WSDL_STREAM_CONTEXT => $streamContext,
                AbstractSoapClientBase::WSDL_PROXY_HOST => $proxyHost !== '192.168.56.1' ? $proxyHost : null,
                AbstractSoapClientBase::WSDL_PROXY_PORT => $proxyPort !== '8888' ? $proxyPort : null,
                AbstractSoapClientBase::WSDL_FEATURES => 1,
            ])
            ->setGenerateTutorialFile(false)
        ;

        // Package generation
        (new Generator($options))->generatePackage();

        $this->io->text("Successfully generated proxies for $serviceName");
    }
}
