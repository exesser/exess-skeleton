<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Cache\Command;

use ExEss\Bundle\CmsBundle\Component\Cache\CacheAdapterFactory;
use ExEss\Bundle\CmsBundle\Component\Cache\Dictionary;
use ExEss\Bundle\CmsBundle\Command\AbstractAdminCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheClearCommand extends AbstractAdminCommand
{
    /**
     * @var string
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected static $defaultName = 'exess:cache:clear';

    private CacheAdapterFactory $cacheAdapterFactory;

    /**
     * @throws \InvalidArgumentException If the provided file does not exist.
     */
    public function __construct(CacheAdapterFactory $cacheAdapterFactory)
    {
        parent::__construct();

        $this->cacheAdapterFactory = $cacheAdapterFactory;
    }

    protected function configure(): void
    {
        $this->setDescription('Clear all the cache including exess specific caches');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title(\sprintf('Flushing caches'));

        $hasErrors = false;
        foreach (Dictionary::CACHE_POOLS as $pool => $ttl) {
            $cacheAdapter = $this->cacheAdapterFactory->create($pool, $ttl);

            if ($cacheAdapter->clear()) {
                $this->io->success(\sprintf('Flushed all \'%s\' entries', $pool));
            } else {
                $this->io->error(\sprintf('Failed flushing entries for \'%s\'', $pool));

                $hasErrors = true;
            }
        }

        return $hasErrors ? 1 : 0;
    }
}
