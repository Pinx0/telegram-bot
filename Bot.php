<?php
namespace TelegramBot;
require_once 'Utility.php';
require_once 'DBConversation.php';
class Bot 
{
	protected $token;
	protected $user_id;
	protected $alias;
	protected $conversation;
	public function __construct($token, $user_id, $alias) 
	{
		$this->token = $token;
		$this->user_id = $user_id;
		$this->alias = $alias;
	}
	public function setDatabaseConnection($host, $user, $password, $database) 
	{
		$this->conversation = new DBConversation($host, $user, $password, $database);
		return;
	}
	public function handle($object)
	{
		$this->conversation->saveMessageToDatabase($object);
		$this->chooseAction($object);
		$this->conversation->endConversation();
		return;
	}
	protected function chooseAction($object) 
	{
		$bot_user = $this->alias;
		$chat_id = $object['message']['chat']['id'];
		$message_id = $object['message']['message_id'];
		$message_text = $object['message']['text'];
		if(Utility::startsWith($message_text,"/")) 
		{
			$arr = explode(' ',trim($message_text));
			$command =  str_replace(strtolower($bot_user),'',strtolower($arr[0]));
			switch ($command) {
				case "/setspeakchance":
					$new_speak_chance = $arr[1];
					if(sizeof($arr)<2) 
					{
						$this->replyToMessage($chat_id, utf8_decode("Especifique la probabilidad de hablar a continuación del comando. Ej: /setspeakchance 0.1"), $message_id);
					}
					else if(!is_numeric($new_speak_chance)) 
					{
						$this->replyToMessage($chat_id, utf8_decode("Debe introducir un valor numérico"), $message_id);
					}
					else if($new_speak_chance < 0) 
					{
						$this->replyToMessage($chat_id, "La probabilidad debe ser mayor que 0", $message_id);
					} 
					else if($new_speak_chance > 1) 
					{
						$this->replyToMessage($chat_id, "La probabilidad no puede ser mayor que 1 (que es el 100%)", $message_id);
					}
					else 
					{
						$this->setSpeakChance($new_speak_chance);
						$this->replyToMessage($chat_id, "Probabilidad de hablar establecida correctamente en un ".($new_speak_chance*100)."%", $message_id);
					}
				break;
				case "/mode":
					if(sizeof($arr)<2) 
					{
						$current_mode = $this->conversation->getMode($chat_id);
						if($current_mode) 
						{
							$this->replyToMessage($chat_id, utf8_decode("Modo global: el bot usará lo que haya aprendido en todos los grupos.\n\nUsa /switchmode para cambiarlo."), $message_id);
						} 
						else
						{
							$this->replyToMessage($chat_id, utf8_decode("Modo privado: el bot usará lo que haya aprendido en este grupo solamente.\n\nUsa /switchmode para cambiarlo."), $message_id);
						}
					}
				break;
				case "/switchmode":
						
					$new_mode = $this->conversation->switchMode($chat_id);
					if($new_mode) 
					{
						$this->replyToMessage($chat_id, utf8_decode("Ahora el bot usará lo que haya aprendido en todos los grupos."), $message_id);
					}
					else 
					{
						$this->replyToMessage($chat_id, utf8_decode("Ahora el bot solo usará lo aprendido en este grupo."), $message_id);
					}
				break;
			}
		} 
		else
		{
			$this->trySpeak($object);
		}
		return;
	}

	
	protected function trySpeak($object) 
	{
		$chat_id = $object['message']['chat']['id'];
		$user_id = $object['message']['from']['id'];
		$reply_to_message_id = $object['message']['reply_to_message']['message_id'];
		$reply_to_user_id = $object['message']['reply_to_message']['from']['id'];
		$message_id = $object['message']['message_id'];
		$bot_id = $this->user_id;
		$bot_alias = $this->alias;
		$message_text = $object['message']['text'];
		$words = explode(' ',trim(str_replace(strtolower($bot_alias),'',strtolower($message_text))));
		$possible_words = array();
		foreach($words as $word) 
		{
			if(strlen($word)>=3) 
			{
				array_push($possible_words,$word);
			}
		}
		$selected_word = '';
		$valor = sizeof($possible_words);
		//$this->sendMessage(6424216,utf8_decode("Palabras posibles: $valor"));
		if(sizeof($possible_words)>0) 
		{
			$random_array = mt_rand (0, sizeof($possible_words)-1); 
			//$this->sendMessage(6424216,utf8_decode("Posicion elegida: $random_array"));
			$selected_word = $possible_words[$random_array];
		}
		//$this->sendMessage(6424216,utf8_decode("Palabra elegida: $selected_word"));
		//Si lo mencionan o contestan a un mensaje suyo, respondemos citando a ese mensaje
		if(Utility::contains(strtolower($message_text),strtolower($this->alias)) || $reply_to_user_id == $bot_id ) 
		{
			$response = $this->conversation->getResponse($chat_id, $selected_word);
			$this->replyToMessage($chat_id, $response, $message_id);
		} 	
		else 
		{
			//Hacemos el roll para ver si le toca hablar
			$random = mt_rand() / mt_getrandmax();
			//Vemos la probabilidad de hablar en este grupo
			$speak_chance = $this->conversation->getSpeakChance($chat_id);
			if($random < $speak_chance) 
			{
				$response =  $this->conversation->getResponse($chat_id, $selected_word);
				$this->sendMessage($chat_id, $response);
			}
		}
		return;
	}
	public function sendMessage($chat_id, $text)
	{
		$token = $this->token;
		$data = [
			'chat_id' => $chat_id,
			'text' => utf8_encode($text)
		];
		$response = file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query($data) );
		return;
	}
	public function replyToMessage($chat_id, $text, $reply_to_message_id)
	{
		$token = $this->token;
		$data = [
			'chat_id' => $chat_id,
			'text' => utf8_encode($text),
			'reply_to_message_id' => $reply_to_message_id
		];
		$response = file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query($data) );
		return;
	}
	public function forwardMessage($chat_id, $message_id, $original_chat_id)
	{
		$token = $this->token;
		$data = [
			'chat_id' => $chat_id,
			'from_chat_id' => $original_chat_id,
			'message_id' => $message_id
		];
		$response = file_get_contents("https://api.telegram.org/bot$token/forwardMessage?" . http_build_query($data) );
		return;
	}
	
}
?>