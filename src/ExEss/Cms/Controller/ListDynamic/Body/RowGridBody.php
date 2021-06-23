<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\ListDynamic\Body;

use ExEss\Cms\Http\Request\AbstractJsonBody;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RowGridBody extends AbstractJsonBody
{
    public function getActionData(): array
    {
        return $this->arguments['actionData'];
    }

    /**
     * Method to configure the options passed through this class.
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined('actionData')
            ->setDefault('actionData', [])
            ->setAllowedTypes('actionData', ['array']);
    }
}
