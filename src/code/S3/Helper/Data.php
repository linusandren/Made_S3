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
     * Retrieve the Kraken processing settings
     */
    public function getKrakenSetting($key)
    {
        $settings = $this->getKrakenSettings();
        foreach ($settings as $setting) {
            if ($setting['key'] === $key) {
                return $setting;
            }
        }
        // We could throw an exception, but maybe we don't want stuff to explode.
        return false;
    }

    /**
     * Retrieve the Kraken processing settings
     */
    public function getKrakenSettings()
    {
        if (!$this->_krakenSettings) {
            $settings = Mage::getStoreConfig('system/s3/kraken_settings');
            $this->_krakenSettings = @unserialize($settings);
        }
        return $this->_krakenSettings;
    }

    /**
     * Returns the resize path for a given image, to also be used in templates
     *
     * @param $setting
     */
    public function getKrakenResizePath($setting)
    {
        $parts = array('catalog', 'resize');
        foreach ($setting as $value) {
            if ($value !== '') {
                $parts[] = $value;
            }
        }
        $path = join('/', $parts) . '/';
        return $path;
    }

    /**
     * Converts a kraken:// url to a media url
     *
     * @param $url
     */
    public function convertKrakenUrlToMediaUrl($url)
    {
        $mediaPath = Mage::getConfig()->getOptions()->getMediaDir();
        $url = str_replace($mediaPath, '', $url);
        $url = trim($url, DS);
        $url = Mage::app()->getStore()->getBaseUrl('media') .
            $url;
        return $url;
    }

    /**
     * Used to fetch the CDN url of the resized version of the image
     *
     * @param $image
     * @param $key
     */
    public function getImageCdnUrl($image, $key)
    {
        $filename = basename($image);
        $setting = $this->getKrakenSetting($key);
        $path = $this->getKrakenResizePath($setting) . $filename;
        $cdnUrl = Mage::getStoreConfig('system/s3/cdn_url');
        $url = $cdnUrl . $path;
        return $url;
    }
}