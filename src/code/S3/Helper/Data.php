<?php

/**
 * S3 related helper functions
 *
 * @author jonathan@madepeople.se
 */
class Made_S3_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected static $_client;

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

    /**
     * Fetches a row from the guard table
     *
     * @param $source
     * @param $target
     * @return mixed
     */
    public function getGuardRow($source, $target)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $tableName = $resource->getTableName('made_s3_storage_guard');
        $query = "SELECT * FROM $tableName WHERE BINARY source_path = :source_path AND BINARY target_path = :target_path";
        $result = $readConnection->query($query, array(
            'source_path' => $source,
            'target_path' => $target,
        ));
        $row = $result->fetch();
        return $row;
    }

    /**
     * Looks up a row in the guard table
     *
     * @param $source
     * @param $target
     * @return boolean
     */
    public function lookupGuardRow($source, $target)
    {
        $row = $this->getGuardRow($source, $target);
        $lookupResult = $row !== false && count($row);
        return $lookupResult;
    }

    /**
     * Inserts a row into the guard table
     *
     * @param $source
     * @param $target
     */
    public function insertGuardRow($source, $target)
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $tableName = $resource->getTableName('made_s3_storage_guard');
        $query = "REPLACE INTO $tableName (source_path, target_path) VALUES (:source_path, :target_path)";
        $writeConnection->query($query, array(
            'source_path' => $source,
            'target_path' => $target,
        ));
    }
}