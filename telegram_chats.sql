CREATE TABLE `telegram_chats` (
	`chat_id` BIGINT(20) NOT NULL,
	`chat_title` VARCHAR(255) NULL DEFAULT NULL,
	`mode` BIT(1) NOT NULL DEFAULT b'0',
	`speak_chance` DOUBLE NOT NULL DEFAULT '0',
	PRIMARY KEY (`chat_id`),
	UNIQUE INDEX `chat_id` (`chat_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;