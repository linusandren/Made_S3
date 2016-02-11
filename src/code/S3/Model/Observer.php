<?php

/**
 * @author jonathan@madepeople.se
 */
class Made_S3_Model_Observer
{
    /**
     * Uploads the image to S3 if a bucket and everything is defined in admin
     *
     * @param Varien_Event_Observer $observer
     */
    public function uploadResizedImageToS3(Varien_Event_Observer $observer)
    {
        if (!Mage::getStoreConfig('system/s3/upload_resized_images')) {
            return;
        }

        try {
            $client = Mage::helper('made_s3')->getClient();
            $bucket = Mage::getStoreConfig('system/s3/bucket_name');
            $destination = $observer->getEvent()->getDestination();

            $baseDir = Mage::getBaseDir();
            $s3Destination = preg_replace("#^$baseDir#", '', $destination);

            $client->putObject(array(
                'Bucket' => $bucket,
                'SourceFile' => $destination,
                'Key' => $s3Destination
            ));
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Set the image cache CDN URL if entered in admin
     *
     * @param Varien_Event_Observer $observer
     */
    public function getImageCdnUrl(Varien_Event_Observer $observer)
    {
        if (!Mage::getStoreConfig('system/s3/upload_resized_images')) {
            return;
        }

        $cdnUrl = Mage::getStoreConfig('system/s3/cdn_url');
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

    /**
     * Uploads a product gallery image to S3. This event is convenient because
     * magento has created the directory structure and everything for us and
     * stored the image on disk. Due to this event we can consider the local
     * file temporary and delete it when the S3 transfer is done.
     *
     * @param Varien_Event_Observer $observer
     */
    public function uploadGalleryImage(Varien_Event_Observer $observer)
    {
        $result = $observer->getResult();
        if ($result['error'] !== 0) {
            // Something went wrong, abort
            return;
        }

        $action = $observer->getAction();

    }
}