<?php
namespace ExEss\Cms\Api\V8_Custom\Params;

use ExEss\Cms\Api\V8_Custom\Params\Validator\ValidatorFactory;
use ExEss\Cms\Dashboard\Model\Grid;
use ExEss\Cms\Service\GridService;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ListRowbarParams extends AbstractParams implements \JsonSerializable
{
    public const DEFAULT_GRID_KEY = 'action-bar';

    private GridService $gridService;

    public function __construct(ValidatorFactory $validatorFactory, GridService $gridService)
    {
        parent::__construct($validatorFactory);
        $this->gridService = $gridService;
    }

    public function getListKey(): string
    {
        return $this->arguments['listKey'];
    }

    public function getRecordId(): string
    {
        return $this->arguments['recordId'];
    }

    public function getGridKey(): string
    {
        return $this->arguments['gridKey'];
    }

    public function getActionData(): array
    {
        return $this->arguments['actionData'];
    }

    public function getGrid(): Grid
    {
        return $this->arguments['grid'];
    }

    /**
     * Method to configure the options passed through this class.
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['listKey', 'recordId', 'gridKey']);

        $resolver
            ->setAllowedTypes('listKey', ['string'])
            ->setAllowedValues('listKey', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
                new Assert\Regex(['pattern' => self::REGEX_KEY]),
            ]));

        // ! this is not always a GUID !
        $resolver
            ->setAllowedTypes('recordId', ['string'])
            ->setAllowedValues('recordId', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
            ]));

        $resolver
            ->setDefault('actionData', [])
            ->setAllowedTypes('actionData', ['array']);

        $resolver
            ->setDefault('gridKey', self::DEFAULT_GRID_KEY)
            ->setAllowedTypes('gridKey', ['string'])
            ->setAllowedValues('gridKey', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
                new Assert\Regex(['pattern' => self::REGEX_KEY]),
            ]));

        $resolver
            ->setDefault('grid', new Grid([]))
            ->setNormalizer(
                'grid',
                function (Options $options, $grid) {
                    if ($options['gridKey'] !== self::DEFAULT_GRID_KEY) {
                        return $this->gridService->getGridByKey(
                            $options['gridKey'],
                            ['recordId' => $options['recordId']] + $options['actionData']
                        );
                    }

                    $gridOptions = [
                        'recordType' => $options['listKey'],
                        'recordId' => $options['recordId'],
                        'id' => $options['recordId'],
                        'gridKey' => $options['gridKey']
                    ];

                    if (!empty($options['actionData'])) {
                        $gridOptions['actionData'] = $options['actionData'];
                    }

                    return new Grid([
                        'columns' => [[
                            'size' => '1-1',
                            'hasMargin' => false,
                            'cssClasses' => ['row__actions'],
                            'rows' => [[
                                'size' => '1-1',
                                'type' => 'listRowActions',
                                'options' => $gridOptions
                            ]]
                        ]]
                    ]);
                }
            );
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return ['grid' => $this->getGrid()];
    }
}
