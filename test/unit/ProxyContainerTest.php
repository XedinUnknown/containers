<?php

namespace Dhii\Container\UnitTest;

use Dhii\Container\ProxyContainer as TestSubject;
use Dhii\Container\TestHelpers\ComponentMockeryTrait;
use Dhii\Data\Container\ContainerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ProxyContainerTest extends TestCase
{
    use ComponentMockeryTrait;

    /**
     * @return TestSubject&MockObject
     */
    protected function createInstance(): TestSubject
    {
        $mock = $this->getMockBuilder(TestSubject::class)
            ->setMethods(null)
            ->getMock();

        return $mock;
    }

    /**
     * @param array $services
     *
     * @return ContainerInterface
     */
//    protected function createContainer(array $services): ContainerInterface
//    {
//        $mock = $this->getMockBuilder(ContainerInterface::class)
//            ->setMethods(['get', 'has'])
//            ->getMock();
//
//        $mock->method('has')
//            ->will($this->returnCallback(function ($key) use ($services) {
//                return array_key_exists($key, $services);
//            }));
//        $mock->method('get')
//            ->will($this->returnCallback(function ($key) use ($services) {
//                if (!array_key_exists($key, $services)) {
//                    throw new NotFoundException();
//                }
//
//                return $services[$key];
//            }));
//
//        return $mock;
//    }

    /**
     * Tests that calls and return values are being proxied correctly.
     */
    public function testProxy()
    {
        {
            $nonExistingKey = uniqid('not-here');
            $key = uniqid('key');
            $value = uniqid('value');
            $services = [
                $key => $value,
            ];
            $container = $this->createContainer($services);
            $subject = $this->createInstance();
        }

        {
            $subject->setInnerContainer($container);
            $this->assertTrue($subject->has($key));
            $this->assertEquals($value, $subject->get($key));

            $this->assertFalse($subject->has($nonExistingKey));
            $this->expectException(NotFoundExceptionInterface::class);
            $subject->get($nonExistingKey);
        }
    }

    /**
     * Tests that the subject reacts correctly when the inner container is missing.
     */
    public function testHasMissingContainer()
    {
        {
            $subject = $this->createInstance();
        }

        {
            $this->expectException(ContainerExceptionInterface::class);
            $subject->has(uniqid('not-there'));
        }
    }

    /**
     * Tests that the subject reacts correctly when the inner container is missing.
     */
    public function testGetMissingContainer()
    {
        {
            $subject = $this->createInstance();
        }

        {
            $this->expectException(ContainerExceptionInterface::class);
            $subject->get(uniqid('not-there'));
        }
    }
}
