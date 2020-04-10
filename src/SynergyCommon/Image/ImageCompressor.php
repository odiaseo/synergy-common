<?php
namespace SynergyCommon\Image;

use SynergyCommon\Service\ServiceLocatorAwareTrait;
use SynergyCommon\Service\ServiceLocatorAwareInterface;
use SynergyCommon\Util\ConsolePrinterTrait;

/**
 * Class ImageCompressor
 *
 * @package SynergyCommon\Image
 */
class ImageCompressor implements CompressionInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    use ConsolePrinterTrait;

    /** @var \Laminas\Log\LoggerInterface */
    protected $_logger;
    /** @var Config\ImageCompressorOptions */
    protected $_config;
    /** @var bool */
    protected $verbose = true;

    /**
     * @return bool
     */
    public function compress()
    {
        $this->printWarningMessage('Method  ' . __FILE__);
        $watchDirectory       = rtrim($this->_config->getWatchDirectory(), '/') . '/';
        $destinationDirectory = rtrim($this->_config->getDestinationDirectory(), '/') . '/';
        $jpegDirectory        = rtrim($this->_config->getJpegDirectory(), '/') . '/';
        $masterDirectory      = rtrim($this->_config->getOriginalDirectory(), '/') . '/';

        $min = $this->_config->getMinQuality();
        $max = $this->_config->getMaxQuality();

        if (!file_exists($watchDirectory)) {
            mkdir($watchDirectory, 0775, true);
            $this->printInfo('creating directory ' . $watchDirectory);
        }

        foreach (new \DirectoryIterator($watchDirectory) as $file) {

            if ($file->isDot()) {
                continue;
            } elseif ($file->isFile()) {
                $sourceFile = $file->getPathname();
                $this->printInfo(' >> Processing ' . $sourceFile);

                $adapter = $this->getConfig()->getAdapter();
                $this->printInfo(' >> adapter ' . get_class($adapter));
                $this->printInfo(' >> coping original ... ' . $masterDirectory, 1, false);

                if ($output = $adapter->copy($sourceFile, $masterDirectory)) {
                    $this->printInfo(' >> ... done : ' . $output);

                    $arg     = \escapeshellarg($sourceFile);
                    $ext     = '-new.png';
                    $command = "pngquant -f --ext {$ext} --quality={$min}-{$max} {$arg} < {$sourceFile}";
                    \shell_exec($command);

                    $tmpFile = str_replace('.png', $ext, $sourceFile);

                    if (file_exists($tmpFile)) {
                        $newFilename = basename($sourceFile);

                        $this->printMessage($tmpFile . ' created');

                        $triArg     = escapeshellarg($tmpFile);
                        $triCommand = " xvfb-run -a trimage -f {$triArg}";
                        \exec($triCommand, $output, $return);
                        $this->printMessage('compresed with trimage');

                        if ($converter = $this->_config->getJpegConverter()) {
                            $converter = $this->serviceLocator->get($converter);
                            if ($converter instanceof ImageConverterInterface) {
                                $convertedList = $converter->convert(
                                    $tmpFile, $newFilename, $this->_config->getDimensions()
                                );

                                foreach ($convertedList as $convertedFile) {
                                    $output = $adapter->copy($convertedFile, $jpegDirectory . basename($convertedFile));
                                    $this->printMessage('file converted to ' . $convertedFile . ' : ' . $output);
                                    if (file_exists($convertedFile)) {
                                        unlink($convertedFile);
                                    }
                                }
                            }
                        }
                        if ($output = $adapter->copy($tmpFile, $destinationDirectory . $newFilename)) {
                            $this->printMessage('copied to ' . $destinationDirectory . ' : ' . $output);

                            if (unlink($sourceFile) and unlink($tmpFile)) {
                                $this->printMessage('source and temporary files deleted');
                            }
                        } else {
                            $this->printErrorMessage(
                                'Unable to copy the compressed file to destination: ' . $destinationDirectory
                            );
                        }
                    } else {
                        $this->printErrorMessage('compression failed');
                    }
                } else {
                    $this->printErrorMessage('unable to copy original file to master: ' . $masterDirectory);
                }
            }
        }

        return true;
    }

    /**
     * @return boolean
     */
    public function getVerbose()
    {
        return $this->verbose;
    }

    /**
     * @param boolean $verbose
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }

    /**
     * @param $text
     *
     * @return $this
     */
    public function info($text)
    {
        $this->printInfo($text);

        return $this;
    }

    public function setLogger($logger)
    {
        $this->_logger = $logger;
    }

    public function getLogger()
    {
        return $this->_logger;
    }

    public function setConfig($config)
    {
        $this->_config = $config;
    }

    public function getConfig()
    {
        return $this->_config;
    }
}
