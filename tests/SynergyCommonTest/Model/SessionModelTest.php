<?php

namespace SessionModelTest\Model;

use Doctrine\ORM\AbstractQuery;
use SynergyCommon\Member\Entity\Session;
use SynergyCommon\Model\AbstractModelFactory;
use SynergyCommon\Model\SessionModel;
use SynergyCommonTest\Bootstrap;
use Zend\Paginator\Paginator;

/**
 * Class SessionModelTest
 * @package SessionModelTest\Model
 */
class SessionModelTest extends \PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    private $alias = 'synergycommon\model\session';

    public function setUp()
    {

        $this->serviceManager = Bootstrap::getServicemanager();
    }

    public function testModelInstance()
    {
        $model = $this->serviceManager->get($this->alias);
        $this->assertInstanceOf(SessionModel::class, $model);
    }

    public function testModelCanBeCreatedWithFactory()
    {
        $factory = new AbstractModelFactory();

        $this->assertTrue($factory->canCreate($this->serviceManager, $this->alias));

        $model = $factory->__invoke($this->serviceManager, $this->alias);
        $this->assertInstanceOf(SessionModel::class, $model);
    }

    public function testPersistSession()
    {
        /** @var Session $found */
        /** @var SessionModel $model */
        $name      = 'phpunit-session';
        $data      = [
            'name'     => $name,
            'data'     => serialize(['id' => 1]),
            'modified' => time(),
            'lifetime' => 0,
            'expireBy' => time()
        ];
        $model     = $this->serviceManager->get($this->alias);
        $session   = new Session();
        $sessionId = hash('crc32', time() . 'session');

        $session->fromArray($data);

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

        $result = $model->findOneTranslatedBy(['name' => $name, 'sessionId' => $sessionId]);
        $this->assertInstanceOf(Session::class, $result);
    }

    public function testGetNullPagerWithFind()
    {
        $model = $this->serviceManager->get('synergycommon\model\licence');
        $pager = $model->findItemsByCriteria(['id' => 1], 1, AbstractQuery::HYDRATE_OBJECT, true);
        $this->assertNull($pager);
    }

    public function testGetPagerWithFind()
    {
        $model = $this->serviceManager->get('synergycommon\model\userGroup');
        $pager = $model->findItemsByCriteria(['id' => 1], 1, AbstractQuery::HYDRATE_OBJECT, true);
        $this->assertInstanceOf(Paginator::class, $pager);
    }
}
