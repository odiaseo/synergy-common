<?php
namespace SynergyCommon\Service;

/**
 * Interface ClientAwareInterface
 * @package SynergyCommon\Service
 */
interface ClientAwareInterface
{
    public function setClient($client);

    public function getClient();
}
