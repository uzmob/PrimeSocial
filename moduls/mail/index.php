<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();

switch ($select) {
    
    default:
    $id = abs(num($_GET['user']));
    
    if (!empty($id)) {
        $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ? ", array($id));
        
        if (DB::$dbs->querySingle("SELECT COUNT(*) FROM ".BLACKUSERS." WHERE `user_id` = ? && `black_id` = ?", array($ank['user_id'], $user['user_id'])) == TRUE) {
            head(''. $lng['Kirishda xatolik'] .'!'); 
            echo DIV_BLOCK . ''.$ank['nick'].'  | '. $lng['foydalanuvchiga yoza olmaysiz. Siz uni qora ro`yhatidasiz'] .'!' . CLOSE_DIV;
            require_once('../../core/stop.php');
            exit();            
        }
        
        if ($ank['user_id'] == $user['user_id']) {
            head(''. $lng['Siz o`zingizga yoza olmaysiz'] .''); 
            echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
            require_once('../../core/stop.php');
            exit();
        }

        
        $sql = DB::$dbs->queryFetch("SELECT * FROM ".DIALOG." WHERE (`user_id` = ? AND `friend_id` = ?) OR (`user_id` = ? AND `friend_id` = ?) ", array($user['user_id'],$ank['user_id'], $ank['user_id'], $user['user_id']));
    
        if (!empty($sql)) {
            header("Location: ".HOME."/mail/dialog/".$sql['id']."/");
        } else {
            DB::$dbs->query("INSERT INTO ".DIALOG." SET `user_id` = ?, `friend_id` = ? ",array($user['user_id'], $ank['user_id']));     
            $last = DB::$dbs->lastInsertId();
            header("Location: ".HOME."/mail/dialog/".$last."/");
        }
    } else {
        head(''. $lng['Mening suhbatlarim'] .'');
         
        
        $all = DB::$dbs->querySingle("SELECT * FROM ".DIALOG." WHERE `user_id` = ? || `friend_id` = ? ", array($user['user_id'], $user['user_id']));
        
        if (empty($all)) {
            echo DIV_BLOCK . ''. $lng['Suhbatlar yo`q'] .'' . CLOSE_DIV;
        } else {
            $n = new Navigator($all,5,''); 
            $sql = DB::$dbs->query("SELECT * FROM ".DIALOG." WHERE `user_id` = ? || `friend_id` = ? ORDER BY `prioritet` DESC", array($user['user_id'], $user['user_id']));
            while($dialog = $sql -> fetch()){
                if ($dialog['friend_id'] == $user['user_id']) {
                    $dialog['friend_id'] = $dialog['user_id'];
                } 
                
                $us = DB::$dbs->queryFetch("SELECT `nick` FROM ".USERS." WHERE `user_id` = ?", array($dialog['friend_id']));
                
                $count_msg = DB::$dbs->querySingle("SELECT COUNT(*) FROM ".DIALOG_MSG." WHERE `dialog_id` = ? AND `delet` != ?", array($dialog['id'], $user['user_id']));
                $count_msg_new = DB::$dbs->querySingle("SELECT COUNT(*) FROM ".DIALOG_MSG." WHERE `dialog_id` = ? AND `delet` != ? AND `user_friend` = ? AND `status` = ? ", array($dialog['id'], $user['user_id'], $user['user_id'], 1));

echo '<div class="touch"><a href="'.HOME.'/mail/dialog/'.$dialog['id'].'/">';
echo ''.icon('mail.png').' ' . $us['nick'] . '
 <span class="count">'.$count_msg . ($count_msg_new > 0 ? ' / <b>+'.$count_msg_new.'</b>' : NULL) . '</span><br/>'; 
echo '</a></div>';

             }
    echo $n->navi(); 
        }
        require_once('../../core/stop.php');
        exit();
    }
    break;
    
    case 'dialog':
    $id = abs(intval($_GET['id']));
    $sql = DB::$dbs->queryFetch("SELECT * FROM ".DIALOG." WHERE `id` = ? ", array($id));
    
    if (empty($sql)) {
        head(''. $lng['Suhbat topilmadi'] .''); 
        echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
        require_once('../../core/stop.php');
        exit();        
    }
    
    if ($sql['user_id'] != $user['user_id'] && $sql['friend_id'] != $user['user_id']) {
        header("Location: ".HOME."/mail/");
        exit();        
    }
    
    if ($sql['user_id'] == $user['user_id']) {
        $user_friend = $sql['friend_id'];
    } else {
        $user_friend = $sql['user_id'];
    }
    
    $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ? ", array($user_friend));
    
    head(''. $lng['bilan suhbat'] .' ' . $ank['nick'] . ' ');
    

    if (!empty($_POST['add']) && DB::$dbs->querySingle("SELECT COUNT(*) FROM ".BLACKUSERS." WHERE `user_id` = ? && `black_id` = ?", array($ank['user_id'], $user['user_id'])) == FALSE) {
        $msg = html($_POST['msg']);
        
        if (empty($msg)) {
            echo DIV_ERROR . ''. $lng['Bo`sh habar'] .'' . CLOSE_DIV;
        } else {
            DB::$dbs->query("INSERT INTO ".DIALOG_MSG." (`user`,`user_friend`,`msg`,`time`,`status`,`dialog_id`, `delet`) VALUE (?,?,?,?,?,?,?) ",array($user['user_id'], $user_friend, $msg, time(), 1, $id, 'no'));
            $last = DB::$dbs->lastInsertId();
            DB::$dbs->query("UPDATE ".DIALOG." SET `prioritet` = ? WHERE `id` = ? ", array(time(), $sql['id']));

            /* Antispam */
            $spam = 0;
            if (antiSpam($msg)) {
                $spam = 1;
            }
            
            if (empty($spam) && antilink($msg)) {
                $spam = 1;
            }
            
            if (!empty($spam)) {
                /* Spam haqida ma`muriyatga habar jo`natish */
                
                DB::$dbs->query("UPDATE ".DIALOG_MSG." SET `spam` = ? WHERE `id` = ?", array('spam', $last));
                DB::$dbs->query("INSERT INTO ".SPAM." (`type`,`msg`,`spam_user`,`time`,`status`,`user_id`, `post_id`) VALUE (?,?,?,?,?,?,?) ",array('mail', $msg, $user['user_id'], time(), 1, 0, $last));                
            }
            /* *** */
            
            header("Location: ?");
        }       
    }

    if (!empty($_GET['del'])) {
        $m = abs(intval($_GET['del']));
        $msg = DB::$dbs->queryFetch("SELECT * FROM ".DIALOG_MSG." WHERE `id` = ? ", array($m));
        $dialog = DB::$dbs->queryFetch("SELECT * FROM ".DIALOG." WHERE `id` = ? ", array($msg['dialog_id']));
    
        if (empty($msg['id'])) {
            header("Location: ".HOME."/mail/dialog/" . $id . "/");
        }
            
        if ($dialog['friend_id'] == $user['user_id']) {
            $us = $dialog['user_id'];
        } else {
            $us = $dialog['friend_id'];
        }
        if ($msg['delet'] == 'no') {
            DB::$dbs->query("UPDATE ".DIALOG_MSG." SET `delet` = ? WHERE `id` = ? ", array($user['user_id'], $msg['id']));
        } else {
            if ($user['user_id'] != $msg['delet']) {
                DB::$dbs->query("DELETE FROM ".DIALOG_MSG." WHERE `id` = ? ", array($msg['id']));
            }
        }
        header("Location: ".HOME."/mail/dialog/" . $id . "/");
    }

    if (DB::$dbs->querySingle("SELECT COUNT(*) FROM ".BLACKUSERS." WHERE `user_id` = ? && `black_id` = ?", array($ank['user_id'], $user['user_id'])) == FALSE) {   
        echo '<div class="white"><form action="#" method="post">
		<textarea name="msg" style="width:98%;"></textarea><br />
		<input type="submit" name="add" value="'. $lng['Jo`natish'] .'">';
		bbsmile();
		echo '</form></div>';
    } else {
        echo DIV_AUT . ''.$ank['nick'].' | '. $lng['foydalanuvchiga yoza olmaysiz. Siz uni qora ro`yhatidasiz'] .'!' . CLOSE_DIV;
    }
                
    $all = DB::$dbs->querySingle("SELECT COUNT(*) FROM ".DIALOG_MSG." WHERE `dialog_id` = ? AND `delet` != ?", array($id, $user['user_id']));
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Dialoglar bo`sh'] .'' . CLOSE_DIV;
    } else {
        
        /* Habarni SPAM qilib belgilaymiz */
        if (isset($_GET['spam'])) {
            /* Sozlamalar */
            $settMail['spamUser'] = FALSE; // Qoidabuzarning barcha habarlarini bloklash
            $settMail['spamMsg'] = TRUE; // Hamma o`xshash habarlarni bloklash
            
            $spam = abs(num($_GET['spam']));
            if (DB::$dbs->querySingle("SELECT COUNT(*) FROM ".DIALOG_MSG." WHERE `id` = ? ", array($spam)) == TRUE) {
                
                /* Habar haqida ma`lumotni o`qiymiz */
                $mail = DB::$dbs->queryFetch("SELECT * FROM ".DIALOG_MSG." WHERE `id` = ? ", array($spam));
                
                /* Habarni SPAM qilib belgilaymiz */
                DB::$dbs->query("UPDATE ".DIALOG_MSG." SET `spam` = ? WHERE `id` = ? ", array('spam', $spam));
                
                if ($settMail['spamMsg'] == TRUE) {
                    /* O`xshash habarlarni SPAM qilib belgilaymiz */
                    DB::$dbs->query("UPDATE ".DIALOG_MSG." SET `spam` = ? WHERE `msg` LIKE '%".$mail['msg']."%' && `user` = ?", array('spam', $mail['user']));
                }
                
                if ($settMail['spamUser'] == TRUE) {
                    /* Qoidabuzarning barcha habarlarini belgilash */
                    DB::$dbs->query("UPDATE ".DIALOG_MSG." SET `spam` = ? WHERE `user` = ? ", array('spam', $mail['user']));
                    echo $mail['user'];
                }
                
                /* Spam haqida ma`muriyatga habar jo`natish */
                DB::$dbs->query("INSERT INTO ".SPAM." (`type`,`msg`,`spam_user`,`time`,`status`,`user_id`, `post_id`) VALUE (?,?,?,?,?,?,?) ",array('mail', $mail['msg'], $mail['user'], time(), 1, $user['user_id'], $mail['id']));
            
                echo DIV_MSG . ''. $lng['Habar spam deb belgilandi'] .'' . CLOSE_DIV;
            }
        }
        
        $n = new Navigator($all,5,'select=dialog&id=' . $sql['id']); 
        $sql = DB::$dbs->query("SELECT * FROM ".DIALOG_MSG." WHERE `dialog_id` = ? AND `delet` != ? ORDER BY `time` DESC LIMIT {$n->start()}, 5", array($id, $user['user_id']));
        
        while($msg = $sql -> fetch()){
            
            /* Spam tizimi */
            if ($msg['spam'] == 'spam' && $msg['user'] != $user['user_id']) {
                $msg['msg'] = ''. $lng['Tiket'] .'Diqqat! Habar moderator tomonidan ko`rilgunicha yopildi.';
            }
            
            if ($msg['user_friend'] == $user['user_id'] && $msg['status'] == 1) {
                DB::$dbs->query("UPDATE ".DIALOG_MSG." SET `status` = ? WHERE `id` = ? ", array(0,$msg['id']));
            }
                echo '<div class="lines">';
            
            if ($msg['status'] == 1) {
                echo '<div style="background: #EAF6FD;color:#023C60;padding:8px;margin: -10px;border-bottom: 1px solid #eee;">';
            }
			
echo '<table cellspacing="0" cellpadding="0" style="margin-bottom:5px;" width="100%" ><tr>';
echo '<td style="width:10%;"><center>';
echo '' . avatar($msg['user'],40,40) . '';
echo '</center></td>';

echo '<td style="width:90%;">';
echo '&nbsp;' . userLink($msg['user']) . ' <a href="'.HOME.'/mail/dialog/'.$id.'/?del='.$msg['id'].'">[x]</a> <span class="count">' . vrem($msg['time']) . '</span> ' . ($msg['user'] != $user['user_id'] ? ' <a href="?spam='.$msg['id'].'">['. $lng['spam'] .']</a> ' : NULL) . ' ';
echo '<br/>&nbsp;' . text($msg['msg']) . '';
echo '</td></tr></table>';
            
            if ($msg['status'] == 1) {
                echo '</div>';
            }
                echo '</div>';
        
        }
        echo $n->navi();
    }

    break;

}
require_once('../../core/stop.php');
?>