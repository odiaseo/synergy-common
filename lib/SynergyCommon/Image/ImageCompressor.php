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

        if (extension_loaded('pngquant')) {

            $directory     = trim($this->_config->getSourceDirectory(), '/') . '/';
            $destDirectory = trim($this->_config->getDestinationDirectory(), '/') . '/';

            $watchDirectory       = $directory . 'watch/';
            $destinationDirectory = $destDirectory . 'compressed/';
            $masterDirectory      = $destDirectory . 'original/';

            $min = $this->_config->getMinQuality();
            $max = $this->_config->getMaxQuality();


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
                            } else {
                                $logger->notice('trimage library not found');
                            }

                            if ($adapter->copy($tmpFile, $destinationDirectory)) {
                                $logger->info('copied to ' . $destinationDirectory);

                                if (unlink($sourceFile) && unlink($tmpFile)) {
                                    $logger->info('source and temporary files deleted');
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
        } else {
            $logger->info('pnquant extenstion not found');
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