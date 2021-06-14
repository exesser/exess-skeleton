<?php
namespace ExEss\Cms\Api\V8_Custom\Params;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class LoginParams extends AbstractParams
{
    public function getUsername(): string
    {
        return $this->arguments['username'];
    }

    public function getPassword(): string
    {
        return $this->arguments['password'];
    }

    /**
     * Method to configure the options passed through this class.
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('username')
            ->setAllowedTypes('username', ['string'])
            ->setAllowedValues('username', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
                new Assert\Length(['max' => 60]),
                new Assert\Regex(['pattern' => '/[0-9a-zA-Z_.-@]/']),
            ]));

        $resolver
            ->setRequired('password')
            ->setAllowedTypes('password', ['string'])
            ->setAllowedValues('password', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
                new Assert\Length(['min' => 8, 'max' => 255]),
            ]));
    }

    /**
     * We dont want to display detailed errors for credentials, just show a generic message
     *
     * @param array[] ...$arguments
     * @return AbstractParams
     * @throws InvalidOptionsException When credentials are invalid.
     */
    public function configure(array ...$arguments): self
    {
        try {
            return parent::configure(...$arguments);
        } catch (InvalidOptionsException $e) {
            throw new InvalidOptionsException('credentials do not meet the requirements');
        }
    }

    public function returnJwt(): bool
    {
        return false;
    }
}
