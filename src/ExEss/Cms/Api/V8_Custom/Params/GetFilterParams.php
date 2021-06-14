<?php
namespace ExEss\Cms\Api\V8_Custom\Params;

use Doctrine\ORM\EntityManager;
use ExEss\Cms\Api\V8_Custom\Params\Validator\ValidatorFactory;
use ExEss\Cms\Entity\ListDynamic;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class GetFilterParams extends AbstractParams
{
    private EntityManager $em;

    public function __construct(
        ValidatorFactory $validatorFactory,
        EntityManager $em
    ) {
        parent::__construct($validatorFactory);
        $this->em = $em;
    }

    public function getList(): ListDynamic
    {
        return $this->arguments['list'];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        // @todo - remove, deprecated and not used
        $resolver->setDefined('filterKey');

        $resolver
            ->setRequired('list')
            ->setAllowedTypes('list', ['string'])
            ->setAllowedValues('list', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
                new Assert\Regex(['pattern' => self::REGEX_KEY]),
            ]))
            ->setNormalizer('list', function (Options $options, string $value): ListDynamic {
                return $this->em->getRepository(ListDynamic::class)->get($value);
            })
        ;
    }
}
