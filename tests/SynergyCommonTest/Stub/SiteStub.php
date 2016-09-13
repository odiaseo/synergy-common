<?php
namespace SynergyCommonTest\Stub;

use SynergyCommon\Entity\BaseSite;

class SiteStub extends BaseSite
{

    public function getAllowedSites()
    {
        return [1, 2];
    }
}
