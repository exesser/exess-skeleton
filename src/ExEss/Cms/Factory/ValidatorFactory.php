<?php declare(strict_types=1);

namespace ExEss\Cms\Factory;

use Doctrine\Common\Annotations\AnnotationReader;
use ExEss\Cms\Doctrine\Type\TranslationDomain;
use Psr\Container\ContainerInterface;
use ExEss\Cms\Validators\Factory\ConstraintValidatorFactory;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;

final class ValidatorFactory
{
    public static function create(ContainerInterface $container): ValidatorInterface
    {
        // https://github.com/doctrine/annotations/issues/103
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

        $validatorBuilder = new ValidatorBuilder();
        $validatorBuilder
            ->setTranslationDomain(TranslationDomain::ERRORS)
            ->setMetadataFactory(new LazyLoadingMetadataFactory(
                new AnnotationLoader(new AnnotationReader())
            ));

        if ($container->has(ConstraintValidatorFactory::class)) {
            $validatorBuilder->setConstraintValidatorFactory($container->get(ConstraintValidatorFactory::class));
        }

        if ($container->has(Translator::class)) {
            $validatorBuilder->setTranslator($container->get(Translator::class));
        }

        return $validatorBuilder->getValidator();
    }
}
