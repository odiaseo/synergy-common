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
     * @param int  $repeat
     * @param bool $lineBreak
     * @param int  $color
     * @param null $bgColor
     *
     * @return \Zend\Console\Adapter\Windows
     */
    public function printMessage($msg, $repeat = 1, $lineBreak = true, $color = ColorInterface::WHITE, $bgColor = null)
    {
        /** @var $console \Zend\Console\Adapter\Windows */
        $console = $this->getServiceManager()->get('console');
        if (php_sapi_name() == 'cli') {
            $msg = is_array($msg) ? print_r($msg, true) : $msg;
            if ($this->getVerbose()) {
                $sign = $repeat ? str_repeat("\t", $repeat) . ' ' : '';
                $msg  = "{$sign}$msg";
                if ($lineBreak) {
                    $console->writeLine($msg, $color, $bgColor);
                } else {
                    $console->write($msg, $color, $bgColor);
                }
            } elseif ($this->getServiceManager()->has('logger')) {
                $this->getServiceManager()->get('logger')->info($msg);
            }
        }

        return $console;
    }

    /**
     * @param      $msg
     * @param int  $repeat
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
     * @param int  $repeat
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
     * @param int  $repeat
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
     * @param int  $repeat
     * @param bool $lineBreak
     *
     * @return \Zend\Console\Adapter\Windows
     */
    public function printInfo($msg, $repeat = 1, $lineBreak = true)
    {
        return $this->printMessage($msg, $repeat, $lineBreak, ColorInterface::LIGHT_BLUE);
    }
}
