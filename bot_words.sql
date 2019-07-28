CREATE TABLE `bot_words` (
	`id_doc` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`chat_id` BIGINT(20) NOT NULL,
	`word_1` VARCHAR(50) NOT NULL,
	`word_2` VARCHAR(50) NULL DEFAULT NULL,
	`word_3` VARCHAR(50) NULL DEFAULT NULL,
	`word_4` VARCHAR(50) NULL DEFAULT NULL,
	`word_5` VARCHAR(50) NULL DEFAULT NULL,
	`word_6` VARCHAR(50) NULL DEFAULT NULL,
	`word_7` VARCHAR(50) NULL DEFAULT NULL,
	`word_8` VARCHAR(50) NULL DEFAULT NULL,
	`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id_doc`),
	INDEX `ix_word_1_to_5` (`word_1`, `word_2`, `word_3`, `word_4`, `word_5`),
	INDEX `ix_word_3` (`word_3`) USING BTREE,
	INDEX `ix_word_4` (`word_4`) USING BTREE,
	INDEX `ix_word_5` (`word_5`) USING BTREE,
	INDEX `ix_word_2` (`word_2`) USING BTREE,
	INDEX `ix_chat_word_1_to_5` (`chat_id`, `word_1`, `word_2`, `word_3`, `word_4`, `word_5`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;
