<?php
namespace ExEss\Cms\Api\V8_Custom\Service\User;

use ExEss\Cms\Http\Cookies;

class RedirectPathCookieService
{
    public const FULL_PATH_COOKIE = 'FULL_INTENDED_PATH';

    private Cookies $cookies;

    public function __construct(Cookies $cookies)
    {
        $this->cookies = $cookies;
    }

    public function setCookie(string $redirectPath): void
    {
        $this->cookies->set(
            self::FULL_PATH_COOKIE,
            [
                'value' => $redirectPath,
                'expires' => (new \DateTime('+5 minutes'))->format(\DATE_COOKIE)
            ]
        );
    }

    public function get(): string
    {
        $responseCookie = Cookies::parseHeader(($header = \current(\array_filter(
            $this->cookies->toHeaders(),
            function (string $header) {
                return (\substr($header, 0, \strlen(self::FULL_PATH_COOKIE)) === self::FULL_PATH_COOKIE);
            }
        ))) ? $header: '')[self::FULL_PATH_COOKIE] ?? '';
        return $this->cookies->get(self::FULL_PATH_COOKIE, $responseCookie);
    }

    public function invalidate(): void
    {
        $this->cookies->set(
            self::FULL_PATH_COOKIE,
            [
                'value' => '',
                'expires' => (new \DateTime('-1 year'))->format(\DATE_COOKIE),
            ]
        );
    }
}
