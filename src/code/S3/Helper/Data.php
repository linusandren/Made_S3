<?php

/**
 * @author jonathan@madepeople.se
 */

// This is a bit on the lol side, but it does the work and the S3Client needs
// to be globally available anyway
require_once dirname(__FILE__) . '/../aws/aws-autoloader.php';
use Aws\S3\S3Client;

class Made_S3_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_client;

    /**
     * Return a (cached) S3 client instance
     */
    public function getClient()
    {
        if (!$this->_client) {
            $client = S3Client::factory(array(
                'key' => Mage::getStoreConfig('system/s3/access_key_id'),
                'secret' => Mage::getStoreConfig('system/s3/access_secret'),
            ));
            $this->setClient($client);
        }

        return $this->_client;
    }

    /**
     * Set the S3 client instance
     *
     * @param type $client
     */
    public function setClient(Aws\S3\S3Client $client)
    {
        $this->_client = $client;
        return $this;
    }
}