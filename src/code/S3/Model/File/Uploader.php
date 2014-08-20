<?php
/**
 * @author jonathan@madepeople.se
 */
class Made_S3_Model_File_Uploader extends Mage_Core_Model_File_Uploader
{
    /**
     * Move files from TMP folder into destination folder
     * 
     * When S3 is chosen as storage, we just make sure the path is correct
     * and store it in the bucket
     *
     * @param string $tmpPath
     * @param string $destPath
     * @return bool
     */
    protected function _moveFile($tmpPath, $destPath)
    {
        if (Mage::helper('made_s3')->useS3()) {
            $bucket = Mage::getStoreConfig('system/s3/bucket');
            $destPath = 's3://' . $bucket . '/' . $destPath;
        }
        return move_uploaded_file($tmpPath, $destPath);
    }
}