<?php

namespace SynergyCommon\Doctrine\Event;

use Gedmo\Sluggable\SluggableListener as GedmoListener;
use SynergyCommon\Util;

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
                return Util::urlize($text, [], false);
            }
        );

        $this->setUrlizer(
            function ($text) {
                return Util::urlize($text, [], false);
            }
        );
    }
}
