<?php

$bot = 2;

$var = explode(" ", $msg);

//ANSWER
$fd = fopen("bots/umnik/answer.dat", "r");
$answer = fgets($fd);
fclose($fd);

//TRAN ANSWER
$fd = fopen("bots/umnik/translit.dat", "r");
$tran_answer = fgets($fd);
fclose($fd);

$up = array("А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "C", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ы", "Ь", "Э", "Ю", "Я");
$down = array("а", "б", "в", "г", "д", "е", "ё", "ж", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "c", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы", "ь", "э", "ю", "я");

$msg2 = $msg;
$msg2 = str_replace($up, $down, $msg2);

if(($msg2 == $answer or $msg2 == $tran_answer) && $buff_action != 0) {
    $interval_vic = 10;
    

        $message = 'Отлично, <a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a>! Правильный ответ был: <b>'.$answer.'</b>. <a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> отвечает на '.(++$user['victorina']).'-й вопрос, к тому же начисляется +1 балл и +1 рейтинг! Следующий вопрос через '.$interval_vic.' секунд. ;-)'; 
DB::$dbs->query("INSERT INTO ".CHAT_MSG." (`room_id`, `user_id`, `kont_id`, `privat`, `time`, `msg`) VALUES (?, ?, ?, ?, ?, ?)", array($room['id'], $bot, 0, 0, time(), $message));
        DB::$dbs->query("UPDATE ".USERS." SET `chat_post` = ?, `victorina` = ?, `rating` = ?, `balls` = ? WHERE `user_id` = ?",array((++$user['chat_post']),  $user['victorina'], (++$user['rating']), (++$user['balls']), $user['user_id']));
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
    }

?>