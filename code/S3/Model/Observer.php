<?php
/**
 * @author jonathan@madepeople.se
 */

// This is a bit on the lol side, but it does the work and the S3Client needs
// to be globally available anyway
require_once dirname(__FILE__) . '/../aws/aws-autoloader.php';
use Aws\S3\S3Client;

class Made_S3_Model_Observer
{
    /**
     * Load the required AWS modules and set up the S3 stream wrapper, if
     * S3 is chosen for media storage
     * 
     * @param Varien_Event_Observer $observer
     */
    public function initAwsSdk(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('made_s3')) {
            return;
        }
        $client = S3Client::factory(array(
            'key' => Mage::getStoreConfig('system/s3/access_key_id'),
            'secret' => Mage::getStoreConfig('system/s3/access_secret'),
        ));

        $client->registerStreamWrapper();
        
        Mage::helper('made_s3')->setClient($client);
    }
}