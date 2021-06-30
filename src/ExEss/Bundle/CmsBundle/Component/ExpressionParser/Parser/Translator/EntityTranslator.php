<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Translator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\Persistence\Proxy;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\AssociationPiece;
use Psr\Container\ContainerInterface;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\PathResolverOptions;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\CompiledPathPiece;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\ContainerMethodPiece;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\DateTimePiece;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\ExternalObjectPiece;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\ListOfFatEntitiesPiece;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\LogContextPiece;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\MethodPiece;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\NamespacePiece;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\PieceInterFace;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece\PropertyPiece;

/**
 * Translates a resolved path piece to an entity
 */
class EntityTranslator implements PieceTranslatorInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException In case the piece is of an invalid or unknown type.
     */
    public function translate(
        PieceInterFace $piece,
        string $entityKey,
        ?object $previous,
        PathResolverOptions $options
    ) {
        if ($piece instanceof ExternalObjectPiece) {
            $translated = \call_user_func_array(
                [$this->container->get($piece->getService()), $piece->getMethod()],
                // prepend 'previous' and the resolver options as first arguments
                \array_merge([$previous, $options], $piece->getArguments())
            );
        } elseif ($piece instanceof ContainerMethodPiece) {
            $translated = \call_user_func_array(
                [$this->container->get($piece->getService()), $piece->getMethod()],
                $piece->getArguments()
            );
        } elseif ($piece instanceof AssociationPiece) {
            /** @var ClassMetadataFactory $factory */
            $factory = $this->container->get('doctrine.orm.entity_manager')->getMetadataFactory();
            $metadata = $factory->getMetaDataFor(\get_class($previous));
            $property = $metadata->getReflectionClass()->getProperty($piece->getRelation());
            $property->setAccessible(true);

            // ensure data is loaded
            if ($previous instanceof Proxy) {
                $previous->__load();
            }

            $translated = $property->getValue($previous);

            if ($translated instanceof Collection) {
                $translated = new ArrayCollection($translated->toArray());
                $translated = $translated->matching($piece->getCriteria())->toArray();
                if (empty($translated)) {
                    $translated = null;
                }
            }
        } elseif ($piece instanceof ListOfFatEntitiesPiece) {
            // @todo fix this for entities
            // // hardcoded replacements
            // $where = \str_replace(
            //     [
            //         '#PREVIOUS_ID#',
            //         '#PREVIOUS_CLASS#',
            //     ],
            //     [
            //         $previous->id ?? '',
            //         \get_class($previous),
            //     ],
            //     $piece->getWhere()
            // );
            // // getter replacements
            // foreach ($piece->getGetters() as $search => $replaceGetter) {
            //     $where = \str_replace($search, $previous->$replaceGetter(), $where);
            // }
            // /** @var Manager $manager */
            // $manager = $this->container->get(Manager::class);
            // $search = $manager->find($piece->getModule())
            //     ->where($where)
            //     ->offset(0)
            //     ->max($piece->getLimit())
            // ;
            // $translated = $manager->getFatEntities($search->fetch())->getArrayCopy();
            // if ($piece->getLimit() === 1) {
            //     $translated = \array_pop($translated);
            // }
        } elseif ($piece instanceof DateTimePiece) {
            $translated = (new \DateTime())->format($piece->getFormat());
        } elseif ($piece instanceof MethodPiece) {
            $translated = $previous->{$piece->getMethod()}();
        } elseif ($piece instanceof PropertyPiece) {
            $translated = $previous->{$piece->getProperty()};
        } elseif ($piece instanceof LogContextPiece) {
            $translated = $piece->getContext();
        } elseif ($piece instanceof CompiledPathPiece) {
            throw new \InvalidArgumentException('A CompiledPathPiece should be resolved and not translated');
        } elseif ($piece instanceof NamespacePiece) {
            $translated = new Model($previous->getNamespace($piece->getNamespace()));
        } else {
            throw new \InvalidArgumentException('Please teach me how to translate a ' . \get_class($piece));
        }

        return $translated;
    }
}
