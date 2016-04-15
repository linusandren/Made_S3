<?php

/**
 * Overridden to do stuff like fetch the directory listings and so on to use
 * with the media browser.
 *
 * @author jonathan@madepeople.se
 */
class Made_S3_Model_Cms_Wysiwyg_Images_Storage
    extends Mage_Cms_Model_Wysiwyg_Images_Storage
{

    public function deleteDirectory($path)
    {
        $s3Client = Made_S3_Helper_Data::getClient();
        if ($s3Client === null) {
            return parent::deleteDirectory($path);
        }

        // prevent accidental root directory deleting
        $rootCmp = rtrim($this->getHelper()->getStorageRoot(), DS);
        $pathCmp = rtrim($path, DS);

        if ($rootCmp == $pathCmp) {
            Mage::throwException(Mage::helper('cms')->__('Cannot delete root directory %s.', $path));
        }

        unlink($path);
    }

    /**
     * Simply iterate over the S3 objects using the stream wrapper, only
     * what we consider directories
     *
     * @param string $path
     * @return Varien_Data_Collection|Varien_Data_Collection_Filesystem
     */
    public function getDirsCollection($path)
    {
        if (Made_S3_Helper_Data::getClient() === null) {
            return parent::getDirsCollection($path);
        }

        $collection = new Varien_Data_Collection;
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if (!preg_match('#/#', $file)) {
                // Only add directories
                continue;
            }
            $collection->addItem(new Varien_Object(array(
                'basename' => basename($file),
                'filename' => $file,
            )));
        }
        closedir($handle);

        return $collection;
    }

    /**
     * Simply iterate over the S3 objects using the stream wrapper, only
     * what we consider files
     *
     * @param string $path
     * @param string $type
     * @return Varien_Data_Collection|Varien_Data_Collection_Filesystem
     */
    public function getFilesCollection($path, $type = null)
    {
        if (Made_S3_Helper_Data::getClient() === null) {
            return parent::getFilesCollection($path, $type);
        }

        $helper = $this->getHelper();

        $collection = new Varien_Data_Collection;
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if (preg_match('#/#', $file)) {
                // Only add files
                continue;
            }
            if ($file === '.mkdir') {
                // Skip this one
                continue;
            }

            $item = new Varien_Object(array(
                'basename' => basename($file),
                'filename' => $file,
            ));

            $item->setId($helper->idEncode($item->getBasename()));
            $item->setName($item->getBasename());
            $item->setShortName($helper->getShortFilename($item->getBasename()));
            $item->setUrl($helper->getCurrentUrl() . $item->getBasename());

            if ($this->isImage($item->getBasename())) {
                $thumbUrl = $item->getUrl();
            } else {
                $thumbUrl = Mage::getDesign()->getSkinBaseUrl() . self::THUMB_PLACEHOLDER_PATH_SUFFIX;
            }

            $item->setThumbUrl($thumbUrl);

            $collection->addItem($item);
        }
        closedir($handle);

        return $collection;
    }
}