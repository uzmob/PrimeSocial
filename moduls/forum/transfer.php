<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();

$theme = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_THEME." WHERE `id` = ? ", array(abs(num($_GET['theme']))));
if (empty($theme)) {
    head(''. $lng['Mavzu topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}

if (privilegy('forum_moder') == FALSE) {
    head(''. $lng['Kirishda xatolik'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit();     
}

head(''. $lng['Mavzuni ko`chirish'] .': ' . $theme['name']);
  

if (!empty($_POST['send'])) {
    $id = abs(num($_POST['forumc']));
    
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_CAT." WHERE `id` = ?", array($id)) == TRUE) {
        
        /* Avvalgi joyi */
        $forum = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS." WHERE `id` = ? ", array($theme['forum_id']));
        $forumc = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_CAT." WHERE `id` = ? ", array($theme['forumc_id']));
        /* */
        
        /* Yangi joyi */
        $forumc_new = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_CAT." WHERE `id` = ? ", array($id));
        $forum_new = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS." WHERE `id` = ? ", array($forumc_new['forum_id']));
        /* */
        
        DB::$dbs->query("UPDATE ".FORUMS_THEME." SET `forum_id` = ?, `forumc_id` = ? WHERE `id` = ? ", array($forumc_new['forum_id'], $forumc_new['id'], $theme['id']));
        
        /* Kochirish haqida habar tayyorlaymiz */
        $msg = ' '. $lng['Mavzu podforumidan ko`chirildi'] .' <b>' . $forum['name'] . '/' . $forumc['name'] . '</b> '. $lng['dan'] .' <b>' . $forum_new['name'] . '/' . $forumc_new['name'] . ' </b> '. $lng['ga'] .'';
        
        DB::$dbs->query("INSERT INTO ".FORUM_POST." (`forum_id`, `forumc_id`, `theme_id`, `msg`, `user_id`, `time`, `file`, `ct`) VALUES 
        (?,?,?,?,?,?,?,?)", array($forum_new['id'], $forumc_new['id'], $theme['id'], $msg, $user['user_id'], time(), 0, 0));

        $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FORUMS_POST." WHERE `theme_id` = ? ", array($theme['id']));
        $page = ceil(($posts / $config['write']['forum_post'])); 
                
        header("Location: ".HOME."/forum/".$forum_new['id']."/".$forumc_new['id']."/".$theme['id']."/?p=".$page);
    } else {
        echo DIV_ERROR . ''. $lng['Podforum topilmadi'] .'' . CLOSE_DIV;
    }
}


echo DIV_BLOCK
    . '<form action="?" method="POST">'
    . ''. $lng['Podforumni tanlang'] .':<br />'
    . '<select name="forumc">';
    $sql = DB::$dbs->query("SELECT * FROM ".FORUMS_CAT ." WHERE `id` != '".$theme['forumc_id']."'");
    while($forumc = $sql -> fetch()) {
        $forum = DB::$dbs->queryFetch("SELECT `name` FROM ".FORUMS." WHERE `id` = ? ", array($forumc['forum_id']));
        echo '<option value="'.$forumc['id'].'">'.$forum['name'].'/'.$forumc['name'].'</option>';
    }    
    echo '</select>'
    . '<input type="submit" name="send" value="'. $lng['Ko`chirish'] .'" />'
    . '</form>';

echo CLOSE_DIV;

echo DIV_LI . '- <a href="'.HOME.'/forum/'.$theme['forum_id'].'/'.$theme['forumc_id'].'/'.$theme['id'].'/">'. $lng['Mavzuga qaytish'] .'</a>' . CLOSE_DIV;

require_once('../../core/stop.php');
?>