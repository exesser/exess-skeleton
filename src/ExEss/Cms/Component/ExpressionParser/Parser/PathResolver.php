<?php declare(strict_types=1);

namespace ExEss\Cms\Component\ExpressionParser\Parser;

use ExEss\Cms\Component\ExpressionParser\Parser\Resolver\Piece\CompiledPathPiece;
use ExEss\Cms\Component\ExpressionParser\Parser\Resolver\CompiledPath;
use ExEss\Cms\Component\ExpressionParser\Parser\Translator\PieceTranslatorInterface;
use ExEss\Cms\Component\ExpressionParser\Parser\Translator\QueryTranslator;

class PathResolver
{
    private PathCompiler $pathCompiler;

    /**
     * @var PieceTranslatorInterface[]|array
     */
    private array $translators;

    public function __construct(
        PathCompiler $pathCompiler,
        array $translators
    ) {
        $this->pathCompiler = $pathCompiler;
        $this->translators = $translators;
    }

    /**
     * @param mixed|object|null $baseEntity
     * @return mixed|null
     * @throws \InvalidArgumentException In case $baseEntity is not an object, array or null.
     */
    public function resolve($baseEntity, string $entityKey, ?PathResolverOptions $options = null)
    {
        if (!\is_object($baseEntity) && $baseEntity !== null) {
            throw new \InvalidArgumentException('First argument must be an object or null');
        }

        // create an options object if none has been passed
        $options = $options ?? new PathResolverOptions();

        // get the compiled path
        $path = $this->pathCompiler->compile($baseEntity, $entityKey, $options);

        // return the translated pieces
        return $this->translatePath($path, $baseEntity, $options);
    }

    /**
     * @param mixed|object|null $linkedEntity
     * @return mixed
     */
    private function translatePath(
        ?CompiledPath $path = null,
        ?object $linkedEntity,
        PathResolverOptions $options,
        string $prefix = ''
    ) {
        if (!$path instanceof CompiledPath) {
            return null;
        }

        foreach ($path as $entityKey => $piece) {
            $entityKey = $prefix . $entityKey;
            if ($piece instanceof CompiledPathPiece) {
                $prefix = $entityKey . '|';
                if ($options->getTranslator() === QueryTranslator::class) {
                    $linkedEntity = $this->translatePath($piece->getPath(), $linkedEntity, $options, $prefix);
                } else {
                    foreach ($linkedEntity as $key => &$thisLinkedBean) {
                        $thisLinkedBean = $this->translatePath($piece->getPath(), $thisLinkedBean, $options, $prefix);
                        if ($thisLinkedBean === null) {
                            unset($linkedEntity[$key]);
                        }
                    }
                }
            } else {
                $linkedEntity = $this->translators[$options->getTranslator()]
                    ->translate($piece, $entityKey, $linkedEntity, $options)
                ;
                if ($linkedEntity === null) {
                    return $linkedEntity;
                }
            }
        }

        return $linkedEntity;
    }
}
