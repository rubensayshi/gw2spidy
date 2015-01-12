ALTER TABLE `user`
    ADD `reset_password` VARCHAR(255) DEFAULT '' AFTER `hybrid_auth_id`;
