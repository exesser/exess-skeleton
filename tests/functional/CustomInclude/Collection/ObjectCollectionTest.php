<?php declare(strict_types=1);

namespace Test\Functional\CustomInclude\Collection;

use ExEss\Cms\Collection\ObjectCollection;
use ExEss\Cms\Entity\User;
use Helper\Testcase\FunctionalTestCase;

class ObjectCollectionTest extends FunctionalTestCase
{
    public function testConstruct(): void
    {
        new ObjectCollection(User::class);
    }

    public function testPersistence(): void
    {
        $collection = new ObjectCollection(User::class);

        // create an object, set a property and add it to the collection
        $myName = 'my name';
        $object = new User();
        $object->setUserName($myName);

        $collection[0] = $object;

        $this->tester->assertEquals($collection[0]->getUserName(), $myName);

        // get an element from the collection with current() and change a value
        $mySecondName = 'my-second-name';
        $objectFromCollectionWithCurrent = $collection->current();
        $objectFromCollectionWithCurrent->setUserName($mySecondName);

        $this->tester->assertEquals($collection[0]->getUserName(), $mySecondName);

        // get an element from the collection by array index, and change a value
        $myThirdName = 'my-third-name';
        $objectFromCollectionWithIndex = $collection[0];
        $objectFromCollectionWithIndex->setUserName($myThirdName);

        $this->tester->assertEquals($collection[0]->getUserName(), $myThirdName);
    }

    protected function updateCollection(ObjectCollection $collection, string $value): void
    {
        $collection[0]->setUserName($value);
    }

    public function testOnlyAcceptsElementsOfType(): void
    {
        $this->tester->expectThrowable(
            new \InvalidArgumentException('Incorrect value argument, must be instance of ' . User::class),
            function (): void {
                $collection = new ObjectCollection(User::class);
                $collection[0] = new \stdClass();
            }
        );
    }

    public function testOnlyAcceptsElementsOfTypeWithOffsetSet(): void
    {
        $this->tester->expectThrowable(
            new \InvalidArgumentException('Incorrect value argument, must be instance of ' . User::class),
            function (): void {
                $collection = new ObjectCollection(User::class);
                $collection->offsetSet(0, new \stdClass());
            }
        );
    }
}
