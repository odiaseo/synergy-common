<?php

namespace SynergyCommon;

interface PageAwareInterface
{
    public function setPageMetadata($meta);

    public function getPageMeta();
}