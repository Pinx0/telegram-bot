CREATE TABLE `telegram_messages` (
	`update_id` BIGINT(20) NOT NULL,
	`message_id` BIGINT(20) NOT NULL,
	`chat_id` BIGINT(20) NOT NULL,
	`user_id` BIGINT(20) NOT NULL,
	`message_date` BIGINT(20) NOT NULL,
	`message_text` TEXT NULL,
	`file_id` VARCHAR(255) NULL DEFAULT NULL,
	`media_type` VARCHAR(50) NULL DEFAULT NULL,
	`doc_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`doc_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;