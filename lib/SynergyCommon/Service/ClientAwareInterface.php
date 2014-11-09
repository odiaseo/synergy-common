<?php

namespace SynergyCommon\Service;

interface ClientAwareInterface
{
    public function setClient($client);

    public function getClient();
}