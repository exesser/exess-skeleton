<?php

use ExEss\Cms\Api\V8_Custom\Controller\Middleware\CachedResponseHandler;
use ExEss\Cms\Api\V8_Custom\Controller\ParamConverter\ParamObjectConverterFactory;
use ExEss\Cms\Api\V8_Custom\Params\ActionParams;
use ExEss\Cms\Api\V8_Custom\Params\ChangeLocaleParams;
use ExEss\Cms\Api\V8_Custom\Params\FlowUpdateParams;
use ExEss\Cms\Api\V8_Custom\Params\GetDashboardParams;
use ExEss\Cms\Api\V8_Custom\Params\GetFilterParams;
use ExEss\Cms\Api\V8_Custom\Params\ListRowbarActionParams;
use ExEss\Cms\Api\V8_Custom\Params\ListRowbarParams;
use ExEss\Cms\Api\V8_Custom\Params\SelectWithSearchParams;

/** @var \ExEss\Cms\App $app */
/** @var ParamObjectConverterFactory $paramsFactory */
$paramsFactory = $app->getContainer()->get(ParamObjectConverterFactory::class);

$app->group('/Api', function () use ($app, $paramsFactory): void {

    $app->group('/V8_Custom', function () use ($app, $paramsFactory): void {
        $controllerCache = $app->getContainer()->get(CachedResponseHandler::class);

        /**
         * Retrieve the command of an action
         * @see \ExEss\Cms\Api\V8_Custom\Controller\FetchActionController::__invoke()
         */
        $app
            ->post(
                '/Action/{action}',
                ExEss\Cms\Api\V8_Custom\Controller\FetchActionController::class
            )
            ->add($paramsFactory->create(ActionParams::class))
        ;

        /**
         * Retrieve a dashboard.
         *
         * this route is currently in use for the lead and account dashboards in the DWP and this was before
         * menus were implemented which give back the ID as well.
         *
         * @deprecated please use "Detail page of a dashboard."
         * @see \ExEss\Cms\Api\V8_Custom\Controller\DashboardController::getDashboard()
         */
        $app
            ->get('/Dashboard/{dash_name}', 'ExEss\Cms\Api\V8_Custom\Controller\DashboardController:getDashboard')
            ->add($paramsFactory->create(GetDashboardParams::class))
            ->add($controllerCache)
        ;

        $app
            ->get(
                '/Dashboard/{dash_name}/{recordId}',
                'ExEss\Cms\Api\V8_Custom\Controller\DashboardController:getDashboard'
            )
            ->add($paramsFactory->create(GetDashboardParams::class))
        ;

        $app
            ->post(
                '/user/change-locale/{locale}',
                \ExEss\Cms\Api\V8_Custom\Controller\User\ChangeLocaleController::class
            )
            ->add($paramsFactory->create(ChangeLocaleParams::class))
        ;

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
            ->get('/Filter/{filterKey}/{list}', 'ExEss\Cms\Api\V8_Custom\Controller\FilterController:getFilter')
            ->add($paramsFactory->create(GetFilterParams::class))
            ->add($controllerCache)
        ;

        $app
            ->post(
                '/ListExtraRowContent/{gridKey}/{listKey}/{recordId}',
                'ExEss\Cms\Api\V8_Custom\Controller\ListRowbarController:getListRowBar'
            )
            ->add($paramsFactory->create(ListRowbarParams::class));
        /**
         * @see \ExEss\Cms\Api\V8_Custom\Controller\ListRowbarController::getListRowActions()
         */
        $app
            ->post(
                '/ListRowAction/{listKey}/{recordId}',
                'ExEss\Cms\Api\V8_Custom\Controller\ListRowbarController:getListRowActions'
            )
            ->add($paramsFactory->create(ListRowbarActionParams::class));

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
