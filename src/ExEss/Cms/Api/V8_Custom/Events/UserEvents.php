<?php

namespace ExEss\Cms\Api\V8_Custom\Events;

/**
 * A dictionary of events send through external api's
 */
class UserEvents
{
    /**
     * Thrown when a new user is created via the login request api-call
     */
    public const LOGIN_REQUESTED = 'user.login_request';

    /**
     * Thrown when a user successfully logs into the api
     */
    public const AFTER_LOGIN = 'user.after_login';
}
