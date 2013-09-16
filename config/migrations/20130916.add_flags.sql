ALTER TABLE `item`
    ADD `pvp_flag` TINYINT(1) DEFAULT 0 NOT NULL AFTER `item_sub_type_id`;
ALTER TABLE `item`
    ADD `soulbound_flag` TINYINT(1) DEFAULT 0 NOT NULL AFTER `pvp_flag`;