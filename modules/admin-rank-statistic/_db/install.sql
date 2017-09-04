INSERT IGNORE INTO `user_perms` ( `name`, `group`, `role`, `about` ) VALUES
    ( 'read_site_rank', 'Site Rank', 'Management', 'Allow user to view all site rank' ),
    ( 'update_site_rank', 'Site Rank', 'Management', 'Allow user to update site rank' );

CREATE TABLE IF NOT EXISTS `site_rank` (
    `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `vendor` VARCHAR(50) NOT NULL,
    `international` INTEGER DEFAULT 0,
    `local` INTEGER DEFAULT 0,
    `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX `by_vendor` ON `site_rank` ( `vendor` );
