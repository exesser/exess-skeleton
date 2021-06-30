<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece;

use Doctrine\Common\Collections\Criteria;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Query\Conditions;

class AssociationPiece implements PieceInterFace
{
    private Conditions $conditions;

    public function __construct(Conditions $conditions)
    {
        $this->conditions = $conditions;
    }

    public function getRelation(): string
    {
        return $this->conditions->getRelation();
    }

    public function getCriteria(): Criteria
    {
        return $this->conditions->getCriteria();
    }
}
