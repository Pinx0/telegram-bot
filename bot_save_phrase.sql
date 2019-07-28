CREATE PROCEDURE `bot_save_phrase`(
	IN `phrase` TEXT
,	IN `chat_id` BIGINT
)
LANGUAGE SQL
NOT DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
COMMENT ''
BEGIN
	DECLARE current_word int DEFAULT 0;
	DECLARE total_words int DEFAULT 0;
	DECLARE my_phrase text;
	SET my_phrase = phrase;
	loop1: WHILE LENGTH(REPLACE(my_phrase, '  ', ' ')) <> LENGTH(my_phrase) DO
		SET my_phrase = REPLACE(my_phrase, '  ', ' ');
	END WHILE loop1;
	loop2: WHILE LENGTH(REPLACE(my_phrase, '\r\n\r\n', '\r\n')) <> LENGTH(my_phrase) DO
		SET my_phrase = REPLACE(my_phrase, '\r\n\r\n', '\r\n');
	END WHILE loop2;
	loop3: WHILE LENGTH(REPLACE(my_phrase, '\n\n', '\n')) <> LENGTH(my_phrase) DO
		SET my_phrase = REPLACE(my_phrase, '\n\n', '\n');
	END WHILE loop3;
	loop4: WHILE LENGTH(REPLACE(my_phrase, '\r\r', '\r')) <> LENGTH(my_phrase) DO
		SET my_phrase = REPLACE(my_phrase, '\r\r', '\r');
	END WHILE loop4;
	SET my_phrase = RTRIM(LTRIM(my_phrase));
	SET total_words = LENGTH(my_phrase) - LENGTH(REPLACE(my_phrase, ' ', '')) + 1;
	SET current_word = 1;
	label1: WHILE current_word <= total_words DO
		INSERT INTO bot_words (chat_id, word_1, word_2, word_3, word_4, word_5, word_6, word_7, word_8)
		SELECT chat_id, SUBSTRING_INDEX(SUBSTRING_INDEX(my_phrase, ' ', current_word), ' ', -1) as PrimeraPalabra,
		CASE WHEN current_word + 1 > total_words THEN null ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(my_phrase, ' ', current_word + 1), ' ', -1) END as SegundaPalabra,
		CASE WHEN current_word + 2 > total_words THEN null ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(my_phrase, ' ', current_word + 2), ' ', -1) END as TerceraPalabra,
		CASE WHEN current_word + 3 > total_words THEN null ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(my_phrase, ' ', current_word + 3), ' ', -1) END as CuartaPalabra,
		CASE WHEN current_word + 4 > total_words THEN null ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(my_phrase, ' ', current_word + 4), ' ', -1) END as QuintaPalabra,
		CASE WHEN current_word + 5 > total_words THEN null ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(my_phrase, ' ', current_word + 5), ' ', -1) END as SextaPalabra,
		CASE WHEN current_word + 6 > total_words THEN null ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(my_phrase, ' ', current_word + 6), ' ', -1) END as SeptimaPalabra,
		CASE WHEN current_word + 7 > total_words THEN null ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(my_phrase, ' ', current_word + 7), ' ', -1) END as OctavaPalabra;
		SET current_word = current_word +1;
	END WHILE label1;  
END