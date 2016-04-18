<?php

/**
 * Not really sure how we should handle callbacks, but if we do, they have to be
 * secured somehow since i suppose we will mess around with product enabling
 * and disabling
 *
 * @author jonathan@madepeople.se
 */
class Made_S3_KrakenController extends Mage_Core_Controller_Front_Action
{
    public function callbackAction()
    {
        // Do nothing, but make sure kraken gets a 200 OK
    }
}