<?php

namespace ExEss\Cms\Base\Request;

use ExEss\Cms\Helper\DataCleaner;
use ExEss\Cms\Servicemix\Request\Filters\Filter;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractRequest extends AbstractRequestBase
{
    protected array $options = [];

    public function getPage(): int
    {
        return $this->options['page'];
    }

    public function getLimit(): int
    {
        return $this->options['limit'];
    }

    public function getSortField(): ?string
    {
        return $this->options['sortField'];
    }

    public function getSortOrder(): string
    {
        return $this->options['sortOrder'];
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->options['filters'];
    }

    /**
     * @return Filter[]
     */
    public function getNormalizedFilters(): array
    {
        return $this->options['normalizedFilters'];
    }

    /**
     * Create a request
     *
     * @param array|array[] ...$options
     *
     * @return static
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException When options are not accessible.
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException When undefined options are passed.
     */
    public static function createFrom(array ...$options)
    {
        // combine the passed option arrays
        $options = \array_merge(...$options);

        $static = new static();
        $static->resolve($options);

        return $static;
    }

    /**
     * @inheritDoc
     */
    protected function configureExtraOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('page', 1)
            ->setAllowedTypes('page', ['null', 'int']);

        $resolver
            ->setDefault('limit', -1)
            ->setAllowedTypes('limit', ['null', 'int']);

        $resolver->setDefault(
            'sortField',
            function (Options $options) {
                if (empty($options['sortBy'])) {
                    return $this->getDefaultSortField();
                }

                $sortOptions = \explode(' ', $options['sortBy']);
                return isset($sortOptions[0])? $sortOptions[0]: $this->getDefaultSortField();
            }
        );

        $resolver->setDefault(
            'sortOrder',
            function (Options $options) {
                if (empty($options['sortBy'])) {
                    return $this->getDefaultSortOrder();
                }

                $sortOptions = \explode(' ', $options['sortBy']);
                return isset($sortOptions[1])? $sortOptions[1]: $this->getDefaultSortOrder();
            }
        );

        $resolver
            ->setDefault('filters', [])
            ->setAllowedTypes('filters', ['null', 'string', 'array'])
            ->setNormalizer('filters', function (Options $options, $value) {
                if (\is_string($value)) {
                    return DataCleaner::jsonDecode($value);
                }
                return $value;
            })
        ;

        $resolver
            ->setDefined('normalizedFilters')
            ->setDefault(
                'normalizedFilters',
                function (Options $options) {
                    $normalizedFilters = [];

                    if (isset($options['fixedFilter'])) {
                        $fixedFilters = $options['fixedFilter'];
                        if (!\is_array($options['filters']) && !\is_array($fixedFilters)) {
                            return [];
                        }
                        if (!\is_array($options['filters'])) {
                            $filters = $fixedFilters;
                        } elseif (\is_array($fixedFilters)) {
                            $filters = \array_merge($options['filters'], $options['fixedFilter']);
                        }
                    } else {
                        $filters = $options['filters'];
                    }

                    if (\is_array($filters)) {
                        foreach ($filters as $field => $filter) {
                            foreach ($filter as $key => $value) {
                                if (!empty($value['value'])) {
                                    $normalizedFilters[] = Filter::createFrom(
                                        $field,
                                        $value['operator'],
                                        $value['value']
                                    );
                                }
                            }
                        }
                    }

                    return $normalizedFilters;
                }
            );
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function jsonSerializeExtra(): array
    {
        return [
            'pagination' => [
                'page' => $this->getPage(),
                'limit' => $this->getLimit()
            ],
            'sort' => [
                'field' => $this->getSortField(),
                'order' => $this->getSortOrder()
            ],
            'filters' => $this->getNormalizedFilters(),
        ];
    }

    protected function getDefaultSortField(): ?string
    {
        return null;
    }

    protected function getDefaultSortOrder(): string
    {
        return 'DESC';
    }
}
