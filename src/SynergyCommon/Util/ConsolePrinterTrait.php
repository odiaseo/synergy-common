<?php
namespace SynergyCommon\Util;

use Zend\Console\ColorInterface;

/**
 * Class ConsolePrinterTrait
 * @method getVerbose()
 * @method \Zend\ServiceManager\ServiceManager getServiceManager()
 *
 * @package SynergyCommon\Util
 */
trait ConsolePrinterTrait
{
    /**
     * Print message to console
     *
     * @param      $msg
     * @param int $repeat
     * @param bool $lineBreak
     * @param int $color
     * @param null $bgColor
     *
     * @return \Zend\Console\Adapter\Windows
     */
    public function printMessage($msg, $repeat = 1, $lineBreak = true, $color = ColorInterface::WHITE, $bgColor = null)
    {
        /** @var $console \Zend\Console\Adapter\Windows */
        $console = $this->getInternalLocator()->get('console');
        if ($this->isVerbose()) {
            if (php_sapi_name() == 'cli') {
                $msg = is_array($msg) ? print_r($msg, true) : $msg;

                $sign = $repeat ? str_repeat("\t", $repeat) : '';
                $sign .= ' ';
                $msg = "{$sign}$msg";
                if ($lineBreak) {
                    $console->writeLine($msg, $color, $bgColor);
                } else {
                    $console->write($msg, $color, $bgColor);
                }
            } elseif ($this->getServiceManager()->has('logger')) {
                $this->getInternalLocator()->get('logger')->info($msg);
            }
        }

        return $console;
    }

    /**
     * @param      $msg
     * @param int $repeat
     * @param bool $lineBreak
     *
     * @return \Zend\Console\Adapter\Windows
     */
    public function printErrorMessage($msg, $repeat = 1, $lineBreak = true)
    {
        return $this->printMessage($msg, $repeat, $lineBreak, ColorInterface::RED);
    }

    /**
     * @param      $msg
     * @param int $repeat
     * @param bool $lineBreak
     *
     * @return \Zend\Console\Adapter\Windows
     */
    public function printSuccessMessage($msg, $repeat = 1, $lineBreak = true)
    {
        return $this->printMessage($msg, $repeat, $lineBreak, ColorInterface::GREEN);
    }

    /**
     * @param      $msg
     * @param int $repeat
     * @param bool $lineBreak
     *
     * @return \Zend\Console\Adapter\Windows
     */
    public function printWarningMessage($msg, $repeat = 1, $lineBreak = true)
    {
        return $this->printMessage($msg, $repeat, $lineBreak, ColorInterface::LIGHT_YELLOW);
    }

    /**
     * @param      $msg
     * @param int $repeat
     * @param bool $lineBreak
     *
     * @return \Zend\Console\Adapter\Windows
     */
    public function printProgressMessage($msg, $repeat = 1, $lineBreak = true)
    {
        return $this->printMessage($msg, $repeat, $lineBreak, ColorInterface::LIGHT_MAGENTA);
    }

    /**
     * @param      $msg
     * @param int $repeat
     * @param bool $lineBreak
     *
     * @return \Zend\Console\Adapter\Windows
     */
    public function printInfo($msg, $repeat = 1, $lineBreak = true)
    {
        return $this->printMessage($msg, $repeat, $lineBreak, ColorInterface::LIGHT_BLUE);
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager
     */
    public function getInternalLocator()
    {
        if (method_exists($this, 'getServiceManager')) {
            return $this->getServiceManager();
        } elseif (method_exists($this, 'getServiceLocator')) {
            return $this->getServiceLocator();
        }

        return null;
    }

    protected function isVerbose()
    {
        if (method_exists($this, 'getVerbose')) {
            return $this->getVerbose();
        }

        return true;
    }
}
