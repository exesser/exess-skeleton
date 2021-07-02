<?php
namespace ExEss\Cms\Robo\Task\Generate;

use Robo\Result;
use Robo\Task\BaseTask;

class OpenApiContract extends BaseTask
{
    private const ROOT_DIR = __DIR__ . "/../../../../..";

    private ?string $fileName = null;

    public function __construct(?string $fileName = null)
    {
        $this->fileName = $fileName;
    }

    public function run(): Result
    {
        // @todo re-implement this
        return new Result($this, Result::EXITCODE_OK);

        if (!isset($_ENV['ENV_URL'])) {
            return new Result($this, Result::EXITCODE_ERROR, "Missing ENV_URL in .env");
        }

        \define('API_HOST', \rtrim($_ENV['ENV_URL'], '/') . '/Api');

        try {
            $openApi = \OpenApi\scan([
                self::ROOT_DIR . '/Api',
            ]);
            $file = $this->fileName ?? (\realpath(self::ROOT_DIR) . '/public/Api/open-api.yml');
            if (\file_exists($file) && \is_file($file)) {
                \unlink($file);
            }
            \file_put_contents($file, $openApi->toYaml());
        } catch (\Exception $e) {
            return new Result(
                $this,
                Result::EXITCODE_ERROR,
                'OpenApi contract rebuild failed : ' . $e->getMessage()
            );
        }

        return new Result($this, Result::EXITCODE_OK);
    }
}
