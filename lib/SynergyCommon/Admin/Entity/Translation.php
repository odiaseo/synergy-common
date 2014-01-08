<?php
namespace SynergyCommon\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\AbstractEntity;

/**
 * Translation
 *
 * @ORM\Entity
 * @ORM\Table(name="Translation")
 *
 */
class Translation
    extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=50, name="name", unique=true);
     */
    private $key;
    /**
     * @ORM\Column(type="text", nullable =false)
     */
    private $segment;
    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable =false)
     */
    private $content;
    /**
     * @Gedmo\Locale
     */
    private $locale;

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setSegment($segment)
    {
        $this->segment = $segment;
    }

    public function getSegment()
    {
        return $this->segment;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

}