<?php
namespace ExEss\Cms\Robo\Task;

use Symfony\Component\Dotenv\Dotenv;

trait EnvironmentAware
{
    protected function bootstrapEnvironment(): void
    {
        $givenEnv = $_ENV['APP_ENV'] ?? null;
        $env = (new Dotenv(false));

        $envDirectory = __DIR__ . '/../../../../../';

        $env->loadEnv("$envDirectory.env");
        if ($givenEnv !== null) {
            if (\file_exists("$envDirectory.env.$givenEnv")) {
                $env->loadEnv("$envDirectory.env.$givenEnv");
            }
            if (\file_exists("$envDirectory.env.$givenEnv.local")) {
                $env->loadEnv("$envDirectory.env.$givenEnv.local");
            }
        }
    }
}
