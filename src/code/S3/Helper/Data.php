<?php

/**
 * S3 related helper functions
 *
 * @author jonathan@madepeople.se
 */
class Made_S3_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected static $_client;

    protected $_krakenSettings;

    /**
     * Return a (cached) S3 client instance
     */
    public static function getClient()
    {
        return self::$_client;
    }

    /**
     * Set the S3 client instance
     *
     * @param type $client
     */
    public static function setClient(Aws\S3\S3Client $client)
    {
        self::$_client = $client;
    }

    /**
     * Used to fetch the CDN url of the resized version of the image
     *
     * @param $image
     * @param $settingKey
     */
    public function getImageCdnUrl($image, $settingKey)
    {
        $filename = basename($image);
        $setting = $this->getKrakenSetting($settingKey);
        $path = $this->getKrakenResizePath($setting) . $filename;
        $cdnUrl = Mage::getStoreConfig('system/s3/cdn_url');
        $url = $cdnUrl . $path;
        return $url;
    }
}