<?

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


$bot = 6;
//BOT FOR WICKED QUIZ
$sett = DB::$dbs->queryFetch("SELECT * FROM ".SETT." WHERE `id` = ?",array(1));  
$interval = (empty($sett['vict_interval_zloy']) ? 10 : $sett['vict_interval_zloy']);
//GET TIME
$fd = fopen("bots/second_bot/time.dat", "r");
if(!$fd) return;
$buff_time = intval(fgets($fd));
fclose($fd);

//GET ACTION
$fd = fopen("bots/second_bot/action.dat", "r");
if(!$fd) $buff_action = "unknown";
else $buff_action = fgets($fd);
fclose($fd);

if($buff_time < time() && $buff_action != 0)
{
if($buff_action != 0) $action = 3;
}
else
{
if($buff_action == 0 && ($buff_time - time() < 0)) $action = 0;
if($buff_action == 1 && ($buff_time - time() < 135)) $action = 1;
if($buff_action == 2 && ($buff_time - time() < 75)) $action = 2;
if($buff_action == 3 && ($buff_time - time() < 15)) $action = 3;
}

//FIRST START
if($buff_time == 0) $action = 0;

switch($action)
{
//NEW QUESTION
case '0':
$ques_arr = DB::$dbs->queryFetch("SELECT * FROM `chat_vopros` ORDER BY RAND() LIMIT 1;");
$question = $ques_arr['vopros'];
$answer = $ques_arr['otvet'];
$tran_answer = $ques_arr['translit'];
$answer = iconv('utf-8', 'windows-1251', $answer);
$length = strlen($answer);
$answer = iconv('windows-1251', 'utf-8', $answer);

$question = "$question (".($length + 2)." букв, да не, напиздил $length букв или ".rand(2, 8).", не помню)";

DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($question, $bot, 0, 0, time(), $room['id'], 0, 0, 0));  
$umnik = DB::$dbs->queryFetch("SELECT `chat_post` FROM ".USERS." WHERE `id` = ?",array($bot));
DB::$dbs->query("UPDATE ".USERS." SET `chat_post` = ? WHERE `id` = ?",array((++$umnik['chat_post']), $bot));

$fd = fopen("bots/second_bot/question.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, $question);
flock($fd, LOCK_UN);
fclose($fd);

$fd = fopen("bots/second_bot/answer.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, $answer);
flock($fd, LOCK_UN);
fclose($fd);

$fd = fopen("bots/second_bot/translit.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, $tran_answer);
flock($fd, LOCK_UN);
fclose($fd);

$fd = fopen("bots/second_bot/action.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, "1");
flock($fd, LOCK_UN);
fclose($fd);

$fd = fopen("bots/second_bot/time.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, (time() + 180 + $interval)); //TIME FOR ANSWER
flock($fd, LOCK_UN);
fclose($fd);
break;

//FIRST HELP
case '1':
$fd = fopen("bots/second_bot/answer.dat", "r");
$answer = fgets($fd);
fclose($fd);

$answer = iconv('utf-8', 'windows-1251', $answer);
$help = substr($answer, 0, 1);
$answer = iconv('windows-1251', 'utf-8', $answer);
$help = iconv('windows-1251', 'utf-8', $help);
$help = "Подсказка, дебилы: $help...";

DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($help, $bot, 0, 0, time(), $room['id'], 0, 0, 0));  
$umnik = DB::$dbs->queryFetch("SELECT `chat_post` FROM ".USERS." WHERE `id` = ?",array($bot));
DB::$dbs->query("UPDATE ".USERS." SET `chat_post` = ? WHERE `id` = ?",array((++$umnik['chat_post']), $bot));

$fd = fopen("bots/second_bot/action.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, "2");
flock($fd, LOCK_UN);
fclose($fd);
break;

//SECOND HELP
case '2':
$fd = fopen("bots/second_bot/answer.dat", "r");
$answer = fgets($fd);
fclose($fd);

$answer = iconv('utf-8', 'windows-1251', $answer);
$help = substr($answer, 0, 2);
$answer = iconv('windows-1251', 'utf-8', $answer);
$help = iconv('windows-1251', 'utf-8', $help);
$help = "Подсказка нах: $help...";

DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($help, $bot, 0, 0, time(), $room['id'], 0, 0, 0));  
$umnik = DB::$dbs->queryFetch("SELECT `chat_post` FROM ".USERS." WHERE `id` = ?",array($bot));
DB::$dbs->query("UPDATE ".USERS." SET `chat_post` = ? WHERE `id` = ?",array((++$umnik['chat_post']), $bot));

$fd = fopen("bots/second_bot/action.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, "3");
flock($fd, LOCK_UN);
fclose($fd);
break;

//THE ANSWER WAS NOT
case '3':
$fd = fopen("bots/second_bot/answer.dat", "r");
$answer = fgets($fd);
fclose($fd);
$message = "Время истекло, кретины! А нужно было сказать: <b>$answer</b>. Идиоты, задам следующий вопрос через ".$interval." секунд!";
DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($message, $bot, 0, 0, time(), $room['id'], 0, 0, 0));  
$umnik = DB::$dbs->queryFetch("SELECT `chat_post` FROM ".USERS." WHERE `id` = ?",array($bot));
DB::$dbs->query("UPDATE ".USERS." SET `chat_post` = ? WHERE `id` = ?",array((++$umnik['chat_post']), $bot));

$fd = fopen("bots/second_bot/time.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, (time() + $interval)); //NEXT QUESTION
flock($fd, LOCK_UN);
fclose($fd);

$fd = fopen("bots/second_bot/action.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, "0");
flock($fd, LOCK_UN);
fclose($fd);
break;
}
?>