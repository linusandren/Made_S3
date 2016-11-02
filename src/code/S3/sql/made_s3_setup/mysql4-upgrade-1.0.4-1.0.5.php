<?php

/**
 * Set the collation case sensitive
 *
 * @author jonathan@madepeople.se
 */

$this->startSetup();

$this->run(<<<QUERY
ALTER TABLE {$this->getTable('made_s3_storage_guard')} COLLATE = latin1_general_cs;
ALTER TABLE {$this->getTable('made_s3_storage_guard')} MODIFY source_path VARCHAR(4096) CHARACTER SET latin1  COLLATE latin1_general_cs;
ALTER TABLE {$this->getTable('made_s3_storage_guard')} MODIFY target_path VARCHAR(4096) CHARACTER SET latin1  COLLATE latin1_general_cs;
QUERY
);

$this->endSetup();