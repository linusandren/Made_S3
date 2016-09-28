<?php

/**
 * Overridden because realpath doesn't speak s3:// and we need wysiwyg uploads
 * and the whole media browser to work correctly.
 *
 * @author jonathan@madepeople.se
 */
class Made_S3_Helper_Cms_Wysiwyg_Images extends Made_Admin_Helper_Cms_Wysiwyg_Images
{

    /**
     * Also an S3 specific hack
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        if (!$this->_currentUrl) {
            $mediaPath = Mage::getConfig()->getOptions()->getMediaDir();
            if (preg_match('#^s3://#', $mediaPath)) {
                $path = str_replace($mediaPath, '', $this->getCurrentPath());
                $path = trim($path, DS);
                $this->_currentUrl = Mage::app()->getStore($this->_storeId)->getBaseUrl('media') .
                    $this->convertPathToUrl($path) . '/';
            } else {
                return parent::getCurrentUrl();
            }
        }
        return $this->_currentUrl;
    }

    /**
     * Also an S3 specific hack
     *
     * @param string $id
     * @return string
     */
    public function convertIdToPath($id)
    {
        $storageRoot = $this->getStorageRoot();
        $storageRoot = preg_replace('#/$#', '', $storageRoot);
        if (preg_match('#^s3://#', $storageRoot)) {
            $path = $this->idDecode($id);
            $path = preg_replace('#([^:])//#', '$1/', $path);
            if (!strstr($path, $storageRoot)) {
                $path = $storageRoot . DS . $path;
            }
        } else {
            return parent::convertIdToPath($id);
        }
        return $path;
    }

    /**
     * Return path of the current selected directory or root directory for startup
     * Try to create target directory if it doesn't exist
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    public function getCurrentPath()
    {
        if (!$this->_currentPath) {
            $storageRoot = $this->getStorageRoot();
            if (preg_match('#^s3://#', $storageRoot)) {
                // It's actually the same for us
                $currentPath = $storageRoot;
                $node = $this->_getRequest()->getParam($this->getTreeNodeName());
                if ($node && $node !== 'root') {
                    $path = $this->convertIdToPath($node);
                    if (is_dir($path) && false !== stripos($path, $currentPath)) {
                        $currentPath = $path;
                    }
                }
                $this->_currentPath = $currentPath;
            } else {
                parent::getCurrentPath();
            }
        }
        return $this->_currentPath;
    }


    /**
     * Images Storage root directory
     *
     * This is copy pasted from core because the Made_Admin core fix actually
     * breaks S3
     *
     * @return string
     */
    public function getStorageRoot()
    {
        if (!$this->_storageRoot) {
            $path = Mage::getConfig()->getOptions()->getMediaDir()
                . DS . Mage_Cms_Model_Wysiwyg_Config::IMAGE_DIRECTORY;
            $this->_storageRoot = realpath($path);
            if (!$this->_storageRoot) {
                $this->_storageRoot = $path;
            }
            $this->_storageRoot .= DS;
        }
        return $this->_storageRoot;
    }
}