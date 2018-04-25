<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

if (privilegy('positions') == FALSE) {
    header("Location: ".HOME."/panel");
    exit();
}

switch ($select) {
        
    default:
    head(''. $lng['Lavozimlar'] .''); 
    if ($_POST) {
        $position = html($_POST['position']);
        
        if (empty($position)) {
            echo DIV_ERROR . ''. $lng['Lavozim nomini kiriting'] .'' . CLOSE_DIV;
        } else {
            DB::$dbs->query("INSERT INTO ".POSITIONS." (`position`) VALUES (?)", array($position));
        }
    }
    
    if ($_GET['prior']) {
        DB::$dbs->query("UPDATE ".POSITIONS." SET `prioritet` = ? WHERE `id` = ? ", array(time(), num($_GET['prior'])));
        header("Location: ".HOME."/panel/privilege/");
    }
    $sql = DB::$dbs->query("SELECT * FROM ".POSITIONS." ORDER BY `prioritet` DESC");
    while($pos = $sql -> fetch()) {
        echo DIV_LI . '<a href="'.HOME.'/panel/privilege/'.$pos['id'].'/">'.$pos['position'] . '</a> 
		<a href="?prior='.$pos['id'].'" style="float:right;">'.icon('rocket.png').'</a>' . CLOSE_DIV;    
    }
    
    echo DIV_AUT;
    echo '<form action="#" method="POST">';
    echo ''. $lng['Yangi lavozim'] .':<br /><input type="text" name="position" />';
    echo '<input type="submit" name="add" value="'. $lng['Yaratish'] .'" /></form>';
    echo CLOSE_DIV;  
    break;
    
    case 'pos':
    $id = num($_GET['id']);
    $pos = DB::$dbs->queryFetch("SELECT * FROM ".POSITIONS." WHERE `id` = ? ",array($id));
    
    head(''.$lng['Imtiyozlar'].''); 
    
    if ($_POST) {
        foreach ($_POST as $name => $value) {
            if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, $name)) == TRUE) {
                $value == 'off' ? DB::$dbs->query("DELETE FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ? ",array($id, $name)) : NULL;
            } else {
                $value == 'on' ? DB::$dbs->query("INSERT INTO ".PRIVILEGE." (`pos`, `privil`) VALUES (?, ?) ",array($id, $name)) : NULL;
            }
        }
    }
    
    echo '<div class="lines">';
    echo '<form action="#" method="POST">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'settings'));
    echo '<b>'. $lng['Sayt sozlamalari'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="settings" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).'/> / '. $lng['Yoqish'] .': <input type="radio" name="settings" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).'/>';
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'ipsoft'));
    echo '<b>'. $lng['Ko`rish'] .' IP+Soft:</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="ipsoft" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).'/> / '. $lng['Yoqish'] .': <input type="radio" name="ipsoft" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).'/>';
        echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'news'));
    echo '<b>'. $lng['Yangiliklarni boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="news" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).'/> / '. $lng['Yoqish'] .': <input type="radio" name="news" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).'/>';
        echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'news_comm_delete'));
    echo '<b>'. $lng['Yangiliklardagi sharhlarni o`chirish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="news_comm_delete" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="news_comm_delete" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />';
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'anceta_update'));
    echo '<b>'. $lng['Anketalarni tahrirlash'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="anceta_update" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="anceta_update" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />';
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'anceta_delete'));
    echo '<b>'. $lng['Anketalarni o`chirish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="anceta_delete" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="anceta_delete" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />';        
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'anceta_bann'));
    echo '<b>'. $lng['Foydalanuvchiga ban berish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="anceta_bann" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="anceta_bann" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />';    
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'positions'));
    echo '<b>'. $lng['Lavozim imtiyozlarini boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="positions" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="positions" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'level'));
    echo '<b>'. $lng['Lavozimga tayinlash'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="level" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="level" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
     echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'balls'));
    echo '<b>'. $lng['Ballarni aylantirish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="balls" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="balls" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'smiles'));
    echo '<b>'. $lng['Smayllarni boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="smiles" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="smiles" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'guestbook'));
    echo '<b>'. $lng['Mini chatni boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="guestbook" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="guestbook" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
        echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'guestbook_moder'));
    echo '<b>'. $lng['Mini chatni moderatsiya qilish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="guestbook_moder" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="guestbook_moder" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'chat'));
    echo '<b>'. $lng['Chatni boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="chat" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="chat" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
     echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'chat_moder'));
    echo '<b>'. $lng['Chatni moderatsiya qilish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="chat_moder" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="chat_moder" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'mysql'));
    echo '<b>'. $lng['MySQL so`rovlar'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="mysql" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="mysql" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'forum'));
    echo '<b>'. $lng['Forumni boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="forum" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="forum" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'forum_moder'));
    echo '<b>'. $lng['Forumni moderatsiya qilish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="forum_moder" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="forum_moder" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'blog'));
    echo '<b>'. $lng['Blogni boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="blog" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="blog" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'blog_moder'));
    echo '<b>'. $lng['Blogni moderatsiya qilish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="blog_moder" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="blog_moder" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'group'));
    echo '<b>'. $lng['Guruhlarni boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="group" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="group" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'group_moder'));
    echo '<b>'. $lng['Guruhlarni moderatsiya qilish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="group_moder" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="group_moder" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />';                                                                               
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'lib'));
    echo '<b>'. $lng['Kutubxonani boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="lib" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="lib" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
        echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'lib_moder'));
    echo '<b>'. $lng['Kutubxonani moderatsiya qilish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="lib_moder" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="lib_moder" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />';                                                                               
        echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'zc'));
    echo '<b>'. $lng['Yuklamalarni boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="zc" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="zc" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />'; 
        echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'zc_moder'));
    echo '<b>'. $lng['Yuklamalarni moderatsiya qilish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="zc_moder" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="zc_moder" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />';                                                                               
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'guest_moder'));
    echo '<b>'. $lng['Mehmonxonani moderatsiya qilish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="guest_moder" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="guest_moder" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />';  
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'moder'));
    echo '<b>'. $lng['Moderatsiya qilish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="moder" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="moder" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />';  
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'album'));
    echo '<b>'. $lng['Fotoalbomlarni boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="album" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="album" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />';  
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'stena_love'));
    echo '<b>'. $lng['Sevgi devorini boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="stena_love" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="stena_love" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />';  
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'statush'));
    echo '<b>'. $lng['Statuslar tarihini boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="statush" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="statush" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />';  
    echo '</div>';
    echo '<div class="lines">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'ticket'));
    echo '<b>'. $lng['Tiketlarni boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="ticket" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="ticket" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />';  
    echo '</div>';
    echo '<div class="white">';
    $sql = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PRIVILEGE." WHERE `pos` = ? && `privil` = ?", array($id, 'spam'));
    echo '<b>'. $lng['Spamlarni boshqarish'] .':</b><br />'. $lng['O`chirish2'] .': <input type="radio" name="spam" value="off" '.($sql == FALSE ? 'checked="checked"' : NULL).' /> / '. $lng['Yoqish'] .': <input type="radio" name="spam" value="on" '.($sql == TRUE ? 'checked="checked"' : NULL).' />';                                    
    echo '<br /><input type="submit" value="'. $lng['Saqlash'] .'" />';
    echo '</form>';
    echo '</div>';
    break;


}

echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>