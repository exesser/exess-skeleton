<?php
namespace ExEss\Cms\Parser\Resolver\Piece;

use ExEss\Cms\Parser\Query\Conditions;

class LinkedFatEntitiesPiece implements PieceInterFace
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

    public function getWhere(): array
    {
        return $this->conditions->getWhere();
    }

    public function getOrder(): string
    {
        return $this->conditions->getOrder();
    }

    /**
     * if null, no limit will be applied
     */
    public function getLimit(): ?int
    {
        return $this->conditions->getLimit();
    }
}
