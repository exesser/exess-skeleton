<?php declare(strict_types=1);

namespace ExEss\Cms\Component\ExpressionParser\Parser;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy;
use ExEss\Cms\CRUD\Config\CrudMetadata;
use ExEss\Cms\Exception\ConfigInvalidException;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\Component\ExpressionParser\Parser\Translator\QueryTranslator;

class ExpressionParser
{
    private const METHOD_PREFIXES = ['get', 'is'];

    private PathResolver $pathResolver;

    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        PathResolver $pathResolver
    ) {
        $this->pathResolver = $pathResolver;
        $this->em = $em;
    }

    public function parse(
        Expression $expression,
        ExpressionParserOptions $parserOptions,
        ?PathResolverOptions $resolverOptions = null
    ): void {
        // create an options object if none has been passed
        $resolverOptions = $resolverOptions ?? new PathResolverOptions();
        $baseEntity = $parserOptions->getBaseEntity();

        // set the translator to be used by the resolver
        if ($parserOptions->isQueryFormat()) {
            $resolverOptions->setTranslator(QueryTranslator::class);
            $resolverOptions->setLastAlias(null);
            if (!$resolverOptions->getQueryBuilder()) {
                throw new \InvalidArgumentException(
                    "QueryTranslator expects a QueryBuilder in the resolver options"
                );
            }
        }

        // formatting options given in the expression overrule the dev's choice
        if ($expression->getFormatEnums() !== null) {
            $devFormatEnums = $parserOptions->isReplaceEnumValueWithLabel();
            $parserOptions->setReplaceEnumValueWithLabel($expression->getFormatEnums());
        }

        // disallow path caching in case the baseEntity is a Model class, it makes no sense
        if ($baseEntity instanceof Model) {
            $resolverOptions->setCacheable(false);
        }

        $field = $expression->getField();

        if (!$expression->hasPath()) {
            // verify if it might be an expression that is baseEntity-less
            if (($value = $this->pathResolver->resolve(null, $field, $resolverOptions)) !== null) {
                $expression->setReplacement($value);
            }
            // we are just on the baseEntity
            $entity = $baseEntity;
        } else {
            $entity = $this->pathResolver->resolve(
                $baseEntity,
                $expression->getPath(),
                $resolverOptions
            );
        }

        if (!$expression->hasReplacement()) {
            if ($entity === null) {
                // no replacement can be done as there's nothing to search a replacement on
            } elseif ($entity instanceof Model || !\is_iterable($entity)) {
                if ($parserOptions->isQueryFormat()) {
                    $select = $resolverOptions->getLastAlias() . ".$field";
                    $expression->setReplacement($select, $entity);
                } else {
                    $expression->setReplacement(
                        $this->getReplacement($entity, $expression, $parserOptions),
                        $entity
                    );
                }
            } else {
                $replacements = [];
                $replacedFromBean = [];
                foreach ($entity as $thisBean) {
                    $replacements[] = $this->getReplacement($thisBean, $expression, $parserOptions);
                    $replacedFromBean[] = $thisBean;
                }
                if (!empty($replacements)) {
                    $expression->setReplacement($replacements, $replacedFromBean);
                }
            }
        }

        // restore the dev's choice
        if (isset($devFormatEnums)) {
            $parserOptions->setReplaceEnumValueWithLabel($devFormatEnums);
        }

        // mark expression as parsed
        $expression->setParsed(true);
    }

    /**
     * @return mixed
     * @throws ConfigInvalidException If $baseEntity is not an object.
     */
    private function getReplacement(
        object $baseEntity,
        Expression $expression,
        ExpressionParserOptions $options
    ) {
        return $this->convertLine(
            $this->formatValue(
                $this->getValue($baseEntity, $expression->getField()),
                $options
            )
        );
    }

    /**
     * @param mixed $line
     * @return mixed
     */
    private function convertLine($line)
    {
        if ($line instanceof \DateTimeInterface) {
            $line = $line->format('d-m-Y') . ($line->format('H:i') !== '00:00' ? $line->format(' H:i') : "");
        } elseif ($line === true || $line === false) {
            $line = $line ? 'true' : 'false';
        }

        return $line;
    }

    /**
     * @return mixed
     * @throws ConfigInvalidException In case the value can't be retrieved.
     */
    private function getValue(object $entity, string $field)
    {
        $class = \get_class($entity);

        $metadata = null;
        if ($entity instanceof Proxy || $this->em->getMetadataFactory()->hasMetadataFor($class)) {
            $metadata = $this->em->getClassMetadata($class);
        }

        if ($metadata) {
            if ($field === 'object_name') {
                return $metadata->getName();
            }
            if (\substr($field, 0, 4) === 'crud') {
                /**
                 * @see CrudMetadata::getCrudListC1R1()
                 * @see CrudMetadata::getCrudListC1R2()
                 * @see CrudMetadata::getCrudListC2R1()
                 * @see CrudMetadata::getCrudListC2R2()
                 */
                $getter = 'get' . \ucfirst($field);
                return CrudMetadata::$getter($metadata, $entity);
            }

            if (!$metadata->hasField($field)) {
                throw new ConfigInvalidException(
                    \sprintf('Config error. Field %s not found on entity %s.', $field, $class)
                );
            }

            // ensure data is loaded
            if ($entity instanceof Proxy) {
                $entity->__load();
            }

            $property = $metadata->getReflectionClass()->getProperty($field);
            $property->setAccessible(true);

            return $property->getValue($entity);
        }

        foreach (self::METHOD_PREFIXES as $methodPrefix) {
            $method = $methodPrefix . \ucfirst($field);
            if (\method_exists($entity, $method)) {
                return $entity->$method();
            }
        }

        if (\property_exists($entity, $field)) {
            return $entity->$field;
        }
        if ($entity instanceof Model && $entity->offsetExists($field)) {
            return $entity->$field;
        }

        throw new ConfigInvalidException(
            "Config error: Method $method nor property $field found on object " . \get_class($entity)
        );
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function formatValue(
        $value,
        ExpressionParserOptions $options
    ) {
        if ($options->getContext() === ExpressionParserOptions::CONTEXT_JSON) {
            if ($value instanceof Model || \is_array($value)) {
                $value = \json_encode($value);
            }
            if (\is_string($value)) {
                return \str_replace(['\\', '"'], ['\\\\', '\\"'], $value);
            }
        }

        // leave the value 'as is'
        return $value;
    }
}
