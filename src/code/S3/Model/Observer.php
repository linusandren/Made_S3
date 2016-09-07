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
}