<?php declare(strict_types=1);

namespace ExEss\Cms\Component\ExpressionParser\Parser\Resolver\Piece;

use ExEss\Cms\Api\V8_Custom\Service\Security;

class SecurityPiece extends ContainerMethodPiece
{
    public function __construct(string $method)
    {
        /**
         * @see Security::getCurrentUserId()
         * @see Security::getPrimaryGroupId()
         */
        parent::__construct(
            Security::class,
            $method
        );
    }
}
