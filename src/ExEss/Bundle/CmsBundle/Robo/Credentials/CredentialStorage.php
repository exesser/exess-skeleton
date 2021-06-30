<?php
namespace ExEss\Bundle\CmsBundle\Robo\Credentials;

use ExEss\Bundle\CmsBundle\Helper\DataCleaner;

class CredentialStorage
{
    private string $credentialFile = '.credentials.json';

    public function __construct(string $dir)
    {
        $this->credentialFile = $dir . '/' . $this->credentialFile;
    }

    /**
     * @throws \RuntimeException When credentials could not be read.
     */
    public function getAll(): array
    {
        if (!\file_exists($this->credentialFile)) {
            return [];
        }

        $credentials = DataCleaner::jsonDecode(\file_get_contents($this->credentialFile));

        if (!\is_array($credentials)) {
            throw new \RuntimeException('cannot read credential file');
        }

        return $credentials;
    }

    /**
     * @throws \RuntimeException When credentials could not be found for $system.
     */
    public function getCredentialsFor(string $system): array
    {
        $store = $this->getAll();

        if (!isset($store[$system])) {
            return [];
        }

        return $store[$system];
    }

    /**
     * @throws \RuntimeException When credentials could not be written to file.
     */
    public function setCredentialsFor(string $system, array $credentials): void
    {
        $store = $this->getAll();

        $store[$system] = $credentials;

        if (!\file_put_contents($this->credentialFile, \json_encode($store))) {
            throw new \RuntimeException("failed to write credential to file: {$this->credentialFile}");
        }
    }
}
