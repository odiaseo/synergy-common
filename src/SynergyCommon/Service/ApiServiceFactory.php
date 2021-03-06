<?php
namespace SynergyCommon\Service;

use Interop\Container\ContainerInterface;
use SynergyCommon\Client\ClientOptions;
use SynergyCommon\Client\HttpRestJsonClient;
use Laminas\Http\Client as HttpClient;
use Laminas\Http\Request;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Rest API service
 *
 * Class ApiService
 *
 * @package SynergyCommon\Service
 */
class ApiServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return BaseApiService
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {

        $config = $serviceLocator->get('Config');

        if (isset($config['synergy']['api']['adapter'])) {
            $adapter = $config['synergy']['api']['adapter'];
        } else {
            $adapter = 'Laminas\Http\Client\Adapter\Curl';
        }

        $httpClient = new HttpClient();
        $httpClient->setAdapter($adapter);

        $request            = new Request();
        $httpRestJsonClient = new HttpRestJsonClient($httpClient, $request);

        if (isset($config['synergy']['api']['options'])) {
            $options = new ClientOptions($config['synergy']['api']['options']);
        } else {
            $options = new ClientOptions();
        }

        $httpRestJsonClient->setOptions($options);
        $service = new BaseApiService();

        if (isset($config['synergy']['api']['logger'])
            and $serviceLocator->has($config['synergy']['api']['logger'])
        ) {
            /** @var $logger \SynergyCommon\Util\ErrorHandler */
            $logger = $serviceLocator->get($config['synergy']['api']['logger']);
            $service->setLogger($logger);
        }

        $service->setClient($httpRestJsonClient);

        return $service;
    }
}
