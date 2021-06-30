<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Action;

class Arguments
{
    public function toArray(): array
    {
        return (array) $this;
    }
}
