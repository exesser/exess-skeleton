<?php
namespace ExEss\Cms\Api\V8_Custom\Params;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class GetSubMenuParams extends AbstractParams
{
    public function getMainMenuKey(): string
    {
        return $this->arguments['mainMenuKey'];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('mainMenuKey')
            ->setAllowedTypes('mainMenuKey', ['string'])
            ->setAllowedValues('mainMenuKey', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
                new Assert\Regex(['pattern' => self::REGEX_KEY]),
            ]));
    }
}
