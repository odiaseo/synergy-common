<?php

namespace SynergyCommon\Doctrine;

interface Repository
{
    public function overrideEntityManager($manager);
}