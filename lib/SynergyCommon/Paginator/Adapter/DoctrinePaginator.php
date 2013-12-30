<?php
    namespace SynergyCommon\Paginator\Adapter;

    use Doctrine\ORM\Query;
    use Doctrine\ORM\Tools\Pagination\Paginator;
    use Zend\Paginator\Adapter\AdapterInterface;

    class DoctrinePaginator extends Paginator implements AdapterInterface
    {
        public function getItems($offset, $itemCountPerPage)
        {
            return $this->getIterator();
        }
    }