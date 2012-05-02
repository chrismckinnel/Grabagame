<?php

namespace Grabagame\BookingBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class GrabagameBookingBundle extends Bundle
{
    /**
     * @return string
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
