CREATE TABLE `telegram_users` (
	`user_id` BIGINT(20) NOT NULL,
	`user_first_name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`user_last_name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`user_username` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	PRIMARY KEY (`user_id`),
	UNIQUE INDEX `user_id` (`user_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;
