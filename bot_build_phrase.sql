CREATE PROCEDURE `bot_build_phrase`(
	IN `starting_word` VARCHAR(50),
	IN `chat_id` BIGINT
)
LANGUAGE SQL
NOT DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
COMMENT ''
BEGIN
	DECLARE my_id_doc int DEFAULT 0;
	DECLARE my_random_doc int DEFAULT 0;
   DECLARE word_count int DEFAULT 0;
   DECLARE chance_2_words double DEFAULT 0.7;
   DECLARE chance_3_words double DEFAULT 0.7;
   DECLARE chance_4_words double DEFAULT 0.7;
   DECLARE chance_5_words double DEFAULT 0.7;
   DECLARE chance_6_words double DEFAULT 0.7;
   DECLARE chance_7_words double DEFAULT 0.7;
	DECLARE chance_search_2_words double DEFAULT 0.5;
   DECLARE chance_random_start double DEFAULT 0;
   DECLARE last_word varchar(50);
   DECLARE prev_last_word varchar(50);
   DECLARE my_words varchar(500);
   DECLARE continuar bit DEFAULT 1;
   DECLARE using_2_words bit DEFAULT 0;
   DROP TABLE IF EXISTS words;
 	CREATE TEMPORARY TABLE words 
	(
	 	id int not null AUTO_INCREMENT,
	  	word varchar(50),
  		CONSTRAINT words_pk PRIMARY KEY (id)
	);
 	IF LENGTH(starting_word) <= 3 OR starting_word is null OR RAND() < chance_random_start THEN
 		SELECT CAST(COUNT(*) * RAND() as int) into my_random_doc from bot_words;
 		SELECT id_doc into  my_id_doc from bot_words LIMIT my_random_doc,1;
	ELSE
		SELECT id_doc into  my_id_doc from bot_words WHERE (word_1 = starting_word AND word_2 is not null) 
	 																OR (word_2 = starting_word AND word_3 is not null)
																	OR (word_3 = starting_word AND word_4 is not null)
																	ORDER BY RAND() DESC LIMIT 0,1;
		IF my_id_doc = 0 THEN
			SELECT CAST(COUNT(*) * RAND() as int) into my_random_doc from bot_words;
			SELECT id_doc into  my_id_doc from bot_words LIMIT my_random_doc,1;
		END IF;
 	END IF;
 	SELECT word_1 INTO last_word FROM bot_words where id_doc = my_id_doc;
   INSERT INTO words (word) VALUES (CONCAT(UPPER(LEFT(last_word,1)),SUBSTRING(last_word,2,LENGTH(last_word))));
   SET word_count = word_count +1;
   
   IF last_word NOT LIKE starting_word THEN
   	SET prev_last_word = last_word;
   	SELECT word_2 INTO last_word FROM bot_words where id_doc = my_id_doc;
   	INSERT INTO words (word) VALUES (last_word);
   	SET word_count = word_count +1;
   END IF;
   
   IF last_word NOT LIKE starting_word THEN
   	SET prev_last_word = last_word;
   	SELECT word_3 INTO last_word FROM bot_words where id_doc = my_id_doc;
   	INSERT INTO words (word) VALUES (last_word);
   	SET word_count = word_count +1;
   END IF;
   
   label1: WHILE word_count < 40 AND continuar = 1 
	DO
		SET my_id_doc = 0;
		SET using_2_words = 0;
		IF RAND() < chance_search_2_words THEN
			SELECT id_doc into  my_id_doc from bot_words WHERE word_1 = prev_last_word AND word_2 = last_word AND word_3 is not null ORDER BY RAND() DESC LIMIT 0,1;
			IF my_id_doc > 0 THEN
				SET using_2_words = 1;
			END IF;
		END IF;

		IF my_id_doc = 0 THEN
			SELECT id_doc into my_id_doc from bot_words WHERE word_1 = last_word AND word_2 is not null ORDER BY RAND() DESC LIMIT 0,1;
		END IF;

		IF my_id_doc = 0 THEN
      	SET last_word = null;
      	SET continuar = 0;
     		ITERATE label1;
      END IF;

		IF using_2_words = 0 THEN
      	SELECT word_2 INTO last_word FROM bot_words where id_doc = my_id_doc;
	      IF last_word is NULL THEN
				SET continuar = 0;
	     		ITERATE label1;
	      END IF;
	   	INSERT INTO words (word) VALUES (last_word);
	   	SET word_count = word_count +1;
	  	END IF;

      IF (using_2_words = 1 OR RAND() < chance_2_words) AND continuar = 1 THEN
			SELECT word_3 INTO last_word FROM bot_words where id_doc = my_id_doc;
			IF last_word is NULL THEN
				SET continuar = 0;
        		ITERATE label1;
			END IF;
			INSERT INTO words (word) VALUES (last_word);
			SET word_count = word_count +1;
			IF RAND() <= chance_3_words AND continuar = 1 THEN
				SELECT word_4 INTO last_word FROM bot_words where id_doc = my_id_doc;
				IF last_word is NULL THEN
					SET continuar = 0;
					ITERATE label1;
				END IF;
				INSERT INTO words (word) VALUES (last_word);
				SET word_count = word_count +1;
				IF RAND() <= chance_4_words AND continuar = 1 THEN
					SELECT word_5 INTO last_word FROM bot_words where id_doc = my_id_doc;
					IF last_word is NULL THEN
						SET continuar = 0;
						ITERATE label1;
					END IF;
					INSERT INTO words (word) VALUES (last_word);
					SET word_count = word_count +1;
					IF RAND() <= chance_5_words AND continuar = 1 THEN
						SELECT word_6 INTO last_word FROM bot_words where id_doc = my_id_doc;
						IF last_word is NULL THEN
							SET continuar = 0;
							ITERATE label1;
						END IF;
						INSERT INTO words (word) VALUES (last_word);
						SET word_count = word_count +1;
						IF RAND() <= chance_6_words AND continuar = 1 THEN
							SELECT word_7 INTO last_word FROM bot_words where id_doc = my_id_doc;
							IF last_word is NULL THEN
								SET continuar = 0;
								ITERATE label1;
							END IF;
							INSERT INTO words (word) VALUES (last_word);
							SET word_count = word_count +1;
							IF RAND() <= chance_7_words AND continuar = 1 THEN
								SELECT word_8 INTO last_word FROM bot_words where id_doc = my_id_doc;
								IF last_word is NULL THEN
									SET continuar = 0;
									ITERATE label1;
								END IF;
								INSERT INTO words (word) VALUES (last_word);
								SET word_count = word_count +1;
							END IF;
						END IF;
					END IF;
				END IF;
			END IF;
		END IF;
	END WHILE label1;  
	SELECT GROUP_CONCAT(word SEPARATOR ' ') into my_words
	FROM words;
	IF starting_word = my_words THEN
		CALL bot_build_phrase(null,chat_id);
	ELSE
		SELECT my_words;
	END IF;
END