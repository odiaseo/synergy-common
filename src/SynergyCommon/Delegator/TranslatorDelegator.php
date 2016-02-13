<?php
namespace SynergyCommon\Delegator;

use Zend\I18n\Translator\Resources;
use Zend\I18n\Translator\Translator;
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
        /** @var Translator $translator */
        $translator = $callback();
        $locale     = $translator->getLocale();
        $language   = \Locale::parseLocale($locale)['language'];

        $translator->addTranslationFilePattern(
            'phpArray',
            Resources::getBasePath(),
            sprintf(Resources::getPatternForValidator(), $language)
        );
        $translator->addTranslationFilePattern(
            'phpArray',
            Resources::getBasePath(),
            sprintf(Resources::getPatternForCaptcha(), $language)
        );

        return $translator;
    }
}
