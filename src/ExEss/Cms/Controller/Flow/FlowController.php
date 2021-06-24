<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\Flow;

use ExEss\Cms\Cleaner\HtmlCleaner;
use ExEss\Cms\Doctrine\Type\FlowFieldType;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Exception\NotFoundException;
use ExEss\Cms\FLW_Flows\Action\Command;
use ExEss\Cms\FLW_Flows\Event\FlowEventDispatcher;
use ExEss\Cms\Http\SuccessResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FlowController
{
    private FlowEventDispatcher $flowEventDispatcher;

    private HtmlCleaner $htmlCleaner;

    public function __construct(
        FlowEventDispatcher $flowEventDispatcher,
        HtmlCleaner $htmlCleaner
    ) {
        $this->flowEventDispatcher = $flowEventDispatcher;
        $this->htmlCleaner = $htmlCleaner;
    }

    /**
     * @Route("/Api/flow/{key}", methods={"POST"})
     * @Route("/Api/flow/{key}/{recordId}", methods={"POST", "GET"})
     * @Route("/Api/flow/{recordType}/{key}/{recordId}", methods={"POST"})
     * @Route("/Api/flow/{recordType}/{key}/{recordId}/{guidanceAction}", methods={"POST"})
     * @ParamConverter("jsonBody")
     *
     * @param string|int|null $recordId
     */
    public function __invoke(
        Flow $flow,
        Body\FlowBody $jsonBody,
        ?string $recordType = null,
        $recordId = null,
        ?string $guidanceAction = null
    ): SuccessResponse {

        // @todo move this
        foreach ($jsonBody->getModel() as $name => $value) {
            try {
                $field = $flow->getField($name);

                if (\in_array(
                    $field->getType(),
                    [FlowFieldType::FIELD_TYPE_WYSIWYG, FlowFieldType::FIELD_TYPE_TEXTAREA],
                    true
                )) {
                    $model[$name] = $this->htmlCleaner->cleanHtml($value);
                }
            } catch (NotFoundException $e) {
                // nothing to do here
            }
        }

        // @todo move this or work it away, later in the process this field is used to get back to a recordId...
        $recordIds = [];
        $recordId = $recordId === null ? null : (string) $recordId;
        if (!empty($id = $jsonBody->getModel()['id'])) {
            if (\is_array($id)) {
                $id = \current($id);
                $recordIds = \current($id)['key'] ?? null;
            } else {
                $recordIds = $id;
            }
        } elseif (!empty($recordId)) {
            $recordIds = $recordId;
        }
        if (!\is_array($recordIds)) {
            $recordIds = [$recordIds];
        }
        $jsonBody->getAction()->setRecordIds($recordIds);

        $response = $this->flowEventDispatcher->dispatch(
            $flow->getKey(),
            $jsonBody->getAction(),
            $jsonBody->getModel(),
            $jsonBody->getParentModel(),
            $jsonBody->getParams(),
            $recordType,
            $guidanceAction,
            $jsonBody->getRoute()
        );

        $status = Response::HTTP_OK;
        if ($response instanceof Command && $response->getRelatedBeans()) {
            $status = Response::HTTP_CREATED;
        }

        return new SuccessResponse($response, $status);
    }
}
