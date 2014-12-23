<?php
namespace SynergyCommon\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\AbstractRestfulController;

class BaseRestfulController
    extends AbstractRestfulController
{
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
            'Zend\View\Model\JsonModel' => array(
                'application/json',
                'application/jsonp',
                'application/javascript',
                '*/*'
            ),
            'Zend\View\Model\ViewModel' => array(
                '*/*'
            ),
        );

    /**
     * Return an item by ID
     *
     * Example
     * curl -X GET -H "Accept: application/json"
     * "affiliate-manager.com/affiliate/merchant/5?merchant_fields=id,title,isActive&category_fields=id,title,createdAt&filter\[isActive\]=0"
     *
     * @param mixed $id
     * @method GET
     *
     * @endpoint /affiliate/:entity/:id
     * @return mixed|\Zend\View\Model\ModelInterface
     */
    public function get($id)
    {
        $params = array_merge(
            $this->params()->fromQuery(),
            $this->params()->fromRoute()
        );


        return $this->_sendPayload(
            $this->_getService()->fetchOne($id, $params)
        );
    }

    /**
     * Return a list
     * Example
     * curl -X GET -H "Accept: application/json"
     * "affiliate-manager.com/affiliate/merchant?merchant_fields=id,title,isActive&category_fields=id,title,createdAt&filter\[isActive\]=0"
     *
     * @method GET
     * @endpoint /affiliate/:entity
     * @return mixed|\Zend\View\Model\ModelInterface
     */
    public function getList()
    {
        $params = array_merge(
            $this->params()->fromQuery(),
            $this->params()->fromRoute()
        );

        return $this->_sendPayload(
            $this->_getService()->fetchAll($params)
        );
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
     * @return mixed|\Zend\View\Model\ModelInterface
     */
    public function create($data)
    {
        $params = array_merge(
            $data,
            $this->params()->fromRoute()
        );

        return $this->_sendPayload(
            $this->_getService()->create($params)
        );
    }

    /**
     * Update an entity
     *
     * example
     * curl -X PUT -d "title=explorer&category=1"  -d "merchant_fields=id,category&category_fields=id,title"
     * -H "Accept: application/json" affiliate-manager.com/affiliate/merchant/5
     *
     * @param mixed $id
     * @param mixed $data
     * @method PUT
     *
     * @endpoint /affiliate/:entity/:id
     * @return mixed|\Zend\View\Model\ModelInterface
     */
    public function update($id, $data)
    {
        $params = array_merge(
            $data,
            $this->params()->fromRoute()
        );

        return $this->_sendPayload(
            $this->_getService()->update($id, $params)
        );
    }

    /**
     * Delete an entity by ID
     *
     * @param mixed $id
     *
     * @method DELETE
     * @endpoint /affiliate/:entity/:id
     * @return mixed|\Zend\View\Model\ModelInterface
     */
    public function delete($id)
    {
        return $this->_sendPayload(
            $this->_getService()->delete($id)
        );
    }

    /**
     * Render output
     *
     * @param $payload
     *
     * @return \Zend\View\Model\ModelInterface
     */
    protected function _sendPayload($payload)
    {
        /** @var $response \Zend\Http\PhpEnvironment\Response */
        $response  = $this->getResponse();
        $viewModel = $this->acceptableViewModelSelector($this->_acceptCriteria);

        if (isset($payload['error']) and $payload['error'] == true) {
            if (!empty($payload['code'])) {
                $response->setStatusCode($payload['code']);
            } else {
                $response->setStatusCode(Response::STATUS_CODE_400);
            }
        }

        if (isset($payload['content'])) {
            $viewModel->setVariables($payload['content']);
        } elseif (!empty($payload)) {
            $viewModel->setVariables($payload);
        }

        return $viewModel;
    }

    /**
     * @param null $serviceKey
     *
     * @return \SynergyCommon\Service\BaseService
     */
    protected function _getService($serviceKey = null)
    {
        $serviceKey = $serviceKey ? : $this->_serviceKey;

        return $this->getServiceLocator()->get($serviceKey);
    }

}