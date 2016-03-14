<?php

namespace SynergyCommonTest\Doctrine;

use SynergyCommon\Session\SaveHandler\DoctrineSaveHandler;
use SynergyCommonTest\Bootstrap;

/**
 * Class DoctrineSaveHandlerTest
 * @package SynergyCommonTest\Doctrine
 */
class DoctrineSaveHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceManager;

    public function setUp()
    {

        $this->serviceManager = Bootstrap::getServicemanager();
    }

    public function testSaveHandler()
    {
        $model     = $this->serviceManager->get('common\model\session');
        $handler   = new DoctrineSaveHandler($model, 0);
        $sessionId = hash('crc32', time() . 'test');
        $handler->open('', 'test');
        $saved     = $handler->write($sessionId, serialize(['name' => 'title']));
        $destroyed = $handler->destroy($sessionId);
        $handler->close();

        $this->assertTrue($destroyed);
        $this->assertTrue($saved);

        $sessionId = hash('crc32', time() . 'title');
        $handler->write($sessionId, serialize(['name' => 'title']));
        $cleared = $handler->gc(0);
        $this->assertTrue($cleared);
    }
}
