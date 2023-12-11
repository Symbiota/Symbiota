CREATE TABLE `users_thirdpartyauth` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(10) UNSIGNED NOT NULL,
  `sub_uuid` VARCHAR(100) NOT NULL,
  `provider` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_users_uid`
    FOREIGN KEY (`uid`)
    REFERENCES `users` (`uid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);
