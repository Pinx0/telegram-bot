<?php
namespace TelegramBot;
class DBConversation 
{
	private $db;
	public function __construct($host, $user, $password, $database) 
	{
		$this->db = new \mysqli($host, $user, $password, $database);
		if ($this->db->connect_errno) 
		{
			printf("Falló la conexión: %s\n", $this->db->connect_error);
			exit();
		}
	}
	public function getSpeakChance($chat_id) 
	{
		if($chat_id > 0) 
		{
			return 1;
		}
		$result = $this->db->query("SELECT speak_chance FROM telegram_chats where chat_id = $chat_id");
		$speak_chance = 0;
		if($result)
		{
			$row = $result->fetch_object();
			$speak_chance = $row->speak_chance;
			$result->close();
		}
		if($speak_chance > 1) $speak_chance = 1;
		if($speak_chance < 0) $speak_chance = 0;
		return $speak_chance;
	}
	public function getResponse($chat_id, $message) 
	{
		$result = $this->db->query("CALL bot_build_phrase('$message',$chat_id)");
		$response = "";
		if($result)
		{
			while ($row = $result->fetch_object())
			{
				$response = $row->my_words;
			}
			$result->close();
			$this->db->next_result();
		}
		return $response;
	}
	public function getMode($chat_id) 
	{
		$result = $this->db->query("SELECT mode FROM telegram_chats where chat_id = $chat_id");
		$mode = false;
		if($result)
		{
			$row = $result->fetch_object();
			$mode_int = $row->mode;
			$result->close();
		}
		if($mode_int == 1) return true;
		else return false;
	}
	public function switchMode($chat_id) 
	{
		$current_mode = $this->getMode($chat_id);
		$new_mode = !$current_mode;
		if($new_mode) $new_mode_int = 1;
		else $new_mode_int = 0;
		$this->db->query("UPDATE telegram_chats SET mode = $new_mode_int WHERE chat_id = $chat_id");
		return $new_mode;
	}
	public function setSpeakChance($chat_id, $new_speak_chance) 
	{
		$this->db->query("UPDATE telegram_chats SET speak_chance = $new_speak_chance WHERE chat_id = $chat_id");
		return;
	}
	public function endConversation()
	{
		$this->db->close();
		return;
	}
	public function getQuote($user_id) 
	{
		$result = $this->db->query("SELECT message_id, chat_id FROM frases WHERE user_id = $user_id ORDER BY RAND() LIMIT 1");
		if($result)
		{
			$row = $result->fetch_object();
			$message_id = $row->message_id;
			$chat_id = $row->chat_id;
			$result->close();
			return array($message_id, $chat_id);
		} 
		else
		{
			return "Pero si tú no has dicho nada interesante en tu puta vida, desgraciado.";
		}
	}
	function saveQuote($object) 
	{
		$reply_to_message_id = $object['message']['reply_to_message']['message_id'];
		$reply_to_text = $object['message']['reply_to_message']['text'];
		$reply_to_chat_id = $object['message']['reply_to_message']['chat']['id'];
		$reply_to_user_id = $object['message']['reply_to_message']['from']['id'];
		$reply_to_date = $object['message']['reply_to_message']['date'];
		$result = $this->db->query("SELECT count(*) as cuenta FROM frases WHERE user_id = $reply_to_user_id, message_id = $reply_to_message_id, chat_id = $reply_to_chat_id");
		if($result)
		{
			$row = $result->fetch_object();
			$cuenta = $row->cuenta;
			$result->close();
		}
		if($cuenta > 0) 
		{
			return "Ese mensaje ya lo tenía guardado, maldito calvo!";
		}
		else
		{
			$texto = addslashes(utf8_decode($reply_to_text));
			$this->db->query("INSERT INTO frases (user_id, content, message_id, chat_id, message_date) VALUES ($reply_to_user_id, '$texto', $reply_to_message_id, $reply_to_chat_id, $reply_to_date)");
			return;
		}
	}
	function deleteQuote($object) 
	{
		$message_id = $object['message']['reply_to_message']['message_id'];
		$chat_id = $object['message']['reply_to_message']['chat']['id'];
		$user_id = $object['message']['reply_to_message']['forward_from']['id'];
		$forward_date = $object['message']['reply_to_message']['forward_date'];
		$filter = "WHERE (message_id = $message_id AND chat_id = $chat_id)";
		if($user_id != null && $forward_date != null) 
		{
			$filter .= " OR (user_id = $user_id AND message_date = $forward_date)";
		}
		
		$result = $this->db->query("SELECT count(*) as cuenta FROM frases $filter");
		if($result)
		{
			$row = $result->fetch_object();
			$cuenta = $row->cuenta;
			$result->close();
		}
		if($cuenta > 0) 
		{
			$this->db->query("DELETE FROM frases $filter");
			return;
		}
		else
		{
			return "Pero si ese mensaje no lo tenía guardado, subnormal! Tienes que citar el mensaje original.";
		}
	}
	public function saveMessageToDatabase($object) 
	{
		
		$update_id = $object['update_id'];

		$message_id = $object['message']['message_id'];
		$message_date = $object['message']['date'];
		$message_text = $object['message']['text'];

		$reply_to_message_id = $object['message']['reply_to_message']['message_id'];
		$reply_to_text = $object['message']['reply_to_message']['text'];
		$reply_to_chat_id = $object['message']['reply_to_message']['chat']['id'];
		$reply_to_user_id = $object['message']['reply_to_message']['from']['id'];

		$forwarded_from_text = $object['message']['reply_to_message']['text'];
		$forwarded_from_user_id = $object['message']['reply_to_message']['forward_from']['id'];
		$forwarded_from_chat_id = $object['message']['reply_to_message']['forward_from_chat']['id'];
		$forwarded_from_message_id = $object['message']['reply_to_message']['forward_from_message_id'];

		$chat_id = $object['message']['chat']['id'];
		$chat_title = $object['message']['chat']['title'];

		$user_id = $object['message']['from']['id'];
		$user_first_name = $object['message']['from']['first_name'];
		$user_last_name = $object['message']['from']['last_name'];
		$user_username = $object['message']['from']['username'];

		$audio_id = $object['message']['audio']['file_id'];
		$animation_id = $object['message']['animation']['file_id'];
		$sticker_id = $object['message']['sticker']['file_id'];
		$video_id = $object['message']['video']['file_id'];
		$voice_id = $object['message']['voice']['file_id'];
		$video_note_id = $object['message']['video_note']['file_id'];
		$archive_id = $object['message']['file']['file_id'];
		$photo_id = $object['message']['photo'][0]['file_id'];
		
		$message_text_slashed = addslashes(utf8_decode($message_text));
		$chat_title_slashed = addslashes(utf8_decode($chat_title));
		$user_first_name_slashed = addslashes(utf8_decode($user_first_name));
		$user_last_name_slashed = addslashes(utf8_decode($user_last_name));
		$user_username_slashed = addslashes(utf8_decode($user_username));
		$media_type = null;
		$file_id = null;
		if($sticker_id != null) 
		{
			$media_type = 'Sticker';
			$file_id = $sticker_id;
		}
		if($animation_id != null) 
		{
			$media_type = 'Animation';
			$file_id = $animation_id;
		}
		if($audio_id != null) 
		{
			$media_type = 'Audio';
			$file_id = $audio_id;
		}
		if($video_id != null) 
		{
			$media_type = 'Video';
			$file_id = $video_id;
		}
		if($voice_id != null) 
		{
			$media_type = 'Voice';
			$file_id = $voice_id;
		}
		if($video_note_id != null) 
		{
			$media_type = 'VideoNote';
			$file_id = $video_note_id;
		}
		if($archive_id != null) 
		{
			$media_type = 'File';
			$file_id = $archive_id;
		}
		if($photo_id != null) 
		{
			$media_type = 'Photo';
			$file_id = $photo_id;
		}
		if($media_type != null) 
		{
			$this->db->query("INSERT INTO telegram_messages (update_id, message_id, chat_id, user_id, message_date, message_text, file_id, media_type) VALUES ($update_id, $message_id, $chat_id, $user_id, $message_date, '$message_text_slashed','$file_id', '$media_type')");
		} 
		else
		{
			$this->db->query("INSERT INTO telegram_messages (update_id, message_id, chat_id, user_id, message_date, message_text) VALUES ($update_id, $message_id, $chat_id, $user_id, $message_date, '$message_text_slashed')");
		}
		$this->db->query("INSERT INTO telegram_chats (chat_id, chat_title) VALUES ($chat_id, '$chat_title_slashed') ON DUPLICATE KEY UPDATE chat_title='$chat_title_slashed'");
		$this->db->query("INSERT INTO telegram_users (user_id, user_first_name, user_last_name, user_username) VALUES ($user_id, '$user_first_name_slashed', '$user_last_name_slashed', '$user_username_slashed') ON DUPLICATE KEY UPDATE user_first_name='$user_first_name_slashed',user_last_name='$user_last_name_slashed', user_username='$user_username_slashed' ");
		return;
	}
}

?>