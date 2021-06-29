<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece;

use ExEss\Cms\Api\V8_Custom\Repository\ExternalObjectRepository;

class ExternalObjectPiece extends ContainerMethodPiece
{
    public function __construct(array $arguments)
    {
        /**
         * @see ExternalObjectRepository::findRelated()
         */
        parent::__construct(
            ExternalObjectRepository::class,
            'findRelated',
            $arguments
        );
    }
}
