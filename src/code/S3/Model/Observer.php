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
        $lookupResult = Mage::helper('made_s3')->lookupGuardRow(
            $observer->getResult()->getBaseFilename(),
            $observer->getResult()->getNewFilename()
        );

        if ($lookupResult) {
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
        Mage::helper('made_s3')->insertGuardRow(
            $observer->getBaseFilename(),
            $observer->getNewFilename()
        );
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

    /**
     * Does a lookup for assets in the database
     *
     * @param Varien_Event_Observer $observer
     */
    public function fileExistsCheckDbAssets(Varien_Event_Observer $observer)
    {
        $result = $observer->getResult();
        $lookupResult = Mage::helper('made_s3')->lookupGuardRow(
            $result->getTargetFile(),
            $result->getTargetFile()
        );
        $result->setTargetFileExists($lookupResult);
    }

    /**
     * Fetches the mtime from the database
     *
     * @param Varien_Event_Observer $observer
     */
    public function mergedFileMtime(Varien_Event_Observer $observer)
    {
        $result = $observer->getResult();
        $row = Mage::helper('made_s3')->getGuardRow(
            $result->getTargetFile(),
            $result->getTargetFile()
        );
        if (!empty($row)) {
            $mtime = strtotime($row['created_at']);
            $result->setTargetFileMtime($mtime);
        }

    }

    /**
     * Save the assets file to s3://
     *
     * @param Varien_Event_Observer $observer
     */
    public function saveAssetTargetFile(Varien_Event_Observer $observer)
    {
        $result = $observer->getResult();
        $saveResult = file_put_contents(
            $result->getTargetFile(),
            $result->getFileData(),
            stream_context_create(array(
                's3' => array(
                    'CacheControl' => 'max-age=31536000'
                )
            ))
        );
        if ($saveResult !== false) {
            Mage::helper('made_s3')->insertGuardRow(
                $result->getTargetFile(),
                $result->getTargetFile()
            );
            $result->setFileSaved(true);
        }
    }

    /**
     * Sets the asset base URL if defined in admin (honestly it should be)
     *
     * @param Varien_Event_Observer $observer
     */
    public function setAssetsBaseUrl(Varien_Event_Observer $observer)
    {
        $result = $observer->getResult();

        $baseUrl = Mage::getStoreConfig('system/s3/cdn_url');
        if (!empty($baseUrl)) {
            $result->setBaseUrl($baseUrl);
        }
    }

    /**
     * Sets various options relating to S3 things
     *
     * @param Varien_Event_Observer $observer
     */
    public function setOptions(Varien_Event_Observer $observer)
    {
        $designPackage = Mage::getSingleton('core/design_package');
        $designPackage->setSkipInitMergedDir(true);
        Mage::helper('made_image/image')->setMinimizeStat(true);
    }
}