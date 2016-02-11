<?php

/**
 * Synchronize template that adds support for the S3 settings and doesn't
 * that the button is clicked when S3 is chosen.
 *
 * @author jonathan@madepeople.se
 */
class Made_S3_Block_System_Config_System_Storage_Media_Synchronize
    extends Mage_Adminhtml_Block_System_Config_System_Storage_Media_Synchronize
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('made/s3/system/config/system/storage/media/synchronize_s3.phtml');
    }
}