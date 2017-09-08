
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- free_delivery_condition
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `free_delivery_condition`;

CREATE TABLE `free_delivery_condition`
(
    `area_id` INTEGER NOT NULL,
    `module_id` INTEGER NOT NULL,
    `amount` DECIMAL(16,6) NOT NULL,
    PRIMARY KEY (`area_id`,`module_id`),
    INDEX `FI_free_delivery_condition_module_id` (`module_id`),
    CONSTRAINT `fk_free_delivery_condition_area_id`
        FOREIGN KEY (`area_id`)
        REFERENCES `area` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_free_delivery_condition_module_id`
        FOREIGN KEY (`module_id`)
        REFERENCES `module` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
