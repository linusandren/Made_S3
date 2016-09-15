<?php

/**
 * Adds the timestamp field to the guard table that can be used for the CSS
 * and JS merge files to prevent making lookups on S3
 *
 * @author jonathan@madepeople.se
 */

$this->startSetup();

$this->run(<<<QUERY
ALTER TABLE {$this->getTable('made_s3_resize_guard')} 
ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
QUERY
);

$this->endSetup();
