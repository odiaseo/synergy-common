<?php
namespace SynergyCommon\Image;

use Zend\Console\Request;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class ImageCompressor
    implements ServiceManagerAwareInterface, CompressionInterface
{
    /** @var \Zend\Log\LoggerInterface */
    protected $_logger;
    /** @var Config\ImageCompressorOptions */
    protected $_config;
    /** @var \Zend\ServiceManager\ServiceManager */
    protected $_serviceManager;

    public function compress()
    {
        $logger = $this->getLogger() ? : $this;

        $watchDirectory       = rtrim($this->_config->getWatchDirectory(), '/') . '/';
        $destinationDirectory = rtrim($this->_config->getDestinationDirectory(), '/') . '/';
        $jpegDirectory        = rtrim($this->_config->getJpegDirectory(), '/') . '/';
        $masterDirectory      = rtrim($this->_config->getOriginalDirectory(), '/') . '/';

        $min = $this->_config->getMinQuality();
        $max = $this->_config->getMaxQuality();

        if (!file_exists($watchDirectory)) {
            mkdir($watchDirectory, 0775, true);
            $logger->info('creating directory ' . $watchDirectory);
        }

        /** @var $file  \SplFileObject */
        foreach (new \DirectoryIterator($watchDirectory) as $file) {

            if ($file->isDot()) {
                continue;
            } elseif ($file->isFile()) {
                $sourceFile = $file->getPathname();
                $logger->info('processing ' . $sourceFile);

                $adapter = $this->getConfig()->getAdapter();
                $logger->info('coping original ... ' . $masterDirectory);

                if ($adapter->copy($sourceFile, $masterDirectory)) {
                    $logger->info(' ... done');

                    $arg     = \escapeshellarg($sourceFile);
                    $ext     = '-new.png';
                    $command = "pngquant -f --ext {$ext} --quality={$min}-{$max} {$arg} < {$sourceFile}";
                    \shell_exec($command);

                    $tmpFile = str_replace('.png', $ext, $sourceFile);

                    if (file_exists($tmpFile)) {
                        $logger->info($tmpFile . ' created');

                        $triArg     = escapeshellarg($tmpFile);
                        $triCommand = " xvfb-run -a trimage -f {$triArg}";
                        \exec($triCommand, $output, $return);
                        $logger->info('compresed with trimage');


                        if ($converter = $this->_config->getJpegConverter()) {
                            $converter = $this->_serviceManager->get($converter);
                            if ($converter instanceof ImageConverterInterface) {
                                $convertedFile = $converter->convert($tmpFile);
                                $adapter->copy($convertedFile, $jpegDirectory . basename($convertedFile));
                                $logger->info('file converted to ' . $convertedFile);
                            }
                        }
                        if ($adapter->copy($tmpFile, $destinationDirectory . basename($sourceFile))) {
                            $logger->info('copied to ' . $destinationDirectory);

                            if (unlink($sourceFile) && unlink($tmpFile)) {
                                $logger->info('source and temporary files deleted');

                                return true;
                            }
                        } else {
                            $logger->info(
                                'Unable to copy the compressed file to destination: ' . $destinationDirectory
                            );
                        }
                    } else {
                        $logger->info('compression failed');
                    }
                } else {
                    $logger->info('unable to copy original file to master: ' . $masterDirectory);
                }
            }
        }

        return false;
    }

    /**
     * Output information if no logger is set
     *
     * @param $text
     */
    public function info($text)
    {
        if ($this->_serviceManager->get('request') instanceof Request) {
            echo $text;
        }
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
}