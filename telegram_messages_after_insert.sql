CREATE TRIGGER `telegram_messages_after_insert` AFTER INSERT ON `telegram_messages` FOR EACH ROW BEGIN
IF NEW.message_text IS NOT NULL AND LENGTH(NEW.message_text) > 1
THEN
	CALL `bot_save_phrase`(NEW.message_text, NEW.chat_id);
END IF;
END