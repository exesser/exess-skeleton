<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Adapter\Response;

use ExEss\Bundle\CmsBundle\Adapter\Exception\DecodeException;

abstract class AbstractXmlResponse extends AbstractResponse
{
    private string $xmlBody;

    public function __construct(int $code, ?string $body, array $headers = [])
    {
        $this->xmlBody = $body;
        try {
            $xml = new \SimpleXMLElement($this->xmlBody);
            $arrayBody = (array)($xml);
        } catch (\Exception $ex) {
            throw new DecodeException('Error decoding xml response: ' . $ex->getMessage());
        }

        parent::__construct($code, $arrayBody, $headers);
    }

    public function __toString(): string
    {
        return $this->xmlBody ?? '';
    }
}
