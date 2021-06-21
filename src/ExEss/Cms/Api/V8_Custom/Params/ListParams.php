<?php declare(strict_types=1);

namespace ExEss\Cms\Api\V8_Custom\Params;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\OrderBy;
use ExEss\Cms\Entity\ListSortingOption;
use ExEss\Cms\Api\V8_Custom\Params\Validator\ValidatorFactory;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListParams extends AbstractParams
{
    private EntityManager $em;

    public function __construct(
        ValidatorFactory $validatorFactory,
        EntityManager $em
    ) {
        parent::__construct($validatorFactory);
        $this->em = $em;
    }

    public function getPage(): int
    {
        return $this->arguments['page'];
    }

    public function getSortBy(): OrderBy
    {
        return $this->arguments['sortBy'];
    }

    public function needsOnlyRecordCount(): bool
    {
        return $this->arguments['onlyRecordCount'];
    }

    public function needsExportToCSV(): bool
    {
        return $this->arguments['exportToCSV'];
    }

    public function getRecordIds(): array
    {
        return $this->arguments['recordIds'];
    }

    public function getRecordId(): string
    {
        return $this->arguments['recordId'];
    }

    public function getRecordType(): ?string
    {
        return $this->arguments['recordType'] ?? null;
    }

    public function getExtraActionData(): array
    {
        return $this->arguments['extraActionData'];
    }

    public function getFilters(): array
    {
        return $this->arguments['filters'];
    }

    public function getQuery(): string
    {
        return $this->arguments['query'];
    }

    public function getParams(): array
    {
        return $this->arguments['params'];
    }

    public function getQuickSearch(): string
    {
        return $this->arguments['quickSearch'];
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function setDefined(OptionsResolver $optionsResolver, array $arguments): void
    {
        $optionsResolver->setDefined(\array_keys($arguments));
    }

    /**
     * @inheritdoc
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('page', 1)
            ->setAllowedTypes('page', ['int', 'string'])
            ->addNormalizer('page', function (Options $option, $value): ?int {
                return \intval($value);
            });

        $resolver
            ->setDefault('sortBy', null)
            ->setAllowedTypes('sortBy', ['null', 'string', OrderBy::class])
            ->setNormalizer('sortBy', function (Options $options, $value): OrderBy {
                if (empty($value)) {
                    return ListSortingOption::getDefault();
                }
                if (\is_object($value) && $value instanceof OrderBy) {
                    return $value;
                }
                /** @var ListSortingOption $listSorting */
                $listSorting = $this->em->getRepository(ListSortingOption::class)->find($value);

                return new OrderBy($listSorting->getSortKey(), $listSorting->getOrderBy());
            })
        ;
        $resolver
            ->setDefault('onlyRecordCount', false)
            ->setAllowedTypes('onlyRecordCount', ['bool'])
        ;
        $resolver
            ->setDefault('exportToCSV', false)
            ->setAllowedTypes('exportToCSV', ['bool'])
        ;
        $resolver
            ->setDefault('recordIds', [])
            ->setAllowedTypes('recordIds', ['array'])
        ;
        $resolver
            ->setDefault('recordId', '')
            ->setAllowedTypes('recordId', ['string'])
        ;
        $resolver
            ->setDefault('recordType', null)
            ->setAllowedTypes('recordType', ['string', 'null'])
        ;
        $resolver
            ->setDefault('extraActionData', [])
            ->setAllowedTypes('extraActionData', ['array'])
        ;
        $resolver
            ->setDefault('filters', [])
            ->setAllowedTypes('filters', ['array'])
        ;
        $resolver
            ->setDefault('params', [])
            ->setAllowedTypes('params', ['array'])
        ;
        $resolver
            ->setDefault('query', '')
            ->setAllowedTypes('query', ['string'])
        ;
        $resolver
            ->setDefault('quickSearch', '')
            ->setAllowedTypes('quickSearch', ['string'])
        ;
        $resolver
            ->setDefault('uniqueListKey', '')
            ->setAllowedTypes('uniqueListKey', ['string'])
        ;
    }
}
