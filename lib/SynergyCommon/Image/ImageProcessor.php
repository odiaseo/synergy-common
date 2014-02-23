<?php
namespace SynergyCommon\Image;

use Zend\Console\Request;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class ImageProcessor
    implements ServiceManagerAwareInterface, CompressionInterface
{
    /** @var \Zend\Log\LoggerInterface */
    protected $_logger;
    /** @var Config\ImageProcessorOptions */
    protected $_config;
    /** @var \Zend\ServiceManager\ServiceManager */
    protected $_serviceManager;

    public function compress()
    {

        if (extension_loaded('pngquant')) {

            $directory            = trim($this->_config->getDirectory(), '/') . '/';
            $watchDirectory       = $directory . 'watch/';
            $destinationDirectory = $directory . 'compressed/';
            $masterDirectory      = $directory . 'original/';

            $min    = $this->_config->getMinQuality();
            $max    = $this->_config->getMaxQuality();
            $logger = $this->getLogger() ? : $this;

            $hasTrimange = extension_loaded('trimage');

            /** @var $file  \SplFileObject */
            foreach (new \DirectoryIterator($watchDirectory) as $file) {
                if ($file->isFile()) {
                    $sourceFile = $file->getPathname();
                    $adapter    = $this->getConfig()->getAdapter();

                    if ($adapter->copy($sourceFile, $masterDirectory)) {
                        $logger->info('original copied to ' . $masterDirectory);

                        $arg     = \escapeshellarg($sourceFile);
                        $command = "pngquant --force --quality={$min}-{$max} {$arg} - < {$sourceFile}";
                        $content = \shell_exec($command);

                        $tmpFile = sys_get_temp_dir() . '/' . $file->getFilename();

                        if (file_put_contents($tmpFile, $content)) {

                            if ($hasTrimange) {
                                $triArg     = escapeshellarg($tmpFile);
                                $triCommand = " xvfb-run -a trimage -f {$triArg}";
                                \exec($triCommand, $output, $return);
                                $logger->info('compresed with trimage');
                            }

                            $adapter->copy($tmpFile, $destinationDirectory);
                            $logger->info('copied to ' . $destinationDirectory);

                            if (unlink($sourceFile) && unlink($tmpFile)) {
                                $logger->info('source and temporary files deleted');
                            }
                        } else {
                            $logger->info('compression failed');
                        }
                    }
                }
            }
        }
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
