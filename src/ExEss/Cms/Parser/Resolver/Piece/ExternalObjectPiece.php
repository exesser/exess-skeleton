<?php
namespace ExEss\Cms\Parser\Resolver\Piece;

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
