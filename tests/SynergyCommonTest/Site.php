<?php
namespace SynergyCommonTest;

use SynergyCommon\Entity\BaseEntity;

class Site extends BaseEntity
{

    public function getId()
    {
        return 28;
    }

    public function getDomain()
    {
        return 'vaboose.co.uk';
    }
}