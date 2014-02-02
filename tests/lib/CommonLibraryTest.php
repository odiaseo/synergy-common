<?php
namespace SynergyCommonTest\Lib;

use SynergyCommonTest\Bootstrap;

class CommonLibraryTest
    extends \PHPUnit_Framework_TestCase
{
    /** @var \Zend\ServiceManager\ServiceManager */
    protected $_serviceManager;

    public function setUp()
    {
        parent::setUp();
        $this->_serviceManager = Bootstrap::getServiceManager();
    }

    public function testLoggerFactory()
    {
        /** @var $logger  \SynergyCommon\Util\ErrorHandler */
        $logger = $this->_serviceManager->get('logger');

        $this->assertInstanceOf('SynergyCommon\Util\ErrorHandler', $logger);
        $adapter = $logger->getLogger();

        if (class_exists('Monolog\Logger')) {
            $this->assertInstanceOf('Monolog\Logger', $adapter);
        } else {
            $this->assertInstanceOf('Zend\Log\Logger', $adapter);
        }
    }

    public function testSessionManager()
    {
        $service = $this->_serviceManager->get('session_manager');
        $this->assertInstanceOf('Zend\Session\SessionManager', $service);

    }

    public function testApiService()
    {
        $service = $this->_serviceManager->get('synergycommon\service\api');
        $this->assertInstanceOf('SynergyCommon\Service\BaseApiService', $service);
    }

    public function testFlshMessaager()
    {
        $service = $this->_serviceManager->get('viewhelpermanager')->get('flashMessages');
        $this->assertInstanceOf('SynergyCommon\View\Helper\FlashMessages', $service);
    }

    /**
     * @dataProvider provider
     *
     * @param $model
     */
    public function testModels($model)
    {
        $model = $this->_serviceManager->get('synergycommon\model\\' . $model);
        $this->assertInstanceOf('SynergyCommon\Model\AbstractModel', $model);
    }

    public function provider()
    {
        return array(
            array('licence'),
            array('site')
        );
    }
}