<?php
namespace ExEss\Cms\Api\V8_Custom\Params;

use ExEss\Cms\Doctrine\Type\Locale;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeLocaleParams extends AbstractParams
{
    /**
     * dont require parent's constructor arguments
     */
    public function __construct()
    {
        // do nothing
    }

    public function getLocale(): string
    {
        return $this->arguments['locale'];
    }

    /**
     * @inheritdoc
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['locale']);

        $resolver
            ->setDefault('locale', Locale::DEFAULT)
            ->setAllowedTypes('locale', ['string'])
            ->setAllowedValues('locale', \array_keys(Locale::getValues()));
    }
}
