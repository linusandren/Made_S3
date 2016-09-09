<?php

/**
 * Creates the table used to make sure that we do not resize images that
 * have already been resized
 *
 * @author jonathan@madepeople.se
 */

$this->startSetup();

$this->run(<<<QUERY
CREATE TABLE {$this->getTable('made_s3_resize_guard')} (
    original_image_path VARCHAR(4096) NOT NULL,
    resized_image_path VARCHAR(4096) NOT NULL,
    PRIMARY KEY (original_image_path(380), resized_image_path(380))
) ENGINE=InnoDB;
QUERY
);

$this->endSetup();