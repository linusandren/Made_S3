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
                $cdnUrl = Mage::getStoreConfig('system/s3/cdn_url');
                if (empty($cdnUrl)) {
                    return;
                }

                $result = $observer->getEvent()->getResult();
                $imageInstance = $observer->getEvent()->getImageInstance();

                $helper = Mage::helper('made_s3');
                $settingsKey = $helper->getKrakenSettingsKeyFromCoreSettings($imageInstance);
                $mediaUrl = $helper->getImageCdnUrl($imageInstance->getNewFile(), $settingsKey);

                $result->setUrl($mediaUrl);
            }
        }
    }
}