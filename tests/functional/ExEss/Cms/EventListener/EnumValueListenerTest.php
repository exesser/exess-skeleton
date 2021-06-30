<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\EventListener;

use Doctrine\ORM\EntityManager;
use ExEss\Bundle\CmsBundle\Doctrine\Type\FlowFieldType;
use ExEss\Bundle\CmsBundle\Entity\FlowField;
use Helper\Testcase\FunctionalTestCase;

/**
 * @see EnumValueListener
 */
class EnumValueListenerTest extends FunctionalTestCase
{
    private EntityManager $em;

    private string $accepted;

    public function _before(): void
    {
        $this->em = $this->tester->grabService('doctrine.orm.entity_manager');
        $this->accepted = \json_encode(\array_keys(FlowFieldType::getValues()));
    }

    public function testInvalidValueOnPersist(): void
    {
        // Given
        $field = new FlowField();
        $field->setType("foo");

        // Then
        $this->tester->expectThrowable(
            new \RuntimeException(
                'Property "type" on entity "' . FlowField::class . '" with type "' . FlowFieldType::class
                . '" contained an invalid value "foo" but allowed values are: ' . $this->accepted
            ),
            function () use ($field): void {
                // When
                $this->em->persist($field);
            }
        );
    }

    public function testInvalidValueOnFlush(): void
    {
        // Given
        $field = new FlowField();
        $field->setType(FlowFieldType::FIELD_TYPE_DATE);

        // persist with valid value
        $this->em->persist($field);

        // afterwards, change to an invalid value
        $field->setType("foo");

        // persist again, just to prove the listeners only trigger on the initial persist
        $this->em->persist($field);

        // Then
        $this->tester->expectThrowable(
            /**
             * thrown by @see \ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType::convertToDatabaseValue()
             */
            new \InvalidArgumentException("foo is an invalid option for 'enum_flow_field_type"),
            function () use ($field): void {
                // When
                $this->em->flush();
            }
        );
    }
}
