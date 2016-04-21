<?php

/**
 * Adds the kraken_status field
 *
 * @author jonathan@madepeople.se
 */

$this->startSetup();

$this->run(<<<QUERY
ALTER TABLE {$this->getTable('catalog_product_entity_media_gallery')} ADD kraken_status TEXT
QUERY
);

$this->endSetup();