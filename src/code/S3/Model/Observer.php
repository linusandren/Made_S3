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
            $bucket = Mage::getStoreConfig('system/s3/bucket');
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
}