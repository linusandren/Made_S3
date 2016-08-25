<?php

require_once 'abstract.php';

/**
 * The purpose of this script is to migrate an existing media collection to
 * kraken with the settings defined in admin
 *
 * @author jonathan@madepeople.se
 */
class Made_S3_Migrate extends Mage_Shell_Abstract
{
    protected $_originalMediaDir;

    protected function _syncCatalogImages()
    {
        $helper = Mage::helper('made_s3');
        $read = Mage::getSingleton('core/resource')
            ->getConnection('catalog_read');

        $productMediaDir = $this->_originalMediaDir . '/catalog/product';

        $sql = 'SELECT value FROM catalog_product_entity_media_gallery';
        $rows = $read->fetchAll($sql);

        $values = array();
        foreach ($rows as $row) {
            $values[] = $row['value'];
        }

        $findData = `find $productMediaDir -type f | grep -v cache | grep -v placeholder`;
        $files = explode("\n", $findData);

        // The last newline from find creates an empty element
        array_pop($files);

        foreach ($files as $file) {
            $fileName = preg_replace("#^$productMediaDir#", '', $file);
            if (in_array($fileName, $values)) {
                // File exists and is referenced, lets throw it at kraken
                echo "$file\n";
            } else {
                // What if we actually also cleaned up unused files
                //unlink($file);
            }
        }
    }

    protected function _traverseMediaDirectory()
    {

    }

    public function run()
    {
        // Before kraken://-ized path
        $this->_originalMediaDir = Mage::getBaseDir('media');

        // Bootstrap the Kraken/S3 settings
        $processor = new Made_S3_Model_Processor;
        $processor->extractContent(false);

        $client = Mage::helper('made_s3')->getClient();
        if ($client === null) {
            throw new Exception('S3 Client not set - S3/Kraken probably not configured');
        }

        $this->_syncCatalogImages();
        $this->_traverseMediaDirectory();
    }
}

$shell = new Made_S3_Migrate();
$shell->run();