<?php
namespace SynergyCommon\Entity;

use Doctrine\ORM\Mapping as ORM;
use SynergyCommon\Exception\InvalidArgumentException;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\InputFilter\InputFilter;

/**
 * Class AbstractEntity
 * @method formatDeeplink()
 *
 * @package SynergyCommon\Entity
 */
abstract class AbstractEntity
{
    /** @var \Zend\InputFilter\InputFilter */
    protected $inputFilter;

    /**
     * Convert the object to an array.
     *
     * @param null $object
     *
     * @return array
     */
    public function toArray($object = null)
    {
        $list   = array();
        $object = $object ?: $this;
        foreach (get_object_vars($object) as $key => $value) {
            if (substr($key, 0, 1) != '_') {
                if (is_object($value)) {
                    $list[$key] = $this->toArray($value);
                } else {
                    $list[$key] = $value;
                }
            }
        }

        return $list;
    }

    /**
     * Populate object attributes from array
     *
     * @param array $data
     *
     * @return $this
     */
    public function fromArray(array $data)
    {
        foreach ($data as $field => $value) {
            $this->{$field} = $value;
        }

        return $this;
    }

    public function __toString()
    {
        if (isset($this->slug)) {
            return $this->slug;
        } elseif (isset($this->title)) {
            return $this->title;
        } else {
            return json_encode($this->toArray());
        }
    }

    public function __call($method, $args)
    {
        $type     = substr($method, 0, 3);
        $property = lcfirst(substr($method, 3));

        if ($type == 'set') {
            if (count($args)) {
                $this->$property = $args[0];

                return $this;
            } else {
                throw new InvalidArgumentException(sprintf("No argument provided with %s", $method));
            }
        } elseif ($type == 'get' and property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    "Method %s called with arguments %s is undefined", $method, print_r($args, true)
                )
            );
        }
    }

    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * @param $property
     * @param $value
     *
     * @return $this
     */
    public function __set($property, $value)
    {
        $this->$property = $value;

        return $this;
    }

    /**
     * Populate object attributes from array
     *
     * @param array $data
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function exchangeArray(array $data)
    {
        $wordFilter = new UnderscoreToCamelCase();
        $filters    = $this->getInputFilter();
        foreach ($data as $field => $value) {
            $method = 'set' . ucfirst($wordFilter->filter($field));

            if (method_exists($this, $method)) {
                if ($filters and $filters->has($field)) {
                    $input = $filters->get($field);
                    $input->setValue($value);
                    if ($input->isValid()) {
                        $value = $input->getValue();
                    } else {
                        throw new \InvalidArgumentException('Invalid value found for field ' . $field);
                    }
                }

                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * @param \Zend\InputFilter\InputFilter $inputFilter
     */
    public function setInputFilter($inputFilter)
    {
        $this->inputFilter = $inputFilter;
    }

    /**
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter()
    {
        if (empty($this->inputFilter)) {
            $this->inputFilter = new InputFilter();
        }
        return $this->inputFilter;
    }

    public function removeWhiteSpace($value)
    {
        $value = str_replace(array("\n", "\r"), ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value);
    }

    abstract public function getId();
}
