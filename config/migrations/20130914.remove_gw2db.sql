ALTER TABLE `item`
    DROP `gw2db_id`,
    DROP `gw2db_external_id`;

ALTER TABLE `recipe`
    DROP `gw2db_id`,
    DROP `gw2db_external_id`;

DROP TABLE `gw2db_item_archive`;