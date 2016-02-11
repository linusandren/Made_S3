<?php

/**
 * S3 storage definition
 *
 * @author jonathan@madepeople.se
 */
class Made_S3_Model_File_Storage extends Mage_Core_Model_File_Storage
{
    const STORAGE_MEDIA_S3 = 3;

    /**
     * Overridden to add support for the S3 storage model
     *
     * @param null $storage
     * @param array $params
     * @return bool|false|Mage_Core_Model_Abstract
     */
    public function getStorageModel($storage = null, $params = array())
    {
        if ($storage === self::STORAGE_MEDIA_S3) {
            $model = Mage::getModel('core/file_storage_database', array('connection' => $connection));
        } else {
            $model = parent::getStorageModel($storage, $params);
            return $model;
        }
    }

}