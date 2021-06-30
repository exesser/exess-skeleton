<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\ListDynamic\Body;

use ExEss\Bundle\CmsBundle\Http\Request\AbstractJsonBody;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RowBarBody extends AbstractJsonBody
{
    public function getActionData(): array
    {
        return $this->arguments['actionData'];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('actionData')
            ->setDefault('actionData', [])
            ->setAllowedTypes('actionData', ['array']);
    }
}
