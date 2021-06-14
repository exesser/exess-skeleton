<?php declare(strict_types=1);

namespace Test\Unit\ExEss\Cms\Component\Session;

use ExEss\Cms\Component\Session\Headers;
use ExEss\Cms\Component\Session\User\UserInterface;
use Ramsey\Uuid\Uuid;
use ExEss\Cms\Test\Testcase\UnitTestCase;

class HeadersTest extends UnitTestCase
{
    public function _after(): void
    {
        Headers::create()->clear();
    }

    public function testDefaultHeaders(): void
    {
        // When
        $headers = Headers::create();

        // Then
        $this->tester->assertTrue(Uuid::isValid($headers->all()[Headers::LOG_ID]));
        $this->tester->assertEquals('', $headers->all()[Headers::LOG_DESCRIPTION]);
        $this->tester->assertEquals(Headers::DEFAULT_COMPONENT, $headers->all()[Headers::LOG_COMPONENT]);
        $this->tester->assertEquals('__MISSING_USER_HEADER__', $headers->all()[Headers::USER_LOG_KEY]);
    }

    public function testHeadersComponent(): void
    {
        // When
        $headers = Headers::create('SHARED');

        // Then
        $this->tester->assertEquals('SHARED', $headers->all()[Headers::LOG_COMPONENT]);
    }

    public function testDefaultHeadersWithUser(): void
    {
        // Given
        $userId = Uuid::uuid4();
        $username = 'MostAwesomeDeveloper a.k.a Kristofvc';
        $user = \Mockery::mock(UserInterface::class);
        $user->shouldReceive('getId')->once()->andReturn($userId);
        $user->shouldReceive('getUsername')->once()->andReturn($username);

        // When
        $headers = Headers::create();
        $headers->setUser($user);

        // Then
        $this->tester->assertEquals($userId, $headers->all()[Headers::USER_LOG_ID_KEY]);
        $this->tester->assertEquals($username, $headers->all()[Headers::USER_LOG_KEY]);
    }

    public function testServerHeaders(): void
    {
        // Given
        Headers::setServerHeader(Headers::LOG_DESCRIPTION, 'Most awesome description');

        // When
        $headers = Headers::create();

        // Then
        $this->tester->assertEquals('Most awesome description', $headers->all()[Headers::LOG_DESCRIPTION]);
    }
}
