<?php
namespace SynergyCommon\Controller;

use SynergyCommon\Service\ServiceLocatorAwareTrait;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class BaseRestfulController
 * @method sendPayload($load)
 * @package SynergyCommon\Controller
 */
abstract class BaseRestfulController extends AbstractRestfulController
{
    use ServiceLocatorAwareTrait;
    /**
     * Service manager alias to concrate service class
     *
     * @var string
     */
    protected $_serviceKey;
    /**
     * Accept header criteria
     *
     * @var array
     */
    protected $_acceptCriteria
        = array(
            'Laminas\View\Model\JsonModel' => array(
                'application/json',
                'application/jsonp',
                'application/javascript',
                '*/*'
            ),
            'Laminas\View\Model\ViewModel' => array(
                '*/*'
            ),
        );

    public function __construct(ServiceManager $serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
    }

    /**
     * Return an item by ID
     *
     * Example
     * curl -X GET -H "Accept: application/json"
     *
     * @param mixed $entityId
     * @method GET
     *
     * @endpoint /affiliate/:entity/:id
     * @return mixed|\Laminas\View\Model\ModelInterface
     */
    public function get($entityId)
    {
        $params = array_merge($this->params()->fromQuery(), $this->params()->fromRoute());

        return $this->sendPayload($this->_getService()->fetchOne($entityId, $params));
    }

    /**
     * Return a list
     * Example
     * curl -X GET -H "Accept: application/json"
     *
     * @method GET
     * @endpoint /affiliate/:entity
     * @return mixed|\Laminas\View\Model\ModelInterface
     */
    public function getList()
    {
        $params = array_merge($this->params()->fromQuery(), $this->params()->fromRoute());

        return $this->sendPayload($this->_getService()->fetchAll($params));
    }

    /**
     * Create an entity
     *
     * example
     * curl -X PUT -d "title=explorer&category=1"  -d "merchant_fields=id,category&category_fields=id,title"
     * -H "Accept: application/json" affiliate-manager.com/affiliate/merchant/5
     *
     * @param mixed $data
     *
     * @method POST
     * @endpoint /affiliate/:entity
     * @return mixed|\Laminas\View\Model\ModelInterface
     */
    public function create($data)
    {
        $params = array_merge($data, $this->params()->fromRoute());

        return $this->sendPayload($this->_getService()->create($params));
    }

    /**
     * Update an entity
     *
     * example
     * curl -X PUT -d "title=explorer&category=1"  -d "merchant_fields=id,category&category_fields=id,title"
     * -H "Accept: application/json" affiliate-manager.com/affiliate/merchant/5
     *
     * @param mixed $entityId
     * @param mixed $data
     * @method PUT
     *
     * @endpoint /affiliate/:entity/:id
     * @return mixed|\Laminas\View\Model\ModelInterface
     */
    public function update($entityId, $data)
    {
        $params = array_merge($data, $this->params()->fromRoute());

        return $this->sendPayload($this->_getService()->update($entityId, $params));
    }

    /**
     * Delete an entity by ID
     *
     * @param mixed $entityId
     *
     * @method DELETE
     * @endpoint /affiliate/:entity/:id
     * @return mixed|\Laminas\View\Model\ModelInterface
     */
    public function delete($entityId)
    {
        return $this->sendPayload($this->_getService()->delete($entityId));
    }

    /**
     * Render output
     *
     * @param $payload
     *
     * @return \Laminas\View\Model\ModelInterface
     */
    protected function _sendPayload($payload)
    {
        return $this->sendPayload($payload);
    }

    /**
     * @param null $serviceKey
     *
     * @return \SynergyCommon\Service\BaseService
     */
    protected function _getService($serviceKey = null)
    {
        $serviceKey = $serviceKey ?: $this->_serviceKey;

        return $this->getServiceLocator()->get($serviceKey);
    }

    /**
     * @return \Interop\Container\ContainerInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
