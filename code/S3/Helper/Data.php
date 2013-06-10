<?php
/**
 * @author jonathan@madepeople.se
 */
class Made_S3_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_client;
    
    /**
     * Return a (cached) S3 client instance
     */
    public function getClient()
    {
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
    
    public function useS3()
    {
        return Mage::getStoreConfig('system/media_storage_configuration/media_storage') == Made_S3_Model_File_Storage::STORAGE_MEDIA_S3
                && $this->_client;
    }
}