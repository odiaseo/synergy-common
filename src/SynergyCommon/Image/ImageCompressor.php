<?php
namespace SynergyCommon\Image;

use SynergyCommon\Util\ConsolePrinterTrait;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class ImageCompressor implements ServiceManagerAwareInterface, CompressionInterface
{
    use ConsolePrinterTrait;

    /** @var \Zend\Log\LoggerInterface */
    protected $_logger;
    /** @var Config\ImageCompressorOptions */
    protected $_config;
    /** @var \Zend\ServiceManager\ServiceManager */
    protected $_serviceManager;
    /** @var bool */
    protected $verbose = true;

    /**
     * @return bool
     */
    public function compress()
    {
        $this->printWarningMessage('Method  ' . __FILE__);
        $logger               = $this->getLogger() ?: $this;
        $watchDirectory       = rtrim($this->_config->getWatchDirectory(), '/') . '/';
        $destinationDirectory = rtrim($this->_config->getDestinationDirectory(), '/') . '/';
        $jpegDirectory        = rtrim($this->_config->getJpegDirectory(), '/') . '/';
        $masterDirectory      = rtrim($this->_config->getOriginalDirectory(), '/') . '/';

        $min = $this->_config->getMinQuality();
        $max = $this->_config->getMaxQuality();

        if ( ! file_exists($watchDirectory)) {
            mkdir($watchDirectory, 0775, true);
            $this->printInfo('creating directory ' . $watchDirectory);
        }

        /** @var $file  \SplFileObject */
        foreach (new \DirectoryIterator($watchDirectory) as $file) {

            if ($file->isDot()) {
                continue;
            } elseif ($file->isFile()) {
                $sourceFile = $file->getPathname();
                $this->printInfo(' >> Processing ' . $sourceFile);

                $adapter = $this->getConfig()->getAdapter();
                $this->printInfo(' >> adapter ' . get_class($adapter));
                $this->printInfo(' >> coping original ... ' . $masterDirectory, 1, false);

                if ($adapter->copy($sourceFile, $masterDirectory)) {
                    $this->printInfo(' >> ... done');

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
                            $converter = $this->_serviceManager->get($converter);
                            if ($converter instanceof ImageConverterInterface) {
                                $convertedList = $converter->convert(
                                    $tmpFile, $newFilename, $this->_config->getDimensions()
                                );

                                foreach ($convertedList as $convertedFile) {
                                    $adapter->copy($convertedFile, $jpegDirectory . basename($convertedFile));
                                    $this->printMessage('file converted to ' . $convertedFile);
                                    if (file_exists($convertedFile)) {
                                        unlink($convertedFile);
                                    }
                                }
                            }
                        }
                        if ($adapter->copy($tmpFile, $destinationDirectory . $newFilename)) {
                            $this->printMessage('copied to ' . $destinationDirectory);

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

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->_serviceManager = $serviceManager;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->_serviceManager;
    }
}
