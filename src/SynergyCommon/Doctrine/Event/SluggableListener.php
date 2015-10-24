<?php

namespace SynergyCommon\Doctrine\Event;

use AffiliateManager\Util;
use Gedmo\Sluggable\SluggableListener as GedmoListener;

/**
 * Class SluggableListener
 */
class SluggableListener extends GedmoListener
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTransliterator(
            function ($text) {
                return Util::urlize($text);
            }
        );

        $this->setUrlizer(
            function ($text) {
                return Util::urlize($text);
            }
        );
    }
}
