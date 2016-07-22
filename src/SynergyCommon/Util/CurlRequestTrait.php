<?php
namespace SynergyCommon\Util;

use RuntimeException;

/**
 * Class CurlRequestTrait
 *
 * @package SynergyCommon\Util
 */
trait CurlRequestTrait
{
    use ConsolePrinterTrait;

    /**
     * @param $url
     * @param string $data
     * @param array|null $header
     * @param string $contentType
     * @param int $timeout
     * @param string $cred
     * @return mixed
     */
    public function curlRequest($url, $data = '', array $header = null, $contentType = '', $timeout = 300, $cred = '')
    {
        $this->printMessage(' >> Processing curl request ... ' . $timeout . 'sec ..', 1, false);

        $header   = $header ?: array();
        $header[] = 'accept-charset: UTF-8';

        if ($contentType) {
            $header[] = "content-type: {$contentType}";
        }

        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlHandle, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($curlHandle, CURLOPT_MAXREDIRS, 5);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);

        if ($data) {
            curl_setopt($curlHandle, CURLOPT_POST, true);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
        }

        if ($cred) {
            curl_setopt($curlHandle, CURLOPT_USERPWD, $cred);
        }

        curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeout);

        $curlResponse = curl_exec($curlHandle);
        $curlErrno    = curl_errno($curlHandle);

        if ($curlErrno) {
            $curlError = curl_error($curlHandle);
            $this->printErrorMessage($curlError);
            throw new RuntimeException($curlError);
        } else {
            $this->printSuccessMessage('done');
        }

        curl_close($curlHandle);

        return $curlResponse;
    }
}
