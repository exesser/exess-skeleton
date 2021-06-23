<?php

use ExEss\Cms\Api\V8_Custom\Controller\Middleware\CachedResponseHandler;
use ExEss\Cms\Api\V8_Custom\Controller\ParamConverter\ParamObjectConverterFactory;
use ExEss\Cms\Api\V8_Custom\Params\FlowUpdateParams;
use ExEss\Cms\Api\V8_Custom\Params\SelectWithSearchParams;

/** @var \ExEss\Cms\App $app */
/** @var ParamObjectConverterFactory $paramsFactory */
$paramsFactory = $app->getContainer()->get(ParamObjectConverterFactory::class);

$app->group('/Api', function () use ($app, $paramsFactory): void {

    $app->group('/V8_Custom', function () use ($app, $paramsFactory): void {
        $controllerCache = $app->getContainer()->get(CachedResponseHandler::class);

        /**
         * The POST can be called to get an update / perform a save
         * @see \ExEss\Cms\Api\V8_Custom\Controller\FlowController::getFlowUpdate()
         */
        $app
            ->post('/Flow/{flow_name}', 'ExEss\Cms\Api\V8_Custom\Controller\FlowController:getFlowUpdate')
            ->add($paramsFactory->create(FlowUpdateParams::class))
        ;
        /**
         * Retrieve a flow record POPULATED
         * The GET method gives the initial situation
         * The POST can be called to get an update / perform a save
         * @see \ExEss\Cms\Api\V8_Custom\Controller\FlowController::getFlowUpdate()
         */
        $app
            ->post(
                '/Flow/{record_type}/{flow_name}/{record_id}[/{flowAction}]',
                'ExEss\Cms\Api\V8_Custom\Controller\FlowController:getFlowUpdate'
            )
            ->add($paramsFactory->create(FlowUpdateParams::class))
        ;
        /**
         * @see \ExEss\Cms\Api\V8_Custom\Controller\FlowController::getFlowUpdate()
         */
        $app
            ->map(
                ['GET', 'POST'],
                '/Flow/{flow_name}/{record_id}',
                'ExEss\Cms\Api\V8_Custom\Controller\FlowController:getFlowUpdate'
            )
            ->add($paramsFactory->create(FlowUpdateParams::class))
        ;

        $app
            ->post(
                '/SelectWithSearch/{selectWithSearchName}',
                'ExEss\Cms\Api\V8_Custom\Controller\SelectWithSearchController:getSelectOptions'
            )
            ->add($paramsFactory->create(SelectWithSearchParams::class));

        /**
         * CRUD
         */
        $app->get(
            '/CRUD/records-information',
            \ExEss\Cms\Api\V8_Custom\Controller\CrudController::class . ':getRecordsInformation'
        );
    });
});
