<?php

namespace SynergyCommon\Model\Config;

use Zend\Stdlib\AbstractOptions;

class SortOrder
    extends AbstractOptions
{
    protected $_field;
    protected $_direction;

    public function setDirection($direction)
    {
        $this->_direction = $direction;
    }

    public function getDirection()
    {
        return $this->_direction;
    }

    public function setField($field)
    {
        $this->_field = $field;
    }

    public function getField()
    {
        return $this->_field;
    }

}