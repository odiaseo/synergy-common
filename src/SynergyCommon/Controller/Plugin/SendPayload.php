<?php
namespace SynergyCommon\Controller\Plugin;

use Laminas\Http\PhpEnvironment\Response;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

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

    public function __invoke($payload)
    {
        /** @var $controller \Laminas\Mvc\Controller\AbstractRestfulController */
        $controller = $this->getController();
        /** @var $response \Laminas\Http\PhpEnvironment\Response */
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
