<?php
namespace SynergyCommon\Util;


use Monolog\Logger;

class ErrorHandler
    extends Logger
{

    public function logException(\Exception $e)
    {
        $log = $this->processException($e);
        $this->err($log);
    }


    /**
     * Format exception
     *
     * @param \Exception $e
     *
     * @return string
     */
    public static function processException(\Exception $e)
    {
        $trace = $e->getTraceAsString();
        $i     = 1;
        do {
            $messages[] = $i++ . ": " . $e->getMessage();
        } while ($e = $e->getPrevious());

        $log = "Exception:\n" . implode("\n", $messages);
        $log .= "\nTrace:\n" . $trace . "\n\n";

        return $log;
    }
}