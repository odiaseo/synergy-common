<?php
namespace SynergyCommon\Delegator;

use Interop\Container\ContainerInterface;
use Zend\I18n\Translator\Resources;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class TranslatorDelegator
 * @package SynergyCommon
 */
class TranslatorDelegator implements DelegatorFactoryInterface
{
    /**
     * @param ContainerInterface $services
     * @param string $name
     * @param callable $callback
     * @param array|null $options
     * @return Translator
     */
    public function __invoke(ContainerInterface $services, $name, callable $callback, array $options = null)
    {
        /** @var Translator $translator */
        $translator = $callback();

        $locale   = $translator->getLocale();
        $language = \Locale::parseLocale($locale)['language'];

        $translator->addTranslationFilePattern(
            'phparray',
            Resources::getBasePath(),
            sprintf(Resources::getPatternForValidator(), $language)
        );
        $translator->addTranslationFilePattern(
            'phparray',
            Resources::getBasePath(),
            sprintf(Resources::getPatternForCaptcha(), $language)
        );
        return $translator;
    }
}
