<?php
namespace ExEss\Cms\Api\V8_Custom\Params;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SidebarParams extends AbstractParams
{
    public function getId(): string
    {
        return $this->arguments['id'];
    }

    public function getObject(): string
    {
        return $this->arguments['object'];
    }

    public function getSidebarType(): string
    {
        return $this->arguments['sidebar'];
    }

    /**
     * Method to configure the options passed through this class.
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('id')
            ->setAllowedTypes('id', ['string'])
            ->setAllowedValues(
                'id',
                $this->validatorFactory->createClosure([
                    new Assert\NotBlank(),
                    new Assert\Uuid(['strict' => false]),
                ])
            )
        ;

        $resolver
            ->setRequired('object')
            ->setAllowedTypes('object', ['string'])
            ->setAllowedValues('object', ['Users'])
        ;

        $resolver
            ->setDefault('sidebar', 'blue')
            ->setAllowedTypes('sidebar', ['string'])
        ;
    }
}
