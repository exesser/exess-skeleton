<?php

namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Events;

/**
 * A dictionary of events send through external api's
 */
class ExternalApiEvents
{
    /**
     * Prepare Request to an external api
     */
    public const PREPARE_REQUEST = 'external_api.prepare_request';

    /**
     * Send Prepared Request external api
     */
    public const SEND_PREPARED_REQUEST = 'external_api.send_prepared_request';

    /**
     * Request to an external api
     */
    public const REQUEST = 'external_api.request';

    /**
     * Response of an external api
     */
    public const RESPONSE = 'external_api.response';

    /**
     * exception on consuming an external api
     */
    public const EXCEPTION = 'external_api.exception';
}
