<?php

/**
 * S3 related events
 *
 * @author jonathan@madepeople.se
 */
class Made_S3_Model_Observer
{
    /**
     * Set the image cache CDN URL if entered in admin
     *
     * @param Varien_Event_Observer $observer
     */
    public function getImageCdnUrl(Varien_Event_Observer $observer)
    {
        $cdnUrl = Mage::getStoreConfig('system/s3/cdn_url');
        if (empty($cdnUrl)) {
            return;
        }
        $result = $observer->getEvent()->getResult();
        $imageInstance = $observer->getEvent()->getImageInstance();
        $baseDir = Mage::getBaseDir('media');
        $path = str_replace($baseDir . DS, "", $imageInstance->getNewFile());
        $mediaUrl = $cdnUrl . $path;
        $result->setUrl($mediaUrl);
    }

    /**
     * Check if the image has been cached already
     *
     * @param Varien_Event_Observer $observer
     */
    public function madeImageIsCached(Varien_Event_Observer $observer)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $tableName = $resource->getTableName('made_s3_resize_guard');
        $query = "SELECT * FROM $tableName WHERE original_image_path = :original_image_path AND resized_image_path = :resized_image_path";
        $result = $readConnection->query($query, array(
            'original_image_path' => $observer->getResult()->getBaseFilename(),
            'resized_image_path' => $observer->getResult()->getNewFilename(),
        ));
        $row = $result->fetch();
        if ($row !== false && count($row)) {
            $observer->getEvent()->getResult()->setIsCached(true);
        } else {
            $observer->getEvent()->getResult()->setIsCached(false);
        }
    }

    /**
     * Save a reference to the cached image in the database
     *
     * @param Varien_Event_Observer $observer
     */
    public function madeImageSaveFile(Varien_Event_Observer $observer)
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $tableName = $resource->getTableName('made_s3_resize_guard');
        $query = "INSERT IGNORE INTO $tableName (original_image_path, resized_image_path) VALUES (:original_image_path, :resized_image_path)";
        $writeConnection->query($query, array(
            'original_image_path' => $observer->getBaseFilename(),
            'resized_image_path' => $observer->getNewFilename(),
        ));
    }

    /**
     * Replace s3:// with kraken:// so that resized product images actually
     * get kraked
     *
     * @param Varien_Event_Observer $observer
     */
    public function madeImageOutputBefore(Varien_Event_Observer $observer)
    {
        $streamWrappers = stream_get_wrappers();
        if (in_array('kraken', $streamWrappers)) {
            $arguments = $observer->getResult()->getArguments();
            $arguments[1] = preg_replace('#^s3:#', 'kraken:', $arguments[1]);
            $observer->getResult()->setArguments($arguments);
        }
    }
}