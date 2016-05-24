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
     * @param string $postData
     * @param array $header
     * @param string $contentType
     * @param int $timeout
     * @return mixed
     */
    public function curlRequest($url, $postData = '', array $header = null, $contentType = '', $timeout = 300)
    {
        $this->printMessage(' >> Processing curl request ... ' . $timeout . 'sec ..', 1, false);

        $header   = $header ?: array();
        $header[] = 'Accept-Charset: UTF-8';

        if ($contentType) {
            $header[] = "Content-Type: {$contentType}";
        }

        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlHandle, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($curlHandle, CURLOPT_MAXREDIRS, 5);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);

        if ($postData) {
            curl_setopt($curlHandle, CURLOPT_POST, true);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $postData);
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
