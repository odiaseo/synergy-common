<?php
namespace SynergyCommon\Controller\Plugin;

use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class SendPayload
 *
 * Send request payload
 *
 * @package SynergyCommon\Controller\Plugin
 */
class SendPayload extends AbstractPlugin
{
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

    public function __invoke($payload)
    {
        /** @var $controller \Zend\Mvc\Controller\AbstractRestfulController */
        $controller = $this->getController();
        /** @var $response \Zend\Http\PhpEnvironment\Response */
        $response  = $controller->getResponse();
        $viewModel = $controller->acceptableViewModelSelector($this->_acceptCriteria);

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
}
