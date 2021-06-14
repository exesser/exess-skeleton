<?php
namespace ExEss\Cms\Api\V8_Custom\Repository;

use ExEss\Cms\ListFunctions\HelperClasses\ListHelperFunctions;
use ExEss\Cms\Parser\PathResolverOptions;
use ExEss\Cms\Servicemix\ExternalObjectHandler;

class ExternalObjectRepository
{
    private ExternalObjectHandler $externalObjectHandler;

    private ListHandler $listHandler;

    private ListHelperFunctions $listHelperFunctions;

    public function __construct(
        ExternalObjectHandler $externalObjectHandler,
        ListHandler $listHandler,
        ListHelperFunctions $listHelperFunctions
    ) {
        $this->externalObjectHandler = $externalObjectHandler;
        $this->listHandler = $listHandler;
        $this->listHelperFunctions = $listHelperFunctions;
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
                    $postedData[$key][] = $this->listHelperFunctions->parseListValue($oneBean, '%'.$value.'%');
                }
            }
            $postedData['recordId'] = $this->listHelperFunctions->parseListValue($linkedEntity, '%id%');
        } else {
            $postedData = [];
            foreach ($arguments as $key => $value) {
                if (\is_array($value)) {
                    $postedData[$key] = $value;
                } elseif (\trim($value, '"') !== $value) {
                    $postedData[$key] = \trim($value, '"');
                } else {
                    $postedData[$key] = $this->listHelperFunctions->parseListValue($linkedEntity, '%'.$value.'%');
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
