<?php
namespace SynergyCommon\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use SynergyCommon\Entity\BaseEntity;

/**
 * Setting
 *
 * @ORM\Entity
 * @ORM\Table(name="Setting")
 *
 */
class Setting
    extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="SynergyCommon\Admin\Entity\SettingKey")
     * @ORM\JoinColumn(name="setting_key_id", referencedColumnName="id", nullable=false)
     */
    protected $settingKey;
    /**
     * @ORM\Column(type="text", name="setting_value")
     */
    protected $value = '';

}