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
        $config = Mage::getConfig();
        $s3 = $config->getNode('global/s3');
        if ($s3 !== false) {
            $active = (int)$s3->active;
            if ($active === 1) {
                $cdnUrl = Mage::getStoreConfig('system/media_storage_configuration/s3_cdn_url');
                if (empty($cdnUrl)) {
                    return;
                }

                $result = $observer->getEvent()->getResult();
                $imageInstance = $observer->getEvent()->getImageInstance();
                $baseDir = Mage::getBaseDir();
                $path = str_replace($baseDir . DS, "", $imageInstance->getNewFile());
                $mediaUrl = $cdnUrl . $path;
                $result->setUrl($mediaUrl);
            }
        }
    }
}