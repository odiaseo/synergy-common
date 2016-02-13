<?php
namespace SynergyCommon\Delegator;

use Zend\I18n\Translator\Resources;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class TranslatorDelegator
 * @package SynergyCommon
 */
class TranslatorDelegator implements DelegatorFactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @param string $name
     * @param string $requestedName
     * @param callable $callback
     * @return mixed
     */
    public function createDelegatorWithName(ServiceLocatorInterface $services, $name, $requestedName, $callback)
    {
        $translator = $callback();
        $translator->addTranslationFilePattern(
            'phpArray',
            Resources::getBasePath(),
            Resources::getPatternForValidator()
        );
        $translator->addTranslationFilePattern(
            'phpArray',
            Resources::getBasePath(),
            Resources::getPatternForCaptcha()
        );

        return $translator;
    }
}
