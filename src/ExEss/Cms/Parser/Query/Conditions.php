<?php
namespace ExEss\Cms\Parser\Query;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use ExEss\Cms\Dictionary\Format;

class Conditions
{
    public const DEFAULT_ORDER = 'date_entered desc';

    private string $relation;

    private array $where = [];

    private string $order = self::DEFAULT_ORDER;

    /**
     * @var ?int
     */
    private ?int $limit = 1;

    public function __construct(string $relation)
    {
        $this->relation = $relation;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function setRelation(string $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    public function getWhere(): array
    {
        // this must be evaluated upon get, as the getter is used upon translation of path to value
        $where = [];
        foreach ($this->where as $queryOption) {
            // match a string between %, not encapsulated by quotes
            // %TODAY% will match, '%TODAY%' will not
            if (
                \preg_match('/(?<![\'"])%(.*)%(?![\'"])/', $queryOption, $subMatches)
                && ($dateValue = \strtotime($subMatches[1])) !== false
            ) {
                $queryOption = \str_replace(
                    $subMatches[0],
                    "'".\date(Format::DB_DATE_FORMAT, $dateValue)."'",
                    $queryOption
                );
            }
            $where[] = \trim($queryOption);
        }

        return $where;
    }

    public function setWhere(array $where): self
    {
        $this->where = $where;

        return $this;
    }

    public function getOrder(): string
    {
        return $this->order;
    }

    public function setOrder(string $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getCriteria(): Criteria
    {
        $criteria = Criteria::create();

        foreach ($this->getWhere() as $where) {
            $criteria->andWhere($this->getComparisonFor($where));
        }

        $order = [];
        foreach (\explode(',', $this->order) as $orderClause) {
            $parts = \explode(' ', \trim($orderClause));

            $order[\trim($parts[0])] = \strtolower(\trim($parts[1] ?? 'asc'));
        }
        $criteria->orderBy($order);

        return $criteria;
    }

    private function getComparisonFor(string $where): Comparison
    {
        $inSeparator = " " . Comparison::IN . " ";
        if (\stristr($where, $inSeparator)) {
            $parts = \explode(\strtolower($inSeparator), \strtolower($where));
            $values = \explode(',', \trim($parts[1], '() '));
            $values = \array_map(function (string $value) {
                $value = \trim($value, '"\' ');
                return $this->format($value);
            }, $values);
            return new Comparison(\trim($parts[0]), Comparison::IN, $values);
        }

        $supported = [
            Comparison::LTE,
            Comparison::GTE,
            Comparison::EQ,
            Comparison::NEQ,
            Comparison::LT,
            Comparison::GT,
        ];

        foreach ($supported as $condition) {
            if (\stristr($where, $condition)) {
                $parts = \explode($condition, $where);
                $value = \trim($parts[1], '"\' ');
                return new Comparison(\trim($parts[0]), $condition, $this->format($value));
            }
        }

        throw new \InvalidArgumentException("Condition $where not supported ye!");
    }

    /**
     * @return string|bool|int|null
     */
    private function format(string $value)
    {
        if (\is_numeric($value)) {
            return (int) $value;
        } elseif ($value === 'true') {
            return true;
        } elseif ($value === 'false') {
            return false;
        } elseif ($value === 'null') {
            return null;
        }

        return $value;
    }
}
