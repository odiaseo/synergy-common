<?php

namespace SessionModelTest\Model;

use SynergyCommon\Member\Entity\Session;
use SynergyCommon\Model\SessionModel;
use SynergyCommonTest\Bootstrap;

/**
 * Class SessionModelTest
 * @package SessionModelTest\Model
 */
class SessionModelTest extends \PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {

        $this->serviceManager = Bootstrap::getServicemanager();
    }

    public function testModelInstance()
    {
        $model = $this->serviceManager->get('common\model\session');
        $this->assertInstanceOf(SessionModel::class, $model);
    }

    public function testPersist()
    {
        /** @var Session $found */
        /** @var SessionModel $model */
        $model     = $this->serviceManager->get('common\model\session');
        $session   = new Session();
        $sessionId = hash('crc32', time() . 'session');
        $name      = 'phpunit-session';
        $session->fromArray(
            [
                'name'     => $name,
                'data'     => serialize(['id' => 1]),
                'modified' => time(),
                'lifetime' => 0,
                'expireBy' => time()
            ]
        );

        $result = $model->save($session);
        $this->assertFalse($result);

        $session->setSessionId($sessionId);
        $result = $model->save($session);
        $this->assertInstanceOf(Session::class, $result);

        $result = $model->findItemsByCriteria(['name' => $name, 'sessionId' => $sessionId]);
        $this->assertTrue(is_array($result));
        $this->assertSame(1, count($result));
        $found = current($result);
        $this->assertInstanceOf(Session::class, $found);
        $this->assertSame($name, $found->getName());
        $this->assertSame($sessionId, $found->getSessionId());

        $found = $model->getSessionRecord($sessionId, $name);
        $this->assertInstanceOf(Session::class, $found);
        $this->assertSame($name, $found->getName());
    }
}
