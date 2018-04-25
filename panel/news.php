<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();


if (privilegy('news') == FALSE) {
    header("Location: ".HOME."/panel");
    exit();
}
 

if (privilegy('news')) {
    switch ($select) {
            
        default:
        head(''. $lng['Sayt yangiliklari'] .'');
        if ($_GET['del']) {
            $del = num($_GET['del']);
            DB::$dbs->query("DELETE FROM ".NEWS." WHERE `id` = ?",array($del));
            DB::$dbs->query("DELETE FROM ".NEWS_COMM." WHERE `new_id` = ?",array($del));
            echo DIV_MSG . ''. $lng['Yangilik o`chirildi'] .'' . CLOSE_DIV;
        }
        
        if (!empty($_POST['sett'])) {
            $news = num($_POST['news']);
            $comm = num($_POST['comm']);
            
            if (empty($news) || empty($comm)) {
                echo DIV_ERROR . ''. $lng['Bo`sh tarkib'] .'' . CLOSE_DIV;
            } else {
                DB::$dbs->query("UPDATE ".CONFIG." SET `write_news` = ?, `write_news_comm` = ? ", array($news, $comm));
                header("Location: ".HOME."/panel/news/");
            }
        }
    
        if ($_POST['add']) {
            $title = html($_POST['title']);
            $afisha = html($_POST['afisha']);
            $new = html($_POST['new']);
            
            if (strlen($title) < 10) {
                $err .= ''. $lng['Yangilik nomi juda qisqa. kamida 10 belgi bo`lishi kerak'] .'<br />';
            }
            
            if (strlen($new) < 20) {
                $err .= ''. $lng['Juda qisqa yangilik matni. kamida 20 belgi bo`lishi kerak'] .'<br />';
            }
            
            if (empty($afisha)) {
                $err .= ''. $lng['Bosh sahifaga ko`rinadigan yangilik matni to`ldirilmadi'] .'<br />';
            }
            
            if ($err) {
                echo DIV_ERROR . $err . CLOSE_DIV;
            } else {
                DB::$dbs->query("INSERT INTO ".NEWS." (`title`, `afisha`, `new`, `time`, `user_id`) VALUES (?, ?, ?, ?, ?)", array($title, $afisha, $new, time(), $user['user_id']));
                echo DIV_MSG . ''. $lng['Yangilik qo`shildi'] .'' . CLOSE_DIV;
            }
        }
        
        echo '<div class="lines">';
        echo '<form action="#" method="POST">';
        echo ''. $lng['Nomi'] .': [max. 100]<br /><input type="text" name="title" style="width:95%"/><br /><br />';
        echo ''. $lng['Bosh sahifa uchun'] .':<br /><textarea name="afisha" cols="30" rows="6" style="width:95%"></textarea><br />';
        echo ''. $lng['Yangilik'] .':<br /><textarea name="new" cols="30" rows="6" style="width:95%"></textarea><br />';
        echo '<input type="submit" name="add" value="'. $lng['Kiritish'] .'" /></form>';
        echo CLOSE_DIV;  
        
        
        echo '<div class="grey">';
        echo ''. $lng['Sahifadagi yangiliklar soni'] .':<br />';
        echo '<form action="#" method="POST">';
        echo '<select name="news" style="width:95%">';
        echo '<option '.(1 == $config['write']['news'] ? 'selected="selected"' : NULL).' value="1">1</option>';
        echo '<option '.(3 == $config['write']['news'] ? 'selected="selected"' : NULL).' value="3">3</option>';
        echo '<option '.(5 == $config['write']['news'] ? 'selected="selected"' : NULL).' value="5">5</option>';
        echo '<option '.(10 == $config['write']['news'] ? 'selected="selected"' : NULL).' value="10">10</option>';
        echo '<option '.(20 == $config['write']['news'] ? 'selected="selected"' : NULL).' value="20">20</option>';
        echo '<option '.(30 == $config['write']['news'] ? 'selected="selected"' : NULL).' value="30">30</option>';
        echo '</select><br />';
        
        echo ''. $lng['Sahifadagi sharhlar'] .':<br />';
        echo '<select name="comm" style="width:95%">';
        echo '<option '.(5 == $config['write']['news_comm'] ? 'selected="selected"' : NULL).' value="5">5</option>';
        echo '<option '.(10 == $config['write']['news_comm'] ? 'selected="selected"' : NULL).' value="10">10</option>';
        echo '<option '.(15 == $config['write']['news_comm'] ? 'selected="selected"' : NULL).' value="15">15</option>';
        echo '<option '.(20 == $config['write']['news_comm'] ? 'selected="selected"' : NULL).' value="20">20</option>';
        echo '<option '.(30 == $config['write']['news_comm'] ? 'selected="selected"' : NULL).' value="30">30</option>';
        echo '<option '.(40 == $config['write']['news_comm'] ? 'selected="selected"' : NULL).' value="40">40</option>';
        echo '<option '.(50 == $config['write']['news_comm'] ? 'selected="selected"' : NULL).' value="50">50</option>';
        echo '</select><br />';
        
        echo '<input type="submit" name="sett" value="'. $lng['Saqlash'] .'" /></form>';
        echo CLOSE_DIV;   
    
        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".NEWS."");
        
        if ($all == 0) {
            echo DIV_AUT . ''. $lng['Yangiliklar yo`q'] .'' . CLOSE_DIV;
        } else {
            $n = new Navigator($all,$config['write']['news'],''); 
            $sql = DB::$dbs->query("SELECT * FROM ".NEWS." ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['news']."");
            while($new = $sql -> fetch()) {
                echo DIV_AUT;
                echo '<b>'. $lng['Sarlavha'] .':</b> ' . $new['title'] . '<br /><br />';
                echo '<b>'. $lng['Bosh sahifaga'] .':</b> ' . text($new['afisha']) . '<br /><br />';  
                echo '<b>'. $lng['Yangilik'] .':</b> ' . text($new['new']) . '<br /><br />'; 
                echo CLOSE_DIV;
                
                echo DIV_LI;
                $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".NEWS_COMM." WHERE `new_id` = ?", array($new['id']));
                echo '<a href="'.HOME.'/panel/news/comm/'.$new['id'].'/">'. $lng['Sharhlar'] .': <b>'.$comm.'</b></a>';
                echo CLOSE_DIV;
                
                echo DIV_LI;
                echo '<a href="'.HOME.'/panel/news/?del='.$new['id'].'">['. $lng['O`chirish'] .']</a> 
				<a href="'.HOME.'/panel/news/edit/'.$new['id'].'/">['. $lng['Tahrirlash'] .']</a>';
                echo CLOSE_DIV;
            }
            echo $n->navi();         
        }
        
        break;
        
        case 'edit':
        head(''. $lng['O`zgartirish'] .'');
        $id = num($_GET['id']);
        
        if ($_POST) {
            $title = html($_POST['title']);
            $afisha = html($_POST['afisha']);
            $new = html($_POST['new']);
            
            if (strlen($title) < 10) {
                $err .= ''. $lng['Sarlavha nomi juda qisqa. Kamida 10 belgi'] .'<br />';
            }
            
            if (strlen($new) < 20) {
                $err .= ''. $lng['Yangilik matni juda qisqa.  Kamida 20 belgi'] .'<br />';
            }
            
            if (empty($afisha)) {
                $err .= ''. $lng['Bosh sahifaga maydonchasi to`ldirilmadi'] .'<br />';
            }
            
            if ($err) {
                echo DIV_ERROR . $err . CLOSE_DIV;
            } else {
                DB::$dbs->query("UPDATE ".NEWS." SET `title` = ?, `afisha` = ?, `new` = ? WHERE `id` = ?", array($title, $afisha, $new, $id));
                echo DIV_MSG . ''. $lng['Yangilik o`zgartirildi'] .'' . CLOSE_DIV;
            }
        }
        
        $new = DB::$dbs->queryFetch("SELECT `title`, `afisha`, `new` FROM ".NEWS." WHERE `id` = ?",array($id));
        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo ''. $lng['Sarlavha'] .': [max. 100]<br /><input type="text" value="'.$new['title'].'" name="title"  style="width:95%"/><br /><br />';
        echo ''. $lng['Bosh sahifaga'] .':<br /><textarea name="afisha" cols="30" rows="6" style="width:95%">'.$new['afisha'].'</textarea><br />';
        echo ''. $lng['Yangilik'] .':<br /><textarea name="new" cols="30" rows="6" style="width:95%">'.$new['new'].'</textarea><br />';
        echo '<input type="submit" value="'. $lng['O`zgartirish'] .'" /></form>';
        echo CLOSE_DIV;      
        break;
        
        case 'comm':
        head(''. $lng['Yangilikga sharh'] .'');
        $id = num($_GET['id']);
        $new = DB::$dbs->queryFetch("SELECT * FROM ".NEWS." WHERE `id` = ? ",array($id));
        
        $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".NEWS_COMM." WHERE `new_id` = ?", array($new['id']));


            if (!empty($_GET['del_comm'])) {
                DB::$dbs->query("DELETE FROM ".NEWS_COMM." WHERE `id` = ? ", array(num($_GET['del_comm'])));
                header("Location: ".HOME."/panel/news.php?select=comm&id=".$id."&p=".(int)$_GET['p']);
            }
            
            
            if (!empty($_GET['edit_comm'])) {
                $comm2 = DB::$dbs->queryFetch("SELECT * FROM ".NEWS_COMM." WHERE `id` = ?",array(num($_GET['edit_comm'])));
                if ($_POST) {
                    $msg = html($_POST['comm']);
                    DB::$dbs->query("UPDATE ".NEWS_COMM." SET `comm` = ? WHERE `id` = ?", array($msg, num($_GET['edit_comm'])));
                    header("Location: ".HOME."/panel/news.php?select=comm&id=".$id."&p=".(int)$_GET['p']);
                }
                
                echo DIV_AUT;
                echo '<form action="news.php?select=comm&id='.$new['id'].'&edit_comm='.$comm2['id'].'&p='.(int)$_GET['p'].'" method="POST">';
                echo ''. $lng['Sharhni o`zgartirish'] .':<br /><textarea name="comm">'.$comm2['comm'].'</textarea><br />';
                echo '<input type="submit" value="'. $lng['O`zgartirish'] .'" /></form>';
                echo CLOSE_DIV;                
            }
                        
            if (empty($comm)) {
                echo DIV_BLOCK . ''. $lng['Sharhlar yo`q'] .'' . CLOSE_DIV;
            } else {
                $n = new Navigator($comm,$config['write']['news_comm'],'select=comm&id='.$id); 
                $sql = DB::$dbs->query("SELECT * FROM ".NEWS_COMM." WHERE `new_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['news_comm']."", array($id));
                while($comm = $sql -> fetch()) {
                    echo DIV_LI . userLink($comm['user_id']) . ' 
					' . (privilegy('news_comm_delete') ? ' <a href="news.php?select=comm&id='.$new['id'].'&del_comm='.$comm['id'].'&p='.(int)$_GET['p'].'" style="float:right;">'.icon('minus2.png').'</a> <a href="news.php?select=comm&id='.$new['id'].'&edit_comm='.$comm['id'].'&p='.(int)$_GET['p'].'" style="float:right;">'.icon('pen2.png').'</a>' : NULL) . '
					<br/><span class="mini">' . vrem($comm['time']) . '</span><br/>';
                    echo '' . text($comm['comm']) . CLOSE_DIV;
                }
                echo $n->navi();                   
            }        
        break;
    }
} else {
    echo DIV_BLOCK . ''. $lng['Kirishda xatolik'] .'' . CLOSE_DIV;
}

echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>