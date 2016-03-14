<?php
namespace SynergyCommonTest;

use SynergyCommon\Controller\BaseRestfulController;
use Zend\View\Model\ViewModel;

/**
 * Class SampleController
 * @package SynergyCommonTest\Controller
 */
class SampleController extends BaseRestfulController
{

    protected $_serviceKey = 'synergycommon\service\session';

    public function indexAction()
    {
        $view = new ViewModel([]);
        $view->setTemplate('sample-view.phtml');

        return $view;
    }
}
