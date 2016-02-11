<?php

/**
 * Generate options for media storage selection, including S3
 *
 * @author jonathan@madepeople.se
 */
class Made_S3_Model_System_Config_Source_Storage_Media_Storage
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Core_Model_File_Storage::STORAGE_MEDIA_FILE_SYSTEM,
                'label' => Mage::helper('adminhtml')->__('File System')
            ),
            array(
                'value' => Mage_Core_Model_File_Storage::STORAGE_MEDIA_DATABASE,
                'label' => Mage::helper('adminhtml')->__('Database')
            ),
            array(
                'value' => Made_S3_Model_File_Storage::STORAGE_MEDIA_S3,
                'label' => Mage::helper('adminhtml')->__('Amazon S3')
            )
        );
    }

}