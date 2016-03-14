<?php
namespace SynergyCommonTest\Controller;

use SynergyCommon\Controller\BaseRestfulController;
use SynergyCommonTest\Bootstrap;
use Zend\Http\Header\ContentType;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Router\Console\RouteMatch;
use Zend\View\Model\ViewModel;

/**
 * Class SampleControllerTest
 * @package SynergyCommonTest\Controller
 */
class SampleControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceManager;

    public function setUp()
    {

        $this->serviceManager = Bootstrap::getServicemanager();
    }

    public function testSendPayload()
    {
        /** @var BaseRestfulController $controller */
        $controller = $this->serviceManager->get('controllerLoader')->get('SynergyCommonTest\Sample');
        $this->assertInstanceOf(BaseRestfulController::class, $controller);
    }

    /**
     * @dataProvider getRestMethods
     */
    public function testRestMethods($method, $id, $data = [])
    {
        /** @var BaseRestfulController $controller */
        $controller = $this->serviceManager->get('controllerLoader')->get('SynergyCommonTest\Sample');

        $params = [
            'name' => 'api\home',
        ];

        if ($id) {
            $params['id'] = $id;
        }
        $request = new Request();
        $router  = new RouteMatch($params);
        $request->setMethod($method);
        $request->setRequestUri('/test/' . $id);

        if ($data) {
            $request->setContent(http_build_query($data));
        }

        $controller->getEvent()->setRouteMatch($router);
        $model = $controller->dispatch($request);

        $this->assertInstanceOf(ViewModel::class, $model);
    }

    public function testIndexAction()
    {
        /** @var BaseRestfulController $controller */
        $controller = $this->serviceManager->get('controllerLoader')->get('SynergyCommonTest\Sample');

        $params = [
            'name'   => 'test\page',
            'action' => 'index'
        ];

        $request = new Request();
        $router  = new RouteMatch($params);
        $request->setRequestUri('/test-page');

        $controller->getEvent()->setRouteMatch($router);
        $model = $controller->dispatch($request);

        $this->assertInstanceOf(ViewModel::class, $model);
    }

    public function getRestMethods()
    {
        return [
            ['GET', ''],
            ['GET', '1'],
            ['POST', ''],
            ['POST', '1'],
            ['PUT', 1, ['id' => 1]],
            ['DELETE', ''],
            ['DELETE', '1'],
        ];
    }
}
