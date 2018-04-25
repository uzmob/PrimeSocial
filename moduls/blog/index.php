<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');


switch ($select) {
    
    default:
    head(''. $lng['Blog'] .'');
    
    
    
    /* Tartiblash */
    if (!empty($_GET['sort'])) {
        if ($_GET['sort'] == 'date') {
            unset($_SESSION['sort']);
        } elseif ($_GET['sort'] == 'rating') {
            $_SESSION['sort'] = 'rating';
        } elseif ($_GET['sort'] == 'name') {
            $_SESSION['sort'] = 'name';
        } else {
            NULL;
        }
        
        header("Location: ".HOME."/blog/");
    }
    
    echo DIV_LI;
    echo ''. $lng['Tartiblash'] .': ' . (empty($_SESSION['sort']) ? '<b>'. $lng['sana'] .'</b>' : '<a href="'.HOME.'/blog/?sort=date">'. $lng['sana'] .'</a>') . ' | ' .
    ($_SESSION['sort'] == 'rating' ? '<b>'. $lng['mashhurlik'] .'</b>' : '<a href="'.HOME.'/blog/?sort=rating">'. $lng['mashhurlik'] .'</a>') . ' | ' .
    ($_SESSION['sort'] == 'name' ? '<b>'. $lng['nom'] .'</b>' : '<a href="'.HOME.'/blog/?sort=name">'. $lng['nom'] .'</a>');
    echo CLOSE_DIV;

    if ($_SESSION['sort'] == 'popular') {
        $sort = 'ORDER BY `rating` DESC';
    } elseif ($_SESSION['sort'] == 'name') {
        $sort = 'ORDER BY `title` DESC';
    } else {
        $sort = 'ORDER BY `time` DESC';
    }
    /* */
    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLOG."");
    if ($all == 0) {
        echo DIV_BLOCK . ''. $lng['Bloglar hali ochilmagan'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['blog'],''); 
        $sql = DB::$dbs->query("SELECT * FROM ".BLOG." ".$sort." LIMIT {$n->start()}, ".$config['write']['blog']."");
        while($blog = $sql -> fetch()) {

    
            echo DIV_BLOCK .  ''.icon('pages.png').' <a href="'.HOME.'/blog/'.$blog['id'].'/"><b>' . $blog['title'] . '</b></a>
			<span style="float:right;"> ' . userLink($blog['user_id']) . '</span><br />';
			 echo SubstrMaus(text($blog['blog']), 100);
			
    echo '<br/><span style="font-size:11px;color:#757575;">' . vrem($blog['time']) . '</span>';
           
            echo CLOSE_DIV;
        }
		
	
        echo $n->navi();        
    }
	if($user){
    echo DIV_BLOCK . '<form action="'.HOME.'/blog/new/" method="POST"><input type="submit" value="'. $lng['Yangi blog'] .'" /></form>' . CLOSE_DIV;
     } else {
	 }
	break;  
    
    case 'add':
    head(''. $lng['Yangi blog'] .'');
    
    if ($_POST['add']) {
        $title = html($_POST['title']);
        $blog = html($_POST['blog']);        
        
        if (empty($title) || empty($blog)) {
            echo DIV_ERROR . ''. $lng['Barcha maydonchalarni to`ldiring'] .'' . CLOSE_DIV;
        } else {
            DB::$dbs->query("INSERT INTO ".BLOG." (`title`, `blog`, `user_id`, `time`) VALUES (?,?,?,?)", array($title, $blog, $user['user_id'], time()));
            $lastid = DB::$dbs->lastInsertId();
            balls_operation(5);
            header("Location: ".HOME."/blog/".$lastid."/");
        }
    }
    
     

    echo DIV_AUT;
    echo '<form action="#" method="POST">';
    echo ''. $lng['Nomi'] .': [max. 100]<br /><input type="text" name="title" /><br /><br />';
    echo ''. $lng['Matni'] .':<br /><textarea name="blog" cols="30" rows="6"></textarea><br />';
    echo '<input type="submit" name="add" value="'. $lng['Yaratish'] .'" /></form>';
    echo CLOSE_DIV;
	bbsmile();
	break;  
    
    case 'view':
    $id = abs(intval($_GET['id']));
    $blog = DB::$dbs->queryFetch("SELECT * FROM ".BLOG." WHERE `id` = ?",array($id));
    
    if (empty($blog)) {
        head(''. $lng['Blog topilmadi'] .': ');
        echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
        require_once('../../core/stop.php');
        exit();
    }

    head('' . $blog['title']);
    if (isset($_POST['add'])) {
        $comm = html($_POST['comm']);
        
        if (empty($comm)) {
            echo DIV_ERROR . ''. $lng['Bo`sh sharh'] .'' . CLOSE_DIV;
        } else {
            DB::$dbs->query("INSERT INTO ".BLOG_COMM." (`blog_id`, `user_id`, `comm`, `time`) VALUES (?, ?, ?, ?)", array($blog['id'], $user['user_id'], $comm, time()));
            
            $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['Sizning blogingizda sharh yozdi'] .' | <a href="'.HOME.'/blog/'.$blog['id'].'/"><b> >>> </b> </a>';
            lenta($lenta, $blog['user_id']);
            
            header("Location: ".HOME."/blog/".$blog['id']."/");
        }
		
		if (!empty($_GET['otv']) && $_GET['otv'] != $user['user_id']) {
        $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM ".USERS." WHERE `user_id` = ? ",array(abs(num($_GET['otv']))));
        if (!empty($ank)) {
            $msg = '[b]' . $ank['nick'] . '[/b], ' . $msg;
        }
        
        $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['sizning habaringizga javob berdi'] .' | <a href="'.HOME.'/blog/'.$blog['id'].'/"><b> >>> </b></a>';
        lenta($lenta, $ank['user_id']);
    } 
	
    }
    
    if (!empty($_GET['delcomm'])) {
        $comm = DB::$dbs->queryFetch("SELECT * FROM ".BLOG_COMM." WHERE `id` = ? ", array(abs(num($_GET['delcomm']))));
        
        if ($comm['user_id'] == $user['user_id'] || $blog['user_id'] == $user['user_id'] || privilegy('blog_moder')) {
            DB::$dbs->query("DELETE FROM ".BLOG_COMM." WHERE `id` = ? ", array(abs(num($_GET['delcomm']))));
        }
        
        header("Location: ".HOME."/blog/".$blog['id']."/");
    }

    if (!empty($_GET['editcomm'])) {

        $comm = DB::$dbs->queryFetch("SELECT * FROM ".BLOG_COMM." WHERE `id` = ? ", array(abs(num($_GET['editcomm']))));
        
        if ($comm['user_id'] == $user['user_id'] || privilegy('blog_moder')) {        
            if (isset($_POST['edit'])) {
                $comm = html($_POST['comm']);
                
                if (empty($comm)) {
                    echo DIV_ERROR . ''. $lng['Bo`sh sharh'] .'' . CLOSE_DIV;
                } else {
                    DB::$dbs->query("UPDATE ".BLOG_COMM." SET `comm` = ? WHERE `id` = ? ", array($comm, abs(num($_GET['editcomm']))));
                    header("Location: ".HOME."/blog/".$blog['id']."/");
                }
                
            }

            echo DIV_AUT;
            echo '<form action="#" method="POST">';
            echo '<b>'. $lng['Sharhni tahrirlash'] .':</b><br /><textarea name="comm">'.$comm['comm'].'</textarea><br />';
            echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'"/>';
            echo '</form>';
            echo CLOSE_DIV;            
        }

    }
    
    if (isset($_GET['delete']) && ($blog['user_id'] == $user['user_id'] || privilegy('blog_moder'))) {
        if (!isset($_GET['go'])) {
            echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlang'] .':</b> <a href="?delete&go">['. $lng['O`chirish'] .']</a> <a href="'.HOME.'/blog/'.$blog['id'].'/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
        } else {
            DB::$dbs->query("DELETE FROM ".BLOG_COMM." WHERE `blog_id` = ? ", array($blog['id']));
            DB::$dbs->query("DELETE FROM ".BLOG_RATING." WHERE `blog_id` = ? ", array($blog['id']));
            DB::$dbs->query("DELETE FROM ".BLOG." WHERE `id` = ? ", array($blog['id']));
            header("Location: ".HOME."/blog/"); 
        }         
    } 
           
    
    
    echo DIV_BLOCK . ''.icon('pages.png').' <b>' . $blog['title'] . '</b>
	<br/><span class="mini">' . vrem($blog['time']) . '</span><p>' .text($blog['blog']) . '</p>' . CLOSE_DIV;
    
    echo DIV_BLOCK . '';
    ?>
<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
<script src="//yastatic.net/share2/share.js"></script>
<div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,twitter,viber,whatsapp,telegram" data-size="s" style="margin-bottom:5px;"></div>
	<?
	if($user){
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLOG_RATING." WHERE `blog_id` = ? && `user_id` = ? ", array($blog['id'], $user['user_id'])) == FALSE) {
        echo '<b>'. $lng['Menga'] .'</b>: <a href="'.HOME.'/blog/'.$blog['id'].'/like/">'. $lng['yoqdi'] .'</a> / 
		<a href="'.HOME.'/blog/'.$blog['id'].'/not_like/">'. $lng['yoqmadi'] .'</a><br />';
    } else {
        $like = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLOG_RATING." WHERE `blog_id` = ? && `type` = ? ", array($blog['id'], 'like'));
        $not_like = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLOG_RATING." WHERE `blog_id` = ? && `type` = ? ", array($blog['id'], 'not_like'));
        
        echo '<b>'. $lng['Blog reytingi'] .'</b>: ' . (empty($blog['rating']) ? '0' : $blog['rating']) . ' (+'.$like.'/-'.$not_like.')<br />';
    }
    } else {
	}
    $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLOG_COMM." WHERE `blog_id` = ? ", array($blog['id']));
    echo '<b>'. $lng['Sharhlar'] .'</b>: '.$comm.' ';
	
	    if ($blog['user_id'] == $user['user_id'] || privilegy('blog_moder')) {
        echo ' | <a href="'.HOME.'/blog/'.$blog['id'].'/?delete"><u>'. $lng['O`chirish'] .'</u></a> | 
		<a href="'.HOME.'/blog/'.$blog['id'].'/edit/"><u>'. $lng['Tahrirlash'] .'</u></a>';   
    }    
	
    echo CLOSE_DIV;
	
    if (empty($comm)) {
       echo '<div class="white">'. $lng['Sharhlar yo`q'] .'.' . CLOSE_DIV;
    } else {
        $n = new Navigator($comm,$config['write']['blog_comm'],'select=view&id='.$blog['id']);
        $sql = DB::$dbs->query("SELECT * FROM ".BLOG_COMM." WHERE `blog_id` = ? ORDER BY `id` DESC  LIMIT {$n->start()}, ".$config['write']['blog_comm']."", array($blog['id']));
        while($comm = $sql -> fetch()) {
		
echo '<div class="white">';
echo '<table cellspacing="0" cellpadding="0" style="margin-bottom:5px;" width="100%" ><tr>';
echo '<td class="grey" style="width:5%;border-radius: 6px 0 0 6px;"><center>';
echo '' . avatar($comm['user_id'],40,40) . '';
echo '</center></td>';

echo '<td class="grey" style="width:95%;border-radius:  0 6px 6px 0;">';
$bsharh = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLOG_COMM." WHERE `user_id` = ?", array($comm['user_id']));
echo '<b>' . userLink($comm['user_id']) . '</b> <span style="font-size:12px;font-weight:bold;">(' . $bsharh . ')</span>';
if($user){
echo '<span style="float:right;">  '. ($comm['user_id'] != $user['user_id'] ? '
		<a href="?otv='.$comm['user_id'].'"> '.icon('sharh.png').'</a> ' : NULL) . '  ';
echo ''.(privilegy('blog_moder') || $comm['user_id'] == $user['user_id'] || $blog['user_id'] == $user['user_id'] ? '<a href="'.HOME.'/moduls/blog/index.php?select=view&id='.$blog['id'].'&delcomm='.$comm['id'].'">'.icon('minus2.png').'</a>' : NULL). (privilegy('blog_moder') || $comm['user_id'] == $user['user_id'] ? ' <a href="'.HOME.'/moduls/blog/index.php?select=view&id='.$blog['id'].'&editcomm='.$comm['id'].'">'.icon('pen2.png').'</a>' : NULL) .'';
echo'</span><br/>';
	 } else {
	 }
echo '<span style="font-size:11px;">' . vrem($comm['time']) . '</span><br/>';
echo '</td></tr></table>';
		
            echo '' . text($comm['comm']);            
            echo CLOSE_DIV;
        }
        echo $n->navi();
         
    }
    
    
	
    
    
	if($user){
    echo '<div class="white">';
if (!empty($_GET['otv'])) {
    $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM ".USERS." WHERE `user_id` = ? ",array(abs(num($_GET['otv']))));
    if (!empty($ank) && $ank['user_id'] != $user['id']) {
        echo ' <b>' . $ank['nick'] . '</b> '. $lng['Habar'] .' <br />';
    } else {
        echo '<b>'. $lng['Habar'] .':</b><br />';
    }
}
    echo '<form action="'.(isset($_GET['otv']) ? '?otv='.(int)$_GET['otv'] : NULL).'" enctype="multipart/form-data" method="POST">';
    echo '<textarea name="comm" style="width:95%;height:4pc;"></textarea>';
    echo CLOSE_DIV;
	
    echo '<div class="white">';
    echo '<input type="submit" name="add" value="'. $lng['Sharh kiritish'] .'"/>';
	bbsmile();
    echo '</form>';
	echo CLOSE_DIV;
	 } else {
	 }
    break;
   
    case 'edit':
    $id = abs(intval($_GET['id']));
    $blog = DB::$dbs->queryFetch("SELECT * FROM ".BLOG." WHERE `id` = ?",array($id));
    
    if (empty($blog)) {
        head(''. $lng['Blog topilmadi'] .'');
        echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
        require_once('../../core/stop.php');
        exit();
    }
    
    if ($blog['user_id'] != $user['user_id'] && privilegy('blog_moder') == FALSE) {
        head(''. $lng['Kirishda xatolik'] .': ');
        echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
        require_once('../../core/stop.php');
        exit();        
    }
    head(''. $lng['Tahrirlash'] .':  ' . $blog['title']);
    
    if ($_POST['edit']) {
        $title = html($_POST['title']);
        $blog1 = html($_POST['blog']);        
        
        if (empty($title) || empty($blog1)) {
            echo DIV_ERROR . ''. $lng['Barcha maydonchalarni to`ldiring'] .'' . CLOSE_DIV;
        } else {
            DB::$dbs->query("UPDATE ".BLOG." SET `title` = ?, `blog` = ? WHERE `id` = ? ", array($title, $blog1, $blog['id']));
            header("Location: ".HOME."/blog/".$blog['id']."/");
        }
    }
    

    echo DIV_AUT;
    echo '<form action="#" method="POST">';
    echo ''. $lng['Nomi'] .': [max. 100]<br /><input type="text" name="title" value="'.$blog['title'].'" /><br /><br />';
    echo ''. $lng['Matni'] .':<br /><textarea name="blog" cols="30" rows="6">'.$blog['blog'].'</textarea><br />';
    echo '<input type="submit" name="edit" value="'. $lng['Tayyor'] .'!" /></form>';
    echo CLOSE_DIV;  
    
    bbsmile();
            
    break;
    
    case 'rating':
    $id = abs(intval($_GET['id']));
    $blog = DB::$dbs->queryFetch("SELECT * FROM ".BLOG." WHERE `id` = ?",array($id));
    
    if (empty($blog)) {
        head(''. $lng['Blog topilmadi'] .'');
        echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
        require_once('../../core/stop.php');
        exit();
    }   
    
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".BLOG_RATING." WHERE `blog_id` = ? && `user_id` = ? ", array($blog['id'], $user['user_id'])) == TRUE) {
        head(''. $lng['Siz ovoz bergansiz'] .'');
        echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
        require_once('../../core/stop.php');
        exit();        
    }
    
    if ($_GET['type'] == 'like') {
        DB::$dbs->query("INSERT INTO ".BLOG_RATING." (`blog_id`, `user_id`, `type`) VALUES (?, ?, ?)", array($blog['id'], $user['user_id'], 'like'));
        DB::$dbs->query("UPDATE ".BLOG." SET `rating` = ? WHERE `id` = ? ", array(($blog['rating'] + 1), $blog['id']));
        
        $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['ga sizning blogingiz yoqdi'] .' | <a href="'.HOME.'/blog/'.$blog['id'].'/"><b> >>> </b></a>';
        lenta($lenta, $blog['user_id']);
    } else {
        DB::$dbs->query("INSERT INTO ".BLOG_RATING." (`blog_id`, `user_id`, `type`) VALUES (?, ?, ?)", array($blog['id'], $user['user_id'], 'not_like'));
        DB::$dbs->query("UPDATE ".BLOG." SET `rating` = ? WHERE `id` = ? ", array(($blog['rating'] - 1), $blog['id']));
        
        $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['ga sizning blogingiz yoqmadi'] .' | <a href="'.HOME.'/blog/'.$blog['id'].'/"><b> >>> </b></a>';
        lenta($lenta, $blog['user_id']);
    }
    
    header("Location: ".HOME."/blog/".$blog['id']."/");
    break;
    
}

    
require_once('../../core/stop.php');
?>