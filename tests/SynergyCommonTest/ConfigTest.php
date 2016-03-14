<?php

namespace SynergyCommonTest;

use SynergyCommon\Client\ClientOptions;
use SynergyCommon\CommonSiteSettings;
use Zend\Filter\StringTrim;
use Zend\InputFilter\InputFilter;
use Zend\Server\Reflection\ReflectionParameter;

/**
 * Class run generic tests on entites. Verifies simple getters/setters
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceManager;
    protected $stack = [];

    public function setUp()
    {
        $this->stack = [
            CommonSiteSettings::class,
            ClientOptions::class
        ];

        $this->serviceManager = Bootstrap::getServicemanager();
    }

    public function testGenericConfigObjects()
    {
        foreach ($this->stack as $declaredClass) {
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
                                $class->$methodName([]);
                                $this->assertTrue(true);
                            }
                        }
                    }

                    if (preg_match('/^(get|is)/', $methodName)) {
                        $this->assertTrue(true);
                        $class->$methodName('test');
                    }
                }
            }
        }
    }
}
