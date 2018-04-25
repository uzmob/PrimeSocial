<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');
require_once('func.php');

$id = abs(num($_GET['id']));
$group = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS." WHERE `id` = ? ",array($id));

if (empty($group)) {
    head(''. $lng['Guruh topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
} 

if ($group['private_forum'] == TRUE && check_private($group['id']) == FALSE && privilegy('group') == FALSE && privilegy('group_moder') == FALSE) {
    head(''. $lng['Kirishda xatolik'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
} 
    
switch ($select) {
    
    default:
    head(''. $lng['Muhokamalar'] .': ' . $group['name']);
    if ($user) {    
    echo DIV_AUT . '<form action="'.HOME.'/groups/topics/'.$group['id'].'/new_theme/" method="POST">
	<input type="submit" name="new_theme" value="'. $lng['Yangi mavzu'] .'" /></form>' . CLOSE_DIV;
    }
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_TOPIC." WHERE `group_id` = ? ", array($group['id']));
        
    if ($all == 0) {
        echo DIV_AUT . ''. $lng['Mavzular ochilmagan'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['groups_topic'],'id='.$group['id']);
$sql = DB::$dbs->query("SELECT * FROM ".GROUPS_TOPIC." WHERE `group_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['groups_topic']."", array($group['id']));
while($topic = $sql -> fetch()) {
$posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_POST." WHERE `topic_id` = ? ", array($topic['id']));
            echo DIV_BLOCK;
            echo ''.icon('pages.png').' <a href="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/">'.$topic['topic'].'</a> 
			<span class="count">'.$posts.'</span>';
            echo CLOSE_DIV;
        }
        echo $n->navi();
    }
    
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
    
    case 'new_theme':

    head(''. $lng['Yangi mavzu ochish'] .': ' . $group['name']);
             
    
    if ($_POST['add']) {
        
        $name2 = html($_POST['name']);
        $msg = html($_POST['msg']);
        $uvedom = abs(num($_POST['uvedom']));
        $vote = html($_POST['vote']);
        $vote_1 = html($_POST['vote_1']);
        $vote_2 = html($_POST['vote_2']);
        $vote_3 = html($_POST['vote_3']);
        $vote_4 = html($_POST['vote_4']);
        $vote_5 = html($_POST['vote_5']);
        $vote_6 = html($_POST['vote_6']);
        $vote_7 = html($_POST['vote_7']);
        $vote_8 = html($_POST['vote_8']);
        $vote_9 = html($_POST['vote_9']);
        $vote_10 = html($_POST['vote_10']);
        
        if (empty($name2)) {
            $err = ''. $lng['Mavzu nomini kiriting'] .'.<br />';
        }
        
        if (strlen($name2) < 8) {
            $err .= ''. $lng['Mavzu nomi juda qisqa'] .'. [min. 8]<br />';
        }
        
        if (empty($msg)) {
            $err .= ''. $lng['Habar yozing'] .'.<br />';
        }
        
        if (strlen($msg) < 20) {
            $err .= ''. $lng['Juda qisqa habar'] .'. [min. 20]<br />';
        }
        
        if (!empty($vote) && strlen($vote) < 20) {
            $err .= ''. $lng['So`rovnoma nomi juda qisqa'] .'. [min. 10]<br />';
        }
        
        if (!empty($vote) && (empty($vote_1) || empty($vote_2))) {
            $err .= ''. $lng['So`rovnomani asosiy variantlarini to`ldiring'] .'';
        }
        
        if (!empty($_FILES['file'])) {
            $name = $_FILES['file']['name']; # Fayl nomi
            $ext = strtolower(strrchr($name, '.')); # Fayl shakli
            $size = $_FILES['file']['size']; # Fayl hajmi
            $time = time();
            $file = $time.$ext;
    
            if ($size > (1048576 * $config['max_upload_groupа_file'])) {
                $err .= ''. $lng['Fayl hajmi belgilangan miqdordan ortmoqda'] .'. [Max. '.$config['max_upload_groupа_file'].'Mb.]<br />';
            }
                
            if (preg_match('/.phtml/i', $name) || preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess') {
                $err .= ''. $lng['Fayl formati to`g`ri kelmaydi'] .'.<br />';
            }
        }
                
        if (empty($err)) {
            if (!empty($ext)) {
                copy($_FILES['file']['tmp_name'], '../../files/groups/forum/'.$time.$ext);
            }
            
            $file = (empty($ext) ? 0 : $file);
            
            DB::$dbs->query("INSERT INTO ".GROUPS_TOPIC." (`group_id`, `topic`, `user_id`, `uvedom`, `time`, `vote`, `vote_1`, `vote_2`, `vote_3`, `vote_4`, `vote_5`, `vote_6`, `vote_7`, `vote_8`, `vote_9`, `vote_10`) VALUES 
            (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array($group['id'], $name2, $user['user_id'], $uvedom, time(), $vote, $vote_1, $vote_2, $vote_3, $vote_4, $vote_5, $vote_6, $vote_7, $vote_8, $vote_9, $vote_10));
                    
            $lastid = DB::$dbs->lastInsertId();
            
            DB::$dbs->query("INSERT INTO ".GROUPS_POST." (`group_id`, `topic_id`, `post`, `user_id`, `time`, `file`) VALUES 
            (?,?,?,?,?,?)", array($group['id'], $lastid, $msg, $user['user_id'], time(),$file));
            
            header("Location: ".HOME."/groups/topics/".$group['id']."/".$lastid."/");
        
        } else {
            echo DIV_ERROR . $err . CLOSE_DIV;
        }            
    }
    echo '<div class="white">';
    echo '<form action="#" enctype="multipart/form-data" method="POST">';
    echo '<b>'. $lng['Mavzu nomi'] .':</b> [min. 8]<br />
	<input type="text" name="name" style="width:95%;"/><br /><br />';
    echo '<b>'. $lng['Habar'] .':</b> [min. 20]<br />
	<textarea name="msg" style="width:95%;height:5pc;"></textarea><br />';
    
    echo '<b>'. $lng['Fayl biriktirish'] .':</b> [max. '.$config['max_upload_groupа_file'].'mb.]<br />
	<input type="file" name="file" style="width:95%;"/><br /><br />';
    echo '<input type="checkbox" name="uvedom" value="1" /> '. $lng['Mavzuni kuzatish'] .' <br /><br />';
    echo '</div>'; 
	
    echo '<div class="white">';
    echo '<b>'. $lng['So`rovnoma kiritish'] .'</b> ('. $lng['shart emas'] .')<br/>
    <b>'. $lng['Nomi'] .':</b> [min. 10]<br />';
    echo '<input type="text" name="vote" style="width:95%;"/><br />';
    echo '<b>'. $lng['Javob variantlari'] .':</b> <br/>
    <span class="mini">'. $lng['Kamida 2 ta maydoncha to`ldirilishi shart'] .'</span><br />';
    echo '<input type="text" name="vote_1"  placeholder="1" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_2" placeholder="2" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_3" placeholder="3" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_4" placeholder="4" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_5" placeholder="5" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_6" placeholder="6" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_7" placeholder="7" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_8" placeholder="8" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_9" placeholder="9" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_10" placeholder="10" style="width:95%;"/><br /><br />';
    
    echo '<input type="submit" name="add" value="'. $lng['Mavzu ochish'] .'" /><br />';
    echo '</form>';
    echo CLOSE_DIV;
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups/topics/'.$group['id'].'/">'. $lng['Muhokamalar'] .'</a> / <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
    
    case 'topic':
    $topic = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS_TOPIC." WHERE `id` = ? ", array(abs(num($_GET['topic']))));
    

    if (empty($topic)) {
        head(''. $lng['Mavzu topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
              
    }
    
    DB::$dbs->query("DELETE FROM ".GROUPS_NEW_POST." WHERE `theme_id` = ? && `user_id` = ? ", array($topic['id'], $user['user_id']));
    
    if ($topic['status'] == 1) {
        head(''. $lng['Mavzu muhokama qilish uchun yopilgan'] .'!'); 
    } else {
        head('' . $topic['topic'] . ''); 
    }
    
    if (isset($_GET['del']) && privilegy('group_moder')) {
         if (!isset($_GET['go'])) {
            echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlang'] .':</b> 
			<a href="?del&go">['. $lng['O`chirish'] .']</a> 
			<a href="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
        } else {
            $sql = DB::$dbs->query("SELECT * FROM ".GROUPS_POST." WHERE `topic_id` = ? ", array($topic['id']));
            while($post = $sql -> fetch()) {
                unlink('../../files/groups/forum/'.$post['file']);
            }
            DB::$dbs->query("DELETE FROM ".GROUPS_POST." WHERE `topic_id` = ? ", array($topic['id']));
            DB::$dbs->query("DELETE FROM ".GROUPS_TOPIC." WHERE `id` = ? ", array($topic['id']));
            header("Location: ".HOME."/groups/topics/".$group['id']."/"); 
        }          
    }
       
    echo '<div class="white"><b>' . $topic['topic'] . '</b> ' . ($topic['status'] == 1 ? '| <b>'. $lng['Mavzu yopilgan'] .'!</b>' : NULL) . '</div>';
    
    if (!empty($topic['vote'])) {
        echo '<br /><b>' . $topic['vote'] . '</b><br />';
        
        if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_VOTE." WHERE `theme_id` = ? && `user_id` = ? ", array($topic['id'], $user['user_id'])) == FALSE && $topic['status'] == 0) {
            echo '<form action="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/vote/" method="POST"><select name="variant">';
            for ($i = 1; $i <= 10; ++$i) {
                echo (!empty($topic['vote_'.$i]) ? '<option value="'.$i.'"">'.$topic['vote_'.$i].'</option>' : NULL);
            }
            echo '</select><input type="submit" name="myvote" value="'. $lng['Ovoz berish'] .'" /></form>';
        } else {
            for ($i = 1; $i <= 10; ++$i) {
                $votes = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_VOTE." WHERE `theme_id` = ? && `variant` = ? ", array($topic['id'], $i));
                echo (!empty($topic['vote_'.$i]) ? '<b>' . $i . '.</b> ' . $topic['vote_'.$i].' ['.$votes.' '. $lng['kishi'] .']<br />' : NULL);
            }            
        }
    }
    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_POST." WHERE `topic_id` = ?", array($topic['id']));
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Habarlar yo`q'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['groups_topic_msg'],'select=topic&topic='.$topic['id'].'&id='.$group['id']);
        $sql = DB::$dbs->query("SELECT * FROM ".GROUPS_POST." WHERE `topic_id` = ? LIMIT {$n->start()}, ".$config['write']['groups_topic_msg']."", array($topic['id']));
        while($post = $sql -> fetch()) {
echo '<div class="white">';
echo '<table cellspacing="0" cellpadding="0" style="margin-bottom:5px;" width="100%" ><tr>';
echo '<td class="grey" style="width:5%;border-radius: 6px 0 0 0 ;"><center>';
echo '' . avatar($post['user_id'],40,40) . '';
echo '</center></td>';
echo '<td class="grey" style="width:95%;border-radius: 0 6px 0 0 ;">';
			echo '' . userLink($post['user_id']) . '  ';
            if ($user) {
            echo '<span style="float:right;">' . ($user['user_id'] != $post['user_id'] && $topic['status'] == 0 ? '<a href="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/new_post/?post='.$post['id'].'">'.icon('ballons.png').'</a> &#160;&#160; 
			<a href="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/new_post/?ctpost='.$post['id'].'">'.icon('oko.png').'</a> &#160;&#160; 
			' : NULL) . ( (privilegy('group_moder') || $post['user_id'] == $user['user_id']) && $topic['status'] == 0 ? '<a href="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/delete/'.$post['id'].'/">'.icon('minus2.png').'</a> &#160;&#160; 
			<a href="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/edit/'.$post['id'].'/">'.icon('pen2.png').'</a> &#160;&#160; ' : NULL) . '</span>';
            }
			echo '<br /><span class="mini">' . vrem($post['time']) . '</span>';
            
            
		 echo '</td></tr></table>';
		 echo '<div class="white" style="box-shadow: 0 8px 10px rgba(162,162,162,0.25), 0 2px 4px rgba(162,162,162,0.22);margin-bottom: 5px;margin-left: 2px;margin-right: 2px;">';
         if (!empty($post['ct'])) {
                $ct = DB::$dbs->queryFetch("SELECT `post` FROM ".GROUPS_POST." WHERE `id` = ? ", array($post['ct']));
                echo DIV_CT . '<small><b>'. $lng['Sitata'] .':</b></small><br />' . text($ct['post']) . CLOSE_DIV; 
            }
            
            
            if (!empty($post['file'])) {
                
                $path = '../../files/groups/forum/'.$post['file'];
                
                $size = get_size(filesize($path));
                $path_info = pathinfo($path);
	
            echo '<a href="'.HOME.'/files/groups/forum/'.$post['file'].'">
			<img src="'.HOME.'/files/groups/forum/'.$post['file'].'"  style="width:50%;"  />
			</a> &nbsp; ';
      
  
                echo '<br /><a href="'.HOME.'/files/groups/forum/'.$post['file'].'"><b>'. $lng['Yuklab olish'] .'</b></a> ['.$path_info['extension'].'] ['.$size.']<br /><br />'; 
            }
		 echo text($post['post']);
		 echo ' </div></div>'; 
            
          
        }
       
        echo $n->navi();        
    }
    if ($user) {
    if ($topic['status'] == 0) {
        echo DIV_AUT . '<form action="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/new_post/" method="POST">
		<input type="submit" name="new_post" value="'. $lng['Mavzuga javob berish'] .'" /></form>' . CLOSE_DIV;
    }
    }
    if ($topic['user_id'] == $user['user_id']) {    
        echo DIV_BLOCK;
        echo '<a href="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/closed_run/"><b>'.($topic['status'] == 0 ? ''. $lng['Yopish'] .'' : ''. $lng['Ochish'] .'').'</b></a> / ';
        
        if ($topic['status'] == 0) {
            echo '<a href="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/edit/">'. $lng['Tahrirlash'] .'</a> / ';
            if (privilegy('group_moder')) echo '<a href="?del">'. $lng['O`chirish'] .'</a> / ';
        }
        echo CLOSE_DIV;  
    } elseif (privilegy('group_moder')) {    
        echo DIV_BLOCK;
        echo '<a href="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/closed_run/"><b>'.($topic['status'] == 0 ? ''. $lng['Yopish'] .'' : ''. $lng['Ochish'] .'').'</b></a> / ';
        if ($theme['status'] == 0) {
            echo '<a href="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/edit/">'. $lng['Tahrirlash'] .'</a> / ';
            echo '<a href="?del">'. $lng['O`chirish'] .'</a> / ';
        }
        echo CLOSE_DIV;  
    }
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups/topics/'.$group['id'].'/">'. $lng['Muhokamalar'] .'</a> / <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    
    break;
    
    case 'vote':
    $variant = abs(num($_POST['variant']));
    $topic = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS_TOPIC." WHERE `id` = ? ", array(abs(num($_GET['topic']))));
    if (empty($topic)) {
        head(''. $lng['Mavzu topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    if ($topic['status'] == 1) {
        head(''. $lng['Mavzu yopilgan'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    if (empty($topic['vote'])) {
        head(''. $lng['Mavzuda so`rovnoma yaratilmagan'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    if (empty($topic['vote_'.$variant]) || $variant > 10) {
        head(''. $lng['Mavjud bo`lmagan variant'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".GROUPS_VOTE." WHERE `theme_id` = ? && `user_id` = ? ", array($topic['id'], $user['user_id'])) == TRUE) {
        head(''. $lng['Siz ovoz bergansiz'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit();    
    }
    
    DB::$dbs->query("INSERT INTO ".GROUPS_VOTE." (`theme_id`, `user_id`, `variant`) VALUES (?,?,?)", array($topic['id'], $user['user_id'], $variant));
    header("Location: ".HOME."/groups/topics/".$group['id']."/".$topic['id']."/");
    break;
    
    case 'closed_run':
    $topic = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS_TOPIC." WHERE `id` = ? ", array(abs(num($_GET['topic']))));
    if (empty($topic)) {
        head(''. $lng['Mavzu topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    if ($topic['user_id'] != $user['id'] && privilegy('group_moder') == FALSE) {
        head(''. $lng['Kirishda xatolik'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit();     
    }
    
    $status = ($topic['status'] == 0 ? 1 : 0);
    DB::$dbs->query("UPDATE ".GROUPS_TOPIC." SET `status` = ? WHERE `id` = ? ", array($status, $topic['id']));
    header("Location: ".HOME."/groups/topics/".$group['id']."/".$topic['id']."/");    
    break;
    
    case 'edit_theme':
    $topic = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS_TOPIC." WHERE `id` = ? ", array(abs(num($_GET['topic']))));
    if (empty($topic)) {
        head(''. $lng['Mavzu topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    if ($topic['status'] == 1) {
        head(''. $lng['Mavzu yopilgan'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    head(''. $lng['Mavzuni tahrirlash'] .': ' . $topic['topic']);
             
    
    if ($_POST['edit']) {
        
        $name = html($_POST['name']);
        $uvedom = abs(num($_POST['uvedom']));
        $vote = html($_POST['vote']);
        $vote_1 = html($_POST['vote_1']);
        $vote_2 = html($_POST['vote_2']);
        $vote_3 = html($_POST['vote_3']);
        $vote_4 = html($_POST['vote_4']);
        $vote_5 = html($_POST['vote_5']);
        $vote_6 = html($_POST['vote_6']);
        $vote_7 = html($_POST['vote_7']);
        $vote_8 = html($_POST['vote_8']);
        $vote_9 = html($_POST['vote_9']);
        $vote_10 = html($_POST['vote_10']);
        
        if (empty($name)) {
            $err = ''. $lng['Mavzu nomini kiriting'] .'.<br />';
        }
        
        if (strlen($name) < 8) {
            $err .= ''. $lng['Mavzu nomi juda qisqa'] .'. [min. 8]<br />';
        }
    
        if (!empty($vote) && strlen($vote) < 20) {
            $err .= ''. $lng['So`rovnoma nomi juda qisqa'] .'. [min. 10]<br />';
        }
        
        if (!empty($vote) && (empty($vote_1) || empty($vote_2))) {
            $err .= ''. $lng['So`rovnomaning asosiy variantlarini to`ldiring'] .'';
        }
    
        if (empty($err)) {
    
            DB::$dbs->query("UPDATE ".GROUPS_TOPIC." SET `topic` = ?, `uvedom` = ?, `vote` = ?, `vote_1` = ?, `vote_2` = ?, `vote_3` = ?, `vote_4` = ?, `vote_5` = ?, `vote_6` = ?, `vote_7` = ?, `vote_8` = ?, `vote_9` = ?, `vote_10` = ? WHERE `id` = ? ", 
            array($name, $uvedom, $vote, $vote_1, $vote_2, $vote_3, $vote_4, $vote_5, $vote_6, $vote_7, $vote_8, $vote_9, $vote_10, $topic['id']));
    
            header("Location: ".HOME."/groups/topics/".$group['id']."/".$topic['id']."/");
        
        } else {
            echo DIV_ERROR . $err . CLOSE_DIV;
        }            
    }
    echo '<div class="white">';
    echo '<form action="#" enctype="multipart/form-data" method="POST">';
    echo '<b>'. $lng['Mavzu nomi'] .':</b> [min. 8]<br />
	<input type="text" name="name" value="'.$topic['topic'].'" style="width:95%;"/><br />';
    
    echo '<input type="checkbox" name="uvedom" value="1" '.($topic['uvedom'] ? 'checked' : NULL).' /> '. $lng['Mavzuni kuzatish'] .' <br/>';
    
    echo '</div>'; 
    echo '<div class="white">';
    echo '<b>'. $lng['So`rovnoma kiritish'] .'</b> ('. $lng['shart emas'] .')<br/>';
    echo '<b>'. $lng['Nomi'] .':</b> [min. 10]<br /><input type="text" name="vote" value="'.$topic['vote'].'" style="width:95%;"/><br />';
    echo '<b>'. $lng['Javob variantlari'] .':</b> <br/>
    <span class="mini">'. $lng['Kamida 2 ta maydoncha to`ldirilishi shart'] .'</span><br />';
    echo '<input type="text" name="vote_1" value="'.$topic['vote_1'].'"  placeholder="1" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_2" value="'.$topic['vote_2'].'" placeholder="2" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_3" value="'.$topic['vote_3'].'" placeholder="3" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_4" value="'.$topic['vote_4'].'" placeholder="4" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_5" value="'.$topic['vote_5'].'" placeholder="5" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_6" value="'.$topic['vote_6'].'" placeholder="6" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_7" value="'.$topic['vote_7'].'" placeholder="7" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_8" value="'.$topic['vote_8'].'" placeholder="8" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_9" value="'.$topic['vote_9'].'" placeholder="9" style="width:95%;"/><br />';
    echo '<input type="text" name="vote_10" value="'.$topic['vote_10'].'" placeholder="10" style="width:95%;"/><br /><br />';
    
    echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /><br />';
    echo '</form>';
    echo CLOSE_DIV;
    
    $forum = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS." WHERE `id` = ? ", array($theme['forum_id']));
    $forumc = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_CAT." WHERE `id` = ? ", array($theme['forumc_id']));
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/"><b>'.$topic['topic'].'</b></a><br/>'; 
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups/topics/'.$group['id'].'/">'. $lng['Muhokamalar'] .'</a> / <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
    
    case 'edit_post':
    $post = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS_POST." WHERE `id` = ? ", array(abs(num($_GET['post']))));
    $topic = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS_TOPIC." WHERE `id` = ? ", array($post['topic_id']));
    if (empty($topic)) {
        head(''. $lng['Mavzu topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    if (empty($post)) {
        head(''. $lng['Sharh topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    if ($post['user_id'] != $user['id'] && privilegy('group_moder') == FALSE) {
        head(''. $lng['Kirishda xatolik'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    if ($topic['status'] == 1) {
        head(''. $lng['Mavzu yopilgan'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    head(''. $lng['Sharhni tahrirlash'] .'');
    
    if ($_POST['edit']) {
        $msg = html($_POST['msg']);
        
        if (empty($msg)) {
            DIV_ERROR . ''. $lng['Habar kiriting'] .'' . CLOSE_DIV;
        } else {
            DB::$dbs->query("UPDATE ".GROUPS_POST." SET `post` = ? WHERE `id` = ? ", array($msg, $post['id']));
            header("Location: ".HOME."/groups/topics/".$group['id']."/".$topic['id']."/");  
        }
    }
    
    echo DIV_BLOCK;
    echo '<form action="#" method="POST">';
    echo '<b>'. $lng['Habar'] .':</b> [min. 20]<br />
	<textarea name="msg" style="width:95%;height:5pc;">'.$post['post'].'</textarea><br />';
    echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /><br />';
    echo '</form>';
    echo CLOSE_DIV;
    
    $forum = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS." WHERE `id` = ? ", array($post['forum_id']));
    $forumc = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_CAT." WHERE `id` = ? ", array($post['forumc_id']));
    $theme = DB::$dbs->queryFetch("SELECT * FROM ".FORUMS_THEME." WHERE `id` = ? ", array($post['theme_id']));
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/"><b>'.$topic['topic'].'</b></a><br/>'; 
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups/topics/'.$group['id'].'/">'. $lng['Muhokamalar'] .'</a> / <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
    
    case 'del_post':
    $post = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS_POST." WHERE `id` = ? ", array(abs(num($_GET['post']))));
    $topic = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS_TOPIC." WHERE `id` = ? ", array($post['topic_id']));
    if (empty($topic)) {
        head(''. $lng['Mavzu topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    if (empty($post)) {
        head(''. $lng['Sharh topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    if ($post['user_id'] != $user['id'] && privilegy('group_moder') == FALSE) {
        head(''. $lng['Kirishda xatolik'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    if ($topic['status'] == 1) {
        head(''. $lng['Mavzu yopilgan'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
    
    if (!empty($post['file'])) {
        unlink('../../files/groups/forum/'.$post['file']);
    }
    DB::$dbs->query("DELETE FROM ".GROUPS_POST." WHERE `id` = ? ", array($post['id']));
    header("Location: ".HOME."/groups/topics/".$group['id']."/".$topic['id']."/");    
    break;
    
    case 'new_post':
    $id = abs(num($_GET['topic']));
    $topic = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS_TOPIC." WHERE `id` = ? ", array($id));
    if (empty($topic)) {
        head(''. $lng['Mavzu topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }

    if ($topic['status'] == 1) {
        head(''. $lng['Mavzu yopilgan'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }
        
    head(''. $lng['Javob berish'] .': ' . $topic['topic']);
            
          

    
    if (!empty($_GET['post'])) {
        $post = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS_POST." WHERE `id` = ? ", array(abs(num($_GET['post']))));
        
        if ($post) {
            echo DIV_BLOCK;
            echo '<b>'. $lng['Sharhga javob berish'] .':</b><br />';
            echo '<b>' . userLink($post['user_id']) . '</b> [' . vrem($post['time']) . ']<br />';
            echo SubstrMaus(text($post['post']), 100);
            echo CLOSE_DIV;
        }
    }
    
    if (!empty($_GET['ctpost'])) {
        $post2 = DB::$dbs->queryFetch("SELECT * FROM ".GROUPS_POST." WHERE `id` = ? ", array(abs(num($_GET['ctpost']))));
        
        if ($post2) {
            echo DIV_BLOCK;
            echo '<b>'. $lng['Sharhga sitata keltirish'] .':</b><br />';
            echo '<b>' . userLink($post2['user_id']) . '</b> [' . vrem($post2['time']) . ']<br />';
            echo SubstrMaus(text($post2['post']), 100);
            echo CLOSE_DIV;
        }
    }
    
    if ($_POST['add']) {
    
        $msg = html($_POST['msg']);
        
        if (empty($msg)) {
            $err .= ''. $lng['Habar kiriting'] .'.<br />';
        }
    
        if (!empty($_FILES['file'])) {
            $name = $_FILES['file']['name']; # Fayl nomi
            $ext = strtolower(strrchr($name, '.')); # Fayl shakli
            $size = $_FILES['file']['size']; # Fayl hajmi
            $time = time();
            $file = $time.$ext;
    
            if ($size > (1048576 * $config['max_upload_groupа_file'])) {
                $err .= ''. $lng['Fayl hajmi belgilangan miqdordan ortmoqda'] .'. [Max. '.$config['max_upload_groupа_file'].'Mb.]<br />';
            }
                
            if (preg_match('/.phtml/i', $name) || preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess') {
                $err .= ''. $lng['Fayl shakli xato'] .'.<br />';
            }
        }
                
        if (empty($err)) {
            if (!empty($ext)) {
                copy($_FILES['file']['tmp_name'], '../../files/groups/forum/'.$time.$ext);
            }
            
            $file = (empty($ext) ? 0 : $file);
            
            if (!empty($post)) {
                $ank = DB::$dbs->queryFetch("SELECT `nick` FROM ".USERS." WHERE `user_id` = ?",array($post['user_id']));
                $msg = '[b]' . $ank['nick'] . '[/b], ' . $msg;
            }
            
            if (!empty($post2)) {
                $ct = $post2['id'];
            } else {
                $ct = '0';
            }
            
            new_posts($topic['id']);
            
            DB::$dbs->query("INSERT INTO ".GROUPS_POST." (`group_id`, `topic_id`, `post`, `user_id`, `time`, `file`, `ct`) VALUES 
            (?,?,?,?,?,?,?)", array($group['id'], $topic['id'], $msg, $user['user_id'], time(), $file, $ct));
            
            header("Location: ".HOME."/groups/topics/".$group['id']."/".$topic['id']."/");
        
        } else {
            echo DIV_ERROR . $err . CLOSE_DIV;
        }            
    }
    
    echo DIV_BLOCK;
    echo '<form action="#" enctype="multipart/form-data" method="POST">';
    echo '<b>'. $lng['Habar'] .':</b> [min. 20]<br />
	<textarea name="msg" style="width:95%;height:5pc;"></textarea><br />';
    echo '<b>'. $lng['Fayl biriktirish'] .':</b> [max. '.$config['max_upload_groupа_file'].'mb.]<br />
	<input type="file" name="file" style="width:95%;"/><br /><br />';
    echo '<input type="submit" name="add" value="'. $lng['Javob berish'] .'" /><br />';
    echo '</form>';
    echo CLOSE_DIV;
echo '<div class="lines">';
echo '- <a href="'.HOME.'/groups/topics/'.$group['id'].'/'.$topic['id'].'/"><b>'.$topic['topic'].'</b></a><br/>'; 
echo '- <a href="'.HOME.'/groups/'.$group['id'].'/">'.$group['name'].'</a><br/>'; 
echo '- <a href="'.HOME.'/groups/topics/'.$group['id'].'/">'. $lng['Muhokamalar'] .'</a> / <a href="'.HOME.'/groups">'. $lng['Guruhlar'] .'</a>'; 
echo '</div>';
    break;
}


require_once('../../core/stop.php');
?>