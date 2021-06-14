<?php
namespace ExEss\Cms\Parser;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use ExEss\Cms\Parser\Translator\FatEntityTranslator;
use ExEss\Cms\Parser\Translator\QueryTranslator;

class PathResolverOptions
{
    private const SUPPORTED_TRANSLATORS = [
        FatEntityTranslator::class,
        QueryTranslator::class,
    ];

    private string $translator = FatEntityTranslator::class;

    private ?Collection $externalLinks = null;

    private array $allBeans = [];

    private bool $cacheable = false;

    private ?QueryBuilder $queryBuilder = null;

    private ?string $lastAlias = null;

    public function getExternalLinks(): ?Collection
    {
        return $this->externalLinks;
    }

    public function setExternalLinks(?Collection $externalLinks): self
    {
        $this->externalLinks = $externalLinks;

        return $this;
    }

    public function getAllBeans(): array
    {
        return $this->allBeans;
    }

    public function setAllBeans(array $allBeans): self
    {
        $this->allBeans = $allBeans;

        return $this;
    }

    public function isCacheable(): bool
    {
        return $this->cacheable && empty($this->allBeans) && empty($this->externalLinks);
    }

    public function setCacheable(bool $cacheable): self
    {
        $this->cacheable = $cacheable;

        return $this;
    }

    public function getTranslator(): string
    {
        return $this->translator;
    }

    /**
     * @throws \InvalidArgumentException In case of an unsupported translator.
     */
    public function setTranslator(string $translator): self
    {
        if (!\in_array($translator, static::SUPPORTED_TRANSLATORS, true)) {
            throw new \InvalidArgumentException("Unsupported translator $translator");
        }

        $this->translator = $translator;

        return $this;
    }

    public function getQueryBuilder(): ?QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function setQueryBuilder(QueryBuilder $queryBuilder): void
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getLastAlias(): ?string
    {
        return $this->lastAlias;
    }

    public function setLastAlias(?string $lastAlias): void
    {
        $this->lastAlias = $lastAlias;
    }
}
