<?
$bot = 2;
$interval_vic = 10;

//GET TIME
$fd = fopen("bots/umnik/time.dat", "r");
if(!$fd) return;
$buff_time = intval(fgets($fd));
fclose($fd);

//GET ACTION
$fd = fopen("bots/umnik/action.dat", "r");
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
$num = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM `chat_vopros`");
$rnd = rand(1,$num);
$ques_arr = DB::$dbs->queryFetch("SELECT * FROM `chat_vopros` WHERE `id` = ?",array($rnd));  
$question = $ques_arr['vopros'];
$answer = $ques_arr['otvet'];
$tran_answer = $ques_arr['translit'];
$answer = iconv('utf-8', 'windows-1251', $answer);
$length = strlen($answer);
$answer = iconv('windows-1251', 'utf-8', $answer);

$question = "$question ($length букв)";

DB::$dbs->query("INSERT INTO ".CHAT_MSG." (`room_id`, `user_id`, `kont_id`, `privat`, `time`, `msg`) VALUES (?, ?, ?, ?, ?, ?)", array($room['id'], $bot, 0, 0, time(), $question));

//$umnik = DB::$dbs->queryFetch("SELECT `chat_post` FROM ".USERS." WHERE `id` = ?",array($bot));
//DB::$dbs->query("UPDATE ".USERS." SET `chat_post` = ? WHERE `id` = ?",array((++$umnik['chat_post']), $bot));
$fd = fopen("bots/umnik/question.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, $question);
flock($fd, LOCK_UN);
fclose($fd);

$fd = fopen("bots/umnik/answer.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, $answer);
flock($fd, LOCK_UN);
fclose($fd);

$fd = fopen("bots/umnik/translit.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, $tran_answer);
flock($fd, LOCK_UN);
fclose($fd);

$fd = fopen("bots/umnik/action.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, "1");
flock($fd, LOCK_UN);
fclose($fd);

$fd = fopen("bots/umnik/time.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, (time() + 180 + $interval_vic));
flock($fd, LOCK_UN);
fclose($fd);
break;

//FIRST HELP
case '1':
$fd = fopen("bots/umnik/answer.dat", "r");
$answer = fgets($fd);
fclose($fd);

$answer = iconv('utf-8', 'windows-1251', $answer);
$help = substr($answer, 0, 1);
$answer = iconv('windows-1251', 'utf-8', $answer);
$help = iconv('windows-1251', 'utf-8', $help);
$help = "Подсказка: $help...";

DB::$dbs->query("INSERT INTO ".CHAT_MSG." (`room_id`, `user_id`, `kont_id`, `privat`, `time`, `msg`) VALUES (?, ?, ?, ?, ?, ?)", array($room['id'], $bot, 0, 0, time(), $help));
//$umnik = DB::$dbs->queryFetch("SELECT `chat_post` FROM ".USERS." WHERE `id` = ?",array($bot));
//DB::$dbs->query("UPDATE ".USERS." SET `chat_post` = ? WHERE `id` = ?",array((++$umnik['chat_post']), $bot));
$fd = fopen("bots/umnik/action.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, "2");
flock($fd, LOCK_UN);
fclose($fd);
break;

//SECOND HELP
case '2':
$fd = fopen("bots/umnik/answer.dat", "r");
$answer = fgets($fd);
fclose($fd);

$answer = iconv('utf-8', 'windows-1251', $answer);
$help = substr($answer, 0, 2);
$answer = iconv('windows-1251', 'utf-8', $answer);
$help = iconv('windows-1251', 'utf-8', $help);
$help = "Подсказка: $help...";

DB::$dbs->query("INSERT INTO ".CHAT_MSG." (`room_id`, `user_id`, `kont_id`, `privat`, `time`, `msg`) VALUES (?, ?, ?, ?, ?, ?)", array($room['id'], $bot, 0, 0, time(), $help));
//$umnik = DB::$dbs->queryFetch("SELECT `chat_post` FROM ".USERS." WHERE `id` = ?",array($bot));
//DB::$dbs->query("UPDATE ".USERS." SET `chat_post` = ? WHERE `id` = ?",array((++$umnik['chat_post']), $bot));
$fd = fopen("bots/umnik/action.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, "3");
flock($fd, LOCK_UN);
fclose($fd);
break;

//NO ANSWER
case '3':
$fd = fopen("bots/umnik/answer.dat", "r");
$answer = fgets($fd);
fclose($fd);
$message = "Время истекло! Правильный ответ был: <b>$answer</b>. Следующий вопрос через ".$interval_vic." секунд.";
DB::$dbs->query("INSERT INTO ".CHAT_MSG." (`room_id`, `user_id`, `kont_id`, `privat`, `time`, `msg`) VALUES (?, ?, ?, ?, ?, ?)", array($room['id'], $bot, 0, 0, time(), $message));
//$umnik = DB::$dbs->queryFetch("SELECT `chat_post` FROM ".USERS." WHERE `id` = ?",array($bot));
//DB::$dbs->query("UPDATE ".USERS." SET `chat_post` = ? WHERE `id` = ?",array((++$umnik['chat_post']), $bot));
$fd = fopen("bots/umnik/time.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, (time() + $interval_vic)); //NEXT QUESTION
flock($fd, LOCK_UN);
fclose($fd);

$fd = fopen("bots/umnik/action.dat", "w");
flock($fd, LOCK_EX);
$puts = fputs($fd, "0");
flock($fd, LOCK_UN);
fclose($fd);
break;
}
?>