<?php
namespace TelegramBot;
class Utility 
{
	public static function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}
	public static function contains($haystack, $needle)
	{
		$pos = strpos($haystack, $needle);
		if ($pos === false) 
		{
			return false;
		} 
		else
		{
			return true;
		}
	}
}

?>