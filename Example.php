<?php
namespace TelegramBot;
require 'Bot.php';
$token = "123456:AAAAAAAAAAAAAAAAAAAAAAAA";
$recieved_token = $_GET['token'];
if($recieved_token != $token) { return;}

$json = file_get_contents('php://input');
$obj = json_decode($json,true);
$bot = new Bot($token,123456, "@yourbot_alias");
$bot->setDatabaseConnection("localhost", "yourdb_user", "your_password", "your_db_name");
$bot->handle($obj);

?>