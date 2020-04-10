<?php

namespace SynergyCommon\Paginator;

use Laminas\Paginator\Paginator as LaminasPaginator;

/**
 * Class Paginator
 * @package SynergyCommon\Paginator
 */
class Paginator extends LaminasPaginator
{

    /**
     * Get the internal cache id
     * Depends on the adapter and the item count per page
     *
     * Used to tag that unique Paginator instance in cache
     *
     * @return string
     */
    protected function _getCacheInternalId()
    {
        $adapter   = $this->getAdapter();
        $adapterId = null;
        if ($adapter instanceof IdentityProviderInterface) {
            $adapterId = $adapter->getUniqueIdentifier();
        }

        if (!$adapterId) {
            $adapterId = spl_object_hash($this->getAdapter());
        }

        return md5(serialize([$adapterId, $this->getItemCountPerPage()]));
    }
}
