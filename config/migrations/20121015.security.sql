# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `user`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255),
    `roles` VARCHAR(255) DEFAULT \'USER_ROLE\',
    `hybrid_auth_provider_id` VARCHAR(50),
    `hybrid_auth_id` VARCHAR(255),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `unique_username` (`username`)
) ENGINE=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
