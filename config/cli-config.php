<?php declare(strict_types=1);

/**
 * Needed for bin/doctrine to work
 *
 * @todo remove when we use Symfony Framework exclusivly
 */
use App\Kernel;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';
(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

// replace with mechanism to retrieve EntityManager in your app
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

/** @var \Doctrine\ORM\EntityManager $entityManager */
$entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

return ConsoleRunner::createHelperSet($entityManager);
