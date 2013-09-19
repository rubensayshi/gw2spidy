ALTER TABLE `item`
    ADD `unsellable_flag` TINYINT(1) DEFAULT 0 NOT NULL AFTER `item_sub_type_id`;