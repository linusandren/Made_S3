<?php

/**
 * Overridden to get rid of methods that aren't needed anymore with the S3 or
 * Kraken thing running
 *
 * @author jonathan@madepeople.se
 */
class Made_S3_Model_Product_Image extends Mage_Catalog_Model_Product_Image
{

    /**
     * Just assume the file exists so we can find template errors and also
     * not waste resources
     *
     * @param string $filename
     * @return bool
     */
//    protected function _fileExists($filename)
//    {
//        return true;
//    }
//
//    /**
//     * I guess we can't know if kraken has memory
//     *
//     * @param null $filename
//     * @return bool
//     */
//    protected function _checkMemory($file = NULL)
//    {
//        return true;
//    }
}