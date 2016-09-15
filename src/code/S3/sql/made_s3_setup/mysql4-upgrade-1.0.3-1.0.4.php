<?php

/**
 * Renames the guard table to something else because we use it for both assets
 * and images, and maybe other stuff as well in the future
 *
 * @author jonathan@madepeople.se
 */

$this->startSetup();

$this->run(<<<QUERY
RENAME TABLE {$this->getTable('made_s3_resize_guard')} TO {$this->getTable('made_s3_storage_guard')};
ALTER TABLE {$this->getTable('made_s3_storage_guard')} CHANGE original_image_path source_path VARCHAR(4096) NOT NULL;
ALTER TABLE {$this->getTable('made_s3_storage_guard')} CHANGE resized_image_path target_path VARCHAR(4096) NOT NULL;
QUERY
);


$this->endSetup();
