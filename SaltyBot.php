<?php
namespace TelegramBot;
require_once 'Bot.php';
class SaltyBot extends Bot 
{
	protected function chooseAction($object) 
	{
		$bot_user = $this->alias;
		$chat_id = $object['message']['chat']['id'];
		$reply_to_user_id = $object['message']['reply_to_message']['from']['id'];
		$reply_to_message_id = $object['message']['reply_to_message']['message_id'];
		$user_id = $object['message']['from']['id'];
		$message_id = $object['message']['message_id'];
		$message_text = $object['message']['text'];
		if(Utility::startsWith($message_text,"/")) 
		{

			$arr = explode(' ',trim($message_text));
			$command =  str_replace(strtolower($bot_user),'',strtolower($arr[0]));
			switch ($command) 
			{
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
				case "/frase":
					if($reply_to_message_id != null) 
					{
						$this->getQuote($chat_id,$reply_to_user_id);
					}
					else 
					{
						$this->getQuote($chat_id,$user_id);
					}
				break;
				case "/pincho":
				case "/pinxo":
				case "/pinx0":
					$this->getQuote($chat_id,6424216);
				break;
				case "/doncque":
					$this->getQuote($chat_id,462889188);
				break;
				case "/emilio":
				case "/anthorath":
					$this->getQuote($chat_id,87320750);
				break;
				case "/alberto":
				case "/ignizar":
					$this->getQuote($chat_id,11182188);
				break;
				case "/tony":
					$this->getQuote($chat_id,124892599);
				break;
				case "/hitler":
					$this->getQuote($chat_id,686083718);
				break;
				case "/bertus":
					$this->getQuote($chat_id,306136952);
				break;
				case "/fer":
				case "/fernando":
				case "/necotheone":
					$this->getQuote($chat_id,5969146);
				break;
				case "/rod":
					$this->getQuote($chat_id,528504533);
				break;
				case "/carlos":
					$this->getQuote($chat_id,2400829);
				break;
				case "/pablo":
				case "/ciruelete":
					$this->getQuote($chat_id,11631659);
				break;
				case "/kassimer":
					$this->getQuote($chat_id,143968347);
				break;
				case "/guardar":
					if($reply_to_message_id != null) 
					{
						$this->replyToMessage($chat_id, utf8_decode($this->conversation->saveQuote($object)),$message_id);
					}
					else 
					{
						$this->replyToMessage($chat_id, utf8_decode("Qué cojones quieres que guarde, subnormal?"),$message_id);
					}
					break;
				case "/borrar":
					if($reply_to_message_id != null) 
					{
						$this->replyToMessage($chat_id, utf8_decode($this->conversation->deleteQuote($object)),$message_id);
					}
					else 
					{
						$this->replyToMessage($chat_id, utf8_decode("Qué cojones quieres que borre, retrasado?"),$message_id);
					}
				break;
				case "/top":
					if(sizeof($arr)>1) 
					{
						$numero = (int)$arr[1];
						if($numero > 20) $numero = 20;
						if($numero <= 0) $numero = 1;
					}
					else
					{
						$numero = 5;
					}
					$this->replyToMessage($chat_id, utf8_decode($this->getTop($numero)),$message_id);
				break;
			}
		} 
		else
		{
			$this->trySpeak($object);
		}
		return;
	}
	protected function getQuote($chat_id, $user_id) 
	{
		$response = $this->conversation->getQuote($user_id);
		if(is_array($response))
		{
			$message_id_forward = $response[0];
			$chat_id_forward = $response[1];
			$this->forwardMessage($chat_id, $message_id_forward, $chat_id_forward );
		}
		else 
		{
			$this->sendMessage($chat_id, utf8_decode($response));
		}
		return;
	}
	protected function getTop($limit) 
	{
		$spreadsheet_url= "https://docs.google.com/spreadsheets/d/e/2PACX-1vSbcNpYMdklMXZD4T5ejefZp_5CRtrWTJ2N2UDyhlPclVK0Yfkm_Fi5QsAwN5LyAMqn78ST868mzJCl/pub?gid=1867633616&single=true&output=csv";
		if (($handle = fopen($spreadsheet_url, "r")) !== FALSE) 
		{
			
			$fila = 0;
			$info = array();
			while (($line = fgetcsv($handle)) !== FALSE) 
			{
				if($fila > 1 && $fila < 25) 
				{
					$info[$line[1]] =  ((float)str_replace(',','.',$line[count($line)-1]))/100;
				}
			  $fila++;
			}
			fclose($handle);
			arsort($info);
			$text = '';
			$i = 0;
			foreach($info as $key => $val) 
			{
				$i++;
				$current_text = "TOP " . $i . " - " . $key . ": " . $val*100 . "%";
				$current_text .= "\r\n";
				$text .= $current_text;
				if($i >= $limit) break;
			}
			$text .= "\r\nVer más en: https://docs.google.com/spreadsheets/d/153r0aVwkbg5vIDqU5rMCSn5MnKS0BfbPknssQ6_X75I/edit?usp=sharing";
			return $text;
		}
		return "No se ha podido conectar con google docs";
	}
}
?>