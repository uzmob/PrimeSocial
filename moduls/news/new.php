<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/

require_once('../../core/start.php');

check_auth();

head(''. $lng['Sayt yangiliklari'] .'');

if (isset($_GET['id'])) {

echo (privilegy('news') ? DIV_LI . '<a href="'.HOME.'/panel/news/"><b>'. $lng['Yangiliklarni boshqarish'] .'</b></a>' . CLOSE_DIV : NULL);
    
    $id = num($_GET['id']);
    $new = DB::$dbs->queryFetch("SELECT * FROM ".NEWS." WHERE `id` = ? ",array($id));
    
    if (empty($new)) {
        echo DIV_AUT . ''. $lng['Yangiliklar yo`q'] .'' . CLOSE_DIV;
    } else {     
        
        switch ($select) {
            
            default:
            echo DIV_BLOCK . ' <a href="'.HOME.'/news/comm/'.$new['id'].'/"> '.text($new['new']).'</a>';
            $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".NEWS_COMM." WHERE `new_id` = ?", array($new['id']));
            echo '<br/>'.icon('sharh.png').' <span class="mini">'.$comm.'</span>';
            echo CLOSE_DIV;
            break;
            
            case 'comm':
            if ($_POST) {
                $comm = html($_POST['comm']);
                
                if (empty($comm)) {
                    $err = ''. $lng['Sharh bo`sh'] .'';
                }
                
                if (!empty($err)) {
                    echo DIV_ERROR . $err . CLOSE_DIV;
                } else {
                    DB::$dbs->query("INSERT INTO ".NEWS_COMM." (`new_id`, `user_id`, `comm`, `time`) VALUES (?, ?, ?, ?)", array($id, $user['user_id'], $comm, time()));
                    balls_operation(3);
                    echo DIV_MSG . ''. $lng['Sharh kiritildi'] .'' . CLOSE_DIV;                    
                }
            }
            
            echo '<div class="white">';
echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>';
echo '<td class="grey" style="width:5%;border-radius: 6px 0 0 0 ;"><center>';
echo ''.icon('RSS-60.png',40,40).'';
echo '</center></td>';

echo '<td class="grey" style="width:95%;border-radius:  0 6px 0 0;">';

        echo ''.icon('efir.png').' <a href="'.HOME.'/news/comm/'.$new['id'].'/">' . $new['title'] . '</a><br/>
		<span class="mini">' . vrem($new['time']) . '</span></td></tr></table>';
		
		echo '<div class="white" style="box-shadow: 0 8px 10px rgba(162,162,162,0.25), 0 2px 4px rgba(162,162,162,0.22);margin-bottom: 5px;margin-left: 2px;margin-right: 2px;">';
    echo ''. text($new['new']) .'<br/>';
	
            $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".NEWS_COMM." WHERE `new_id` = ?", array($new['id']));
            echo '<br/>'.icon('sharh.png').' <span class="mini">'.$comm.'</span>';
			    
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".NEWS_RATING." WHERE `new_id` = ? && `user_id` = ? ", array($new['id'], $user['user_id'])) == FALSE) {
        echo '<span style="float:right;"> <a href="/news/index.php?select=rating&id='.$new['id'].'&type=like">'.icon('heart.png').' </a>  
		<a href="/news/index.php?select=rating&id='.$new['id'].'&type=not_like">'.icon('dislike.png').' </a></span><br />';
    } else {
        $like = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".NEWS_RATING." WHERE `new_id` = ? && `type` = ? ", array($new['id'], 'like'));
        $not_like = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".NEWS_RATING." WHERE `new_id` = ? && `type` = ? ", array($new['id'], 'not_like'));
        
        echo '<span style="float:right;font-size:11px;"> <u style="color:#5D9C75;font-size:15px;">' . (empty($new['rating']) ? '0' : $new['rating']) . '</u>  '.icon('heart.png').''.$like.' '.icon('dislike.png').' '.$not_like.'</span><br />';
    }
	echo '</div>';

	echo '</div>';
            if (!empty($_GET['del_comm'])) {
                DB::$dbs->query("DELETE FROM ".NEWS_COMM." WHERE `id` = ? ", array(num($_GET['del_comm'])));
                header("Location: ".HOME."/moduls/news/new.php?select=comm&id=".$id."&p=".(int)$_GET['p']);
            }
                        
            if (empty($comm)) {
                echo DIV_BLOCK . ''. $lng['Sharhlar yo`q'] .'' . CLOSE_DIV;
            } else {
                $n = new Navigator($comm,$config['write']['news_comm'],'select=comm&id='.$id); 
                $sql = DB::$dbs->query("SELECT * FROM ".NEWS_COMM." WHERE `new_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['news_comm']."", array($id));
                while($comm = $sql -> fetch()) {
				
echo '<div class="white">';
echo '<table cellspacing="0" cellpadding="0" style="margin-bottom:5px;" width="100%" ><tr>';
echo '<td class="grey" style="width:5%;border-radius: 6px 0 0 0 ;"><center>';
echo '' . avatar($comm['user_id'],40,40) . '';
echo '</center></td>';

echo '<td class="grey" style="width:95%;border-radius:  0 6px 6px 0;">';
echo ' ' . userLink($comm['user_id']) . ' 
<span style="float:right;">'. ($comm['user_id'] != $user['user_id'] ? '
		<a href="?otv='.$comm['user_id'].'"> '.icon('sharh.png').'</a> ' : NULL) . '  ' . (privilegy('news_comm_delete') ? ' <a href="'.HOME.'/moduls/news/new.php?select=comm&id='.$new['id'].'&del_comm='.$comm['id'].'&p='.(int)$_GET['p'].'">'.icon('minus2.png').'</a>' : NULL) . '';
echo '</span><br/><span class="mini">' . vrem($comm['time']) . '</span></td></tr></table>';
echo '<div class="white" style="box-shadow: 0 8px 10px rgba(162,162,162,0.25), 0 2px 4px rgba(162,162,162,0.22);margin-bottom: 5px;margin-left: 2px;margin-right: 2px;">';
echo ' ' . text($comm['comm']) . '</div>';
		echo ' </div>'; 
                }
                echo $n->navi();                   
            }
            
    echo '<div class="white">';
            echo '<form action="#" method="POST">';
            echo '<textarea name="comm" style="width:95%;height:4pc;"></textarea><br />';
            echo '<input type="submit" value="'. $lng['Sharh kiritish'] .'" />';
            bbsmile();
			echo '</form>';
            echo CLOSE_DIV;
            break;   

    case 'rating':
    $id = abs(intval($_GET['id']));
    $new = DB::$dbs->queryFetch("SELECT * FROM ".NEWS." WHERE `id` = ?",array($id));
    
    if (empty($new)) {
        head(''. $lng['Yangiliklar topilmadi'] .'');
        echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
        require_once('../../core/stop.php');
        exit();
    }   
    
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".NEWS_RATING." WHERE `new_id` = ? && `user_id` = ? ", array($new['id'], $user['user_id'])) == TRUE) {
        head(''. $lng['Siz ovoz bergansiz'] .'');
        echo DIV_BLOCK . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
        require_once('../../core/stop.php');
        exit();        
    }
    
    if ($_GET['type'] == 'like') {
        DB::$dbs->query("INSERT INTO ".NEWS_RATING." (`new_id`, `user_id`, `type`) VALUES (?, ?, ?)", array($new['id'], $user['user_id'], 'like'));
        DB::$dbs->query("UPDATE ".NEWS." SET `rating` = ? WHERE `id` = ? ", array(($new['rating'] + 1), $new['id']));
        
    } else {
        DB::$dbs->query("INSERT INTO ".NEWS_RATING." (`new_id`, `user_id`, `type`) VALUES (?, ?, ?)", array($new['id'], $user['user_id'], 'not_like'));
        DB::$dbs->query("UPDATE ".NEWS." SET `rating` = ? WHERE `id` = ? ", array(($new['rating'] - 1), $new['id']));
       
    }
    
    header("Location: ".HOME."/news/comm/".$new['id']."/");
    break;
                
        }
     
    }
    
}

require_once('../../core/stop.php');
?>