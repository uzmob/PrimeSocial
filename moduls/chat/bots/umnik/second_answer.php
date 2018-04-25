<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


$bot = 6;
// Oskorblenie
if(eregi('Злая викторина', $msg) !== false)
{
$sql = DB::$dbs->queryFetch("SELECT * FROM `chat_bad_answers` ORDER BY RAND() LIMIT 1;");

$message = '<b>' . $user['nick'] . '</b>, ' . $sql['answer'];

DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($message, $bot, 0, 0, time(), $room['id'], 0, 0, 0));  
$umnik = DB::$dbs->queryFetch("SELECT `chat_post` FROM ".USERS." WHERE `id` = ?",array($bot));
DB::$dbs->query("UPDATE ".USERS." SET `chat_post` = ? WHERE `id` = ?",array((++$umnik['chat_post']), $bot));
}
//konec oskorbleniya

if($msg === "!question" or $msg === "!вопрос")
{
	if($buff_action != 0)
	{
	$fd = fopen("bots/second_bot/question.dat", "r");
	$question = fgets($fd);
	fclose($fd);
	$message = "<b>$user[nick]</b>, $question";
        DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($message, $bot, 0, 0, time(), $room['id'], 0, 0, 0));    
	}
	else
	{
	$message = "<b>$user[nick]</b>, блять! Для тупых повторяю: время вышло! Жди следующего вопроса!";
        DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($message, $bot, 0, 0, time(), $room['id'], 0, 0, 0));    
	}
    
     $umnik = DB::$dbs->queryFetch("SELECT `chat_post` FROM ".USERS." WHERE `id` = ?",array($bot));
    DB::$dbs->query("UPDATE ".USERS." SET `chat_post` = ? WHERE `id` = ?",array((++$umnik['chat_post']), $bot));
}

$var = explode(" ", $msg);

if ( ($var[0] == 'статс' || $var[0] == 'stats') && !empty($var[1]) ) {
    $ank = DB::$dbs->queryFetch("SELECT `id`, `zloy_otvet` FROM ".USERS." WHERE `nick` = ?",array(html($var[1])));
    
    if (!empty($ank)) {
        $message = 'У пользователя ' . get_nick($ank['id'], 'page') . ' <b>' . ( empty($ank['zloy_otvet']) ? '0' : $ank['zloy_otvet']) . '</b> правильных ответов.';
        DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($message, $bot, $user['id'], 0, time(), $room['id'], 0, 0, 0));  
    }
} 

$result = substr($msg, 0, 3);
//NATRAVLENIE
if($result == "fas")
{
$nick = mysql_escape_string(substr($msg, 4));

	if(empty($nick))
	{
	$message = "<b>$user[nick]</b>, ты не ввел ник, дебил!";
        DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($message, $bot, 0, 0, time(), $room['id'], 0, 0, 0));    
	}
	else
	{
		if($user['level'] != 8)
		{
		$message = "<b>$user[nick]</b>, пошел на хуй! Не стану я юзера \"".$nick."\" доставать. Ты не админ!";
        DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($message, $bot, 0, 0, time(), $room['id'], 0, 0, 0));  
		}
		else
		{
			if($nick == 'Злая викторина')
			{
			$message = "<b>$user[nick]</b>, а не охуел? Я сам на себя наезжать не собираюсь...";
        DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($message, $bot, 0, 0, time(), $room['id'], 0, 0, 0));  
			}
			elseif($nick == $bots[0])
			{
			$message = "$nickname, ты с ума сошел? Не тронь бота ".$bots[0]."!";
        DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($message, $bot, 0, 0, time(), $room['id'], 0, 0, 0));  
			}
			elseif($nick == $bots[1])
			{
			$message = "$nickname, вообще-то я на друзей не наезжаю!";
        DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($message, $bot, 0, 0, time(), $room['id'], 0, 0, 0));  
			}
			elseif($nick == $bots[3])
			{
			$message = "$nickname, ага. А потом мне влетит от системы...Не...Не буду...";
        DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($message, $bot, 0, 0, time(), $room['id'], 0, 0, 0));  
			}
			elseif($nick == "Unkind" || $nick == "Kaysar")
			{
			$message = "$nickname, иди в задницу, сука опущенная! Это создатель чата, мудила!";
        DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($message, $bot, 0, 0, time(), $room['id'], 0, 0, 0));  
			}
			else
			{
			$sql = mysql_query("SELECT * FROM `chat_bad_answers` ORDER BY RAND() LIMIT 1;");
			$bad_answer = mysql_result($sql, 0, 'answer');
			$message = "$nick, $bad_answer";
        DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($message, $bot, 0, 0, time(), $room['id'], 0, 0, 0));  
			}
		}
	}
}
//END OF NATRAVLENIE

//ANSWER
$fd = fopen("bots/second_bot/answer.dat", "r");
$answer = fgets($fd);
fclose($fd);

//TRAN ANSWER
$fd = fopen("bots/second_bot/translit.dat", "r");
$tran_answer = fgets($fd);
fclose($fd);

$up = array("А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "C", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ы", "Ь", "Э", "Ю", "Я");
$down = array("а", "б", "в", "г", "д", "е", "ё", "ж", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "c", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы", "ь", "э", "ю", "я");

$msg = str_replace($up, $down, $msg);

if(($msg == $answer or $msg == $tran_answer) && $buff_action != 0)
{
$sett = DB::$dbs->queryFetch("SELECT * FROM ".SETT." WHERE `id` = ?",array(1));  
$interval_zloy = (empty($sett['vict_interval_zloy']) ? 10 : $sett['vict_interval_zloy']);
$sql = mysql_query("SELECT `answers` FROM `chat_users` WHERE `id` = '".$id."';");
$answers = mysql_result($sql, 0);
$message = 'Эта пять, <a href="'.HOME.'/inside.php?id='.$user['id'].'"><b>' . $user['nick'] . '</b></a>! Придурки, ответ-то был: <b>'.$answer.'</b>. <a href="'.HOME.'/inside.php?id='.$user['id'].'"><b>' . $user['nick'] . '</b></a> отсосал(а) '.(++$user['zloy_otvet']).' раз(а). Следующий вопрос задам через '.$interval_zloy.' секунд, тупицы!'; 
DB::$dbs->query("INSERT INTO ".ROOMS_MSG." (`msg`, `user_id`, `friend_id`, `privat`, `time`, `room_id`, `color`, `mig`, `shrift`) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($message, $bot, 0, 0, time(), $room['id'], 0, 0, 0));    
$umnik = DB::$dbs->queryFetch("SELECT `chat_post` FROM ".USERS." WHERE `id` = ?",array($bot));
DB::$dbs->query("UPDATE ".USERS." SET `chat_post` = ? WHERE `id` = ?",array((++$umnik['chat_post']), $bot));
DB::$dbs->query("UPDATE ".USERS." SET `chat_post` = ?, `zloy_otvet` = ? WHERE `id` = ?",array((++$user['chat_post']), $user['zloy_otvet'], $user['id']));

$fd = fopen("bots/second_bot/time.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, (time() + $interval_zloy)); //NEXT QUESTION
flock($fd, LOCK_UN);
fclose($fd);

$fd = fopen("bots/second_bot/action.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, "0");
flock($fd, LOCK_UN);
fclose($fd);
}
?>