<?php
namespace ExEss\Cms\Api\V8_Custom\Repository;

use ExEss\Bundle\CmsBundle\Component\ExpressionParser\ParserService;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\PathResolverOptions;
use ExEss\Cms\Servicemix\ExternalObjectHandler;

class ExternalObjectRepository
{
    private ExternalObjectHandler $externalObjectHandler;

    private ListHandler $listHandler;

    private ParserService $parserService;

    public function __construct(
        ExternalObjectHandler $externalObjectHandler,
        ListHandler $listHandler,
        ParserService $parserService
    ) {
        $this->externalObjectHandler = $externalObjectHandler;
        $this->listHandler = $listHandler;
        $this->parserService = $parserService;
    }

    /**
     * @param \ExEss\Cms\Base\Response\BaseResponse|object $linkedEntity
     * @return null|\ExEss\Cms\Base\Response\BaseResponse|\ExEss\Cms\Base\Response\BaseResponse[]
     */
    public function findRelated(
        object $linkedEntity,
        PathResolverOptions $options,
        string $handler,
        array $arguments,
        int $limit
    ) {
        if (empty($handler) || empty($arguments)) {
            // currently not supported
            return null;
        }

        if ($limit === 1 && $this->externalObjectHandler->combineCalls($handler)) {
            $postedData = [];

            foreach ($options->getAllBeans() as $oneBean) {
                foreach ($arguments as $key => $value) {
                    if (!isset($postedData[$key])) {
                        $postedData[$key] = [];
                    }
                    $postedData[$key][] = $this->parserService->parseListValue($oneBean, '%'.$value.'%');
                }
            }
            $postedData['recordId'] = $this->parserService->parseListValue($linkedEntity, '%id%');
        } else {
            $postedData = [];
            foreach ($arguments as $key => $value) {
                if (\is_array($value)) {
                    $postedData[$key] = $value;
                } elseif (\trim($value, '"') !== $value) {
                    $postedData[$key] = \trim($value, '"');
                } else {
                    $postedData[$key] = $this->parserService->parseListValue($linkedEntity, '%'.$value.'%');
                }
            }
        }

        if ($limit === 1) {
            $object = $this->externalObjectHandler->getObject($handler, $postedData);
        } else {
            $list = $this->listHandler->getList($handler, ['params' => $postedData]);
            $object = $list['list'];
        }

        return $object;
    }
}
