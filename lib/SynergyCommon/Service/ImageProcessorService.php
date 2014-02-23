<?php
namespace SynergyCommon\Service;

use Zend\Console\Request;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class ImageProcessorFactory
    implements ServiceManagerAwareInterface, CompressionInterface
{
    /** @var \Zend\ServiceManager\ServiceManager */
    protected $_serviceManager;


    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->_serviceManager = $serviceManager;
    }

    public function compress()
    {
        $config      = $this->_serviceManager->get('config');
        $imageConfig = isset($config['image_config']) ? $config['image_config'] : array();

        if (!empty($imageConfig) and extension_loaded('pngquant')) {
            $watchDirectory       = $imageConfig['watch_directory'];
            $destinationDirectory = $imageConfig['destination'];
            $masterDirectory      = $imageConfig['master_directory'];

            $min = empty($imageConfig['min_quality']) ? 60 : $imageConfig['min_quality'];
            $max = empty($imageConfig['max_quality']) ? 90 : $imageConfig['max_quality'];

            if ($this->_serviceManager->has('logger')) {
                $logger = $this->_serviceManager->get('logger');
            } else {
                $logger = $this;
            }

            $hasTrimange = extension_loaded('trimage');

            /** @var $file  \SplFileObject */
            foreach (new \DirectoryIterator($watchDirectory) as $file) {
                if ($file->isFile()) {
                    $sourceFile      = $file->getPathname();
                    $masterCopy      = rtrim($masterDirectory, '/') . '/' . $file->getFilename();
                    $destinationFile = rtrim($destinationDirectory, '/') . '/' . $file->getFilename();

                    $logger->info('original copied to ' . $destinationFile);

                    if (copy($sourceFile, $masterCopy)) {
                        $arg     = \escapeshellarg($sourceFile);
                        $command = "pngquant --force --quality={$min}-{$max} {$arg} - < {$sourceFile}";
                        $content = \shell_exec($command);

                        if ($content) {
                            file_put_contents($destinationFile, $content);
                            $logger->info('copied to ' . $destinationFile);

                            if ($hasTrimange) {
                                $triArg     = escapeshellarg($destinationFile);
                                $triCommand = " xvfb-run -a trimage -f {$triArg}";
                                \exec($triCommand, $output, $return);
                                $logger->info('compresed with trimage');
                            }

                            if (unlink($sourceFile)) {
                                $logger->info('source file deleted from watch directory');
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
}