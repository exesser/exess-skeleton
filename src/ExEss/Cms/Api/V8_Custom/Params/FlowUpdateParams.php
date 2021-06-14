<?php
namespace ExEss\Cms\Api\V8_Custom\Params;

use Doctrine\ORM\EntityManager;
use ExEss\Cms\Doctrine\Type\FlowFieldType;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Api\V8_Custom\Params\Validator\ValidatorFactory;
use ExEss\Cms\Cleaner\HtmlCleaner;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Exception\NotFoundException;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response\Model;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class FlowUpdateParams extends AbstractParams
{
    private HtmlCleaner $htmlCleaner;

    private EntityManager $em;

    public function __construct(
        ValidatorFactory $validatorFactory,
        HtmlCleaner $htmlCleaner,
        EntityManager $em
    ) {
        parent::__construct($validatorFactory);
        $this->htmlCleaner = $htmlCleaner;
        $this->em = $em;
    }

    public function getFlow(): Flow
    {
        return $this->arguments['flow_name'];
    }

    public function getGuidanceAction(): ?string
    {
        return $this->arguments['flowAction'];
    }

    public function getRoute(): ?array
    {
        return $this->arguments['route'];
    }

    public function getModel(): array
    {
        return $this->arguments['model'];
    }

    public function getParentModel(): ?Model
    {
        return $this->arguments['parentModel'];
    }

    public function getAction(): FlowAction
    {
        return $this->arguments['action'];
    }

    public function getParams(): array
    {
        return $this->arguments['params'];
    }

    public function getRecordId(): ?string
    {
        return $this->arguments['record_id'];
    }

    public function getRecordType(): ?string
    {
        return $this->arguments['record_type'];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('flow_name')
            ->setAllowedTypes('flow_name', ['string'])
            ->setAllowedValues('flow_name', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
            ]))
            ->setNormalizer('flow_name', function (Options $options, string $value): Flow {
                return $this->em->getRepository(Flow::class)->get($value);
            })
        ;
        $resolver
            ->setDefined('flowAction')
            ->setAllowedTypes('flowAction', ['null', 'string'])
            ->setDefault('flowAction', null)
            ->setNormalizer('flowAction', function (Options $options, ?string $value): ?string {
                if (
                    !$value
                    && !empty($options['model'][Dwp::REPLACE_FLOW_ACTION])
                    && ($options['action']['event'] ?? null) === FlowAction::EVENT_INIT
                ) {
                    $value = $options['model'][Dwp::REPLACE_FLOW_ACTION];
                }

                return empty($value) ? null : $value;
            })
        ;
        $resolver
            ->setDefined('route')
            ->setAllowedTypes('route', ['null', 'array'])
            ->setDefault('route', null)
        ;
        $resolver
            ->setDefined('model')
            ->setAllowedTypes('model', ['array'])
            ->setDefault('model', [])
            ->setNormalizer('model', function (Options $options, array $model) {
                $flow = $options['flow_name'];
                if (!$flow instanceof Flow) {
                    throw new \InvalidArgumentException("Expected a flow to be present");
                }

                foreach ($model as $name => $value) {
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

                return $model;
            })
        ;
        $resolver
            ->setDefined('parentModel')
            ->setAllowedTypes('parentModel', ['null', 'array'])
            ->setDefault('parentModel', null)
            ->setNormalizer('parentModel', function (Options $options, ?array $value): ?Model {
                return $value ? new Model($value) : null;
            })
        ;
        $resolver
            ->setDefined('action')
            ->setAllowedTypes('action', ['array', 'object'])
            ->setDefault('action', ['event' => FlowAction::EVENT_INIT])
            ->setNormalizer('action', function (Options $options, $value): FlowAction {
                $recordIds = [];
                if (!empty($options['record_id'])) {
                    $recordIds = $options['record_id'];
                }
                if (!empty($options['model']['id'])) {
                    $id = $options['model']['id'];
                    if (\is_array($id)) {
                        $id = \current($id);
                        $recordIds = \current($id)['key'] ?? null;
                    } else {
                        $recordIds = $id;
                    }
                }
                if (!\is_array($recordIds)) {
                    $recordIds = [$recordIds];
                }

                return new FlowAction(\array_merge((array) $value, ['recordIds' => $recordIds]));
            })
        ;
        $resolver
            ->setDefined('params')
            ->setAllowedTypes('params', ['array'])
            ->setDefault('params', [])
        ;
        $resolver
            ->setDefined('record_id')
            ->setAllowedTypes('record_id', ['null', 'string', 'int'])
            ->setDefault('record_id', null)
            ->setNormalizer('record_id', function (Options $options, $value): ?string {
                $returnValue = $value ?? $options['model'][Dwp::REPLACE_RECORD_ID] ?? null;
                return $returnValue === null ? $returnValue : (string) $returnValue;
            })
        ;
        $resolver
            ->setDefined('record_type')
            ->setAllowedTypes('record_type', ['null', 'string'])
            ->setDefault('record_type', null)
            ->setNormalizer('record_type', function (Options $options, ?string $value): ?string {
                return $value ?? $options['model'][Dwp::REPLACE_RECORD_TYPE] ?? null;
            })
        ;
        // this is sent but unused, ignore it
        $resolver
            ->setDefined('progress')
        ;
    }
}
