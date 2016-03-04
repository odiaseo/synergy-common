<?php

namespace SynergyCommon\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractTranslation extends AbstractPersonalTranslation
{
    /**
     * @var string $locale
     *
     * @ORM\Column(type="string", length=15)
     */
    protected $locale;
}
