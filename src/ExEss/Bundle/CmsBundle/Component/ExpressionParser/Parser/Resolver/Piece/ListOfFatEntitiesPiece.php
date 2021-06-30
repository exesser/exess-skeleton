<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Resolver\Piece;

class ListOfFatEntitiesPiece implements PieceInterFace
{
    private string $module;

    private string $where;

    private int $limit;

    private array $getters;

    public function __construct(string $module, string $where, int $limit = 1, array $getters = [])
    {
        $this->module = $module;
        $this->where = $where;
        $this->limit = $limit;
        $this->getters = $getters;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getWhere(): string
    {
        return $this->where;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getGetters(): array
    {
        return $this->getters;
    }
}
