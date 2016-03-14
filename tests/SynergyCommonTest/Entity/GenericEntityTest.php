<?php

namespace SynergyCommonTest\Entity;

use SynergyCommon\Entity\AbstractEntity;
use SynergyCommon\Entity\AbstractEntityFactory;
use SynergyCommon\Entity\BaseEntity;
use SynergyCommon\Entity\BaseLicence;
use SynergyCommon\Entity\BasePage;
use SynergyCommon\Entity\BaseRole;
use SynergyCommon\Entity\BaseSite;
use SynergyCommon\Entity\BaseUser;
use SynergyCommonTest\Bootstrap;
use Zend\Filter\StringTrim;
use Zend\InputFilter\InputFilter;
use Zend\Server\Reflection\ReflectionParameter;

/**
 * Class run generic tests on entites. Verifies simple getters/setters
 */
class GenericEntityTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceManager;
    protected $stack = [];

    public function setUp()
    {
        $this->stack = [
            BaseEntity::class,
            BasePage::class,
            BaseRole::class,
            BaseUser::class,
            BaseLicence::class,
            BaseSite::class,
        ];

        $this->serviceManager = Bootstrap::getServicemanager();
    }

    /**
     * @expectedException \SynergyCommon\Exception\InvalidEntityException
     */
    public function testAbstractEntityFactoryException()
    {
        $factory = new AbstractEntityFactory();
        $factory->createServiceWithName($this->serviceManager, 'synergy\entity\page', 'synergy\entity\page');
    }

    public function testCannotCreateFactory()
    {
        $factory = new AbstractEntityFactory();
        $result  = $factory->canCreateServiceWithName($this->serviceManager, 'synergy\entity', 'synergy\entity');
        $this->assertFalse($result);
    }

    public function testAbstractEntityFactory()
    {
        $entity = $this->serviceManager->get('synergycommon\entity\basePage');
        $this->assertInstanceOf(AbstractEntity::class, $entity);
    }

    /**
     * @group entity
     */
    public function testEntities()
    {
        foreach ($this->stack as $declaredClass) {
            $split = explode('\\', $declaredClass);
            if (in_array('Entity', $split)) {
                $reflectionClass = new \ReflectionClass($declaredClass);

                if ($reflectionClass->IsInstantiable()) {
                    $class      = new $declaredClass;
                    $methods    = $reflectionClass->getMethods();
                    $methodList = [];
                    /** @var \ReflectionMethod $method */
                    foreach ($methods as $method) {
                        $methodName   = $method->getName();
                        $methodParams = $method->getParameters();

                        if (preg_match('/^set/', $methodName)) {
                            $attr               = lcfirst(substr($methodName, 3));
                            $methodList [$attr] = 'testdata ';
                            if (count($methodParams) === 1) {
                                /** @var \ReflectionParameter $param */
                                $param = current($methodParams);
                                $param = new ReflectionParameter($param);

                                if ($param->allowsNull()) {
                                    $class->$methodName(null);
                                    $this->assertTrue(true);
                                }
                            }
                        }

                        if (preg_match('/^(get|is)/', $methodName)) {
                            $this->assertTrue(true);
                            $class->$methodName('test');
                        }
                    }

                    /** @var BaseEntity $entity */
                    $entity = new $declaredClass();
                    $entity->exchangeArray($methodList);
                    $arrayList = $entity->toArray();
                    $this->assertTrue(is_array($arrayList));

                    if (method_exists($entity, 'ensureNoLineBreaks')) {
                        $entity->ensureNoLineBreaks();
                    }
                }
            }
        }
    }

    public function testMagicMethods()
    {
        $entity = new BasePage();
        $entity->getInputFilter()->add(
            [
                'name'    => 'slug',
                'filters' => [
                    ['name' => StringTrim::class]
                ]
            ]
        );

        $data = [
            'lft'   => 1,
            'rgt'   => 2,
            'level' => 1,
            'slug'  => 'page'
        ];

        $entity->fromArray($data);

        foreach ($data as $key => $value) {
            $this->assertSame($value, $entity->$key);
        }
    }

    /**
     * @param $data
     * @param $expected
     * @dataProvider entityDataProvider
     */
    public function testToString($data, $expected)
    {
        $entity = new BasePage();
        $entity->fromArray($data);
        $this->assertSame($expected, (string)$entity);
    }

    /**
     * @param $test
     * @param $result
     * @dataProvider testDataProvider
     */
    public function testRemoveWhiteSpace($test, $result)
    {
        $entity = new BasePage();
        $this->assertSame($result, $entity->removeWhiteSpace($test));
    }

    public function testDataProvider()
    {
        return [
            ['test' . PHP_EOL, 'test'],
            ["\r\ntest", 'test'],
            ["test \n school", 'test school'],
        ];
    }

    public function entityDataProvider()
    {
        return [
            [['slug' => 'page'], 'page'],
            [['title' => 'test-title'], 'test-title'],
        ];
    }
}
