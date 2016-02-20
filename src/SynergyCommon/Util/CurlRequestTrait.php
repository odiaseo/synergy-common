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

    public function curlRequest($url, $postData = '', array $header = null, $contentType = '')
    {
        $this->printMessage(' >> Processing curl request ... ', 1, false);

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
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);

        if ($postData) {
            curl_setopt($curlHandle, CURLOPT_POST, true);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $postData);
        }

        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10);

        $curlResponse = curl_exec($curlHandle);
        $curlErrno    = curl_errno($curlHandle);

        if ($curlErrno) {
            $curlError = curl_error($curlHandle);
            $this->printErrorMessage($curlError);
            throw new RuntimeException($curlError);
        } else {
            // $this->printSuccessMessage('done');
        }

        curl_close($curlHandle);

        return $curlResponse;
    }
}
