<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/

$id = abs(num($_GET['user']));
$ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ? ", array($id));

if (empty($ank)) {
    head(''. $lng['Foydalanuvchi topilmadi'] .'');
    echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;  
    exit();
}

$folder = DB::$dbs->queryFetch("SELECT * FROM ".FILES." WHERE `id` = ? ", array(abs(num($_GET['folder']))));
    
if (empty($folder)) {
    head(''. $lng['Bo`lim topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
	require_once('../../core/stop.php');
    exit(); 
} 

$file = DB::$dbs->queryFetch("SELECT * FROM ".FILES_FILE." WHERE `id` = ? ", array(abs(num($_GET['file']))));
if (empty($file)) {
    head(''. $lng['Fayl topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}   


if ($_POST) {
    $comm = html($_POST['comm']);
                
    if (empty($comm)) {
        $err = ''. $lng['Bo`sh sharh'] .'';
    }
                
    if (!empty($err)) {
        echo DIV_ERROR . $err . CLOSE_DIV;
    } else {
        DB::$dbs->query("INSERT INTO ".FILES_COMM." (`file_id`, `user_id`, `comm`, `time`) VALUES (?, ?, ?, ?)", array($file['id'], $user['user_id'], $comm, time()));
        echo DIV_MSG . ''. $lng['Sharh joylandi'] .'' . CLOSE_DIV;                    
    }
}
            
$comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".FILES_COMM." WHERE `file_id` = ?", array($file['id']));


if (!empty($_GET['del_comm'])) {
    DB::$dbs->query("DELETE FROM ".FILES_COMM." WHERE `id` = ? ", array(num($_GET['del_comm'])));
    header("Location: ".HOME."/moduls/files/comm.php?folder=".$folder['id']."&user=".$ank['user_id']."&file=".$file['id']."&".(int)$_GET['p']);
}
                        
if (empty($comm)) {
    echo DIV_BLOCK . ''. $lng['Sharhlar yo`q'] .'' . CLOSE_DIV;
} else {
    $n = new Navigator($comm,$config['write']['files_comm'],'folder='.$folder['id'].'&file='.$file['id']); 
    $sql = DB::$dbs->query("SELECT * FROM ".FILES_COMM." WHERE `file_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['files_comm']."", array($file['id']));
    
    while($comm = $sql -> fetch()) {
	
			
echo '<div class="white">';
echo '<table cellspacing="0" cellpadding="0" style="margin-bottom:5px;" width="100%" ><tr>';
echo '<td class="grey" style="width:5%;border-radius: 6px 0 0 6px;"><center>';
echo '' . avatar($comm['user_id'],40,40) . '';
echo '</center></td>';

echo '<td class="grey" style="width:95%;border-radius:  0 6px 6px 0;">';
echo '<b>' . userLink($comm['user_id']) . '</b>';
echo '<span style="float:right;"> 
' . ($ank['user_id'] == $user['user_id'] ? ' <a href="'.HOME.'/moduls/files/comm.php?folder='.$folder['id'].'&user='.$ank['user_id'].'&file='.$file['id'].'&del_comm='.$comm['id'].'&p='.(int)$_GET['p'].'">'.icon('minus2.png').'</a>' : NULL) . '
</span>';
echo '<br/><span style="font-size:11px;">' . vrem($comm['time']) . '</span><br/>';
echo '</td></tr></table>';

            echo '' . text($comm['comm']);            
            echo CLOSE_DIV;
    }
    echo $n->navi();                   
}
            
echo '<div class="white">';
echo '<b>'. $lng['Habar'] .':</b><br />';
echo '<form action="#" method="POST">';
echo '<textarea name="comm" style="width:95%;height:4pc;"></textarea><br />';
echo '<input type="submit" value="'. $lng['Sharh kiritish'] .'" />';
bbsmile();  
echo '</form>';
echo CLOSE_DIV;
            
?>