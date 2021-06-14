<?php
namespace ExEss\Cms\Soap;

use WsdlToPhp\PackageBase\AbstractSoapClientBase as AbstractSoapClientBaseOrig;

abstract class AbstractSoapClientBase extends AbstractSoapClientBaseOrig
{
    //@codingStandardsIgnoreStart
    public function getSoapClientClassName($soapClientClassName = null): string
    {
        //@codingStandardsIgnoreEnd
        return SoapClient::class;
    }
}
