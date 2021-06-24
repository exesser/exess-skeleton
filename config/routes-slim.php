<?php

use ExEss\Cms\Api\V8_Custom\Controller\ParamConverter\ParamObjectConverterFactory;

/** @var \ExEss\Cms\App $app */
/** @var ParamObjectConverterFactory $paramsFactory */
$paramsFactory = $app->getContainer()->get(ParamObjectConverterFactory::class);

$app->group('/Api', function () use ($app, $paramsFactory): void {
    $app->group('/V8_Custom', function () use ($app, $paramsFactory): void {
    });
});
