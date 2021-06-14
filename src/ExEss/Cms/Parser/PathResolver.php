<?php
namespace ExEss\Cms\Parser;

use ExEss\Cms\Parser\Resolver\Piece\CompiledPathPiece;
use ExEss\Cms\Parser\Resolver\CompiledPath;
use ExEss\Cms\Parser\Translator\PieceTranslatorInterface;
use ExEss\Cms\Parser\Translator\QueryTranslator;

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
     * @param mixed|object|null $baseFatEntity
     * @return mixed|null
     * @throws \InvalidArgumentException In case base fat entity is not an object, array or null.
     */
    public function resolve($baseFatEntity, string $fatEntityKey, ?PathResolverOptions $options = null)
    {
        if (!\is_object($baseFatEntity) && $baseFatEntity !== null) {
            throw new \InvalidArgumentException('First argument must be an object or null');
        }

        // create an options object if none has been passed
        $options = $options ?? new PathResolverOptions();

        // get the compiled path
        $path = $this->pathCompiler->compile($baseFatEntity, $fatEntityKey, $options);

        // return the translated pieces
        return $this->translatePath($path, $baseFatEntity, $options);
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

        foreach ($path as $fatEntityKey => $piece) {
            $fatEntityKey = $prefix . $fatEntityKey;
            if ($piece instanceof CompiledPathPiece) {
                $prefix = $fatEntityKey . '|';
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
                    ->translate($piece, $fatEntityKey, $linkedEntity, $options)
                ;
                if ($linkedEntity === null) {
                    return $linkedEntity;
                }
            }
        }

        return $linkedEntity;
    }
}
