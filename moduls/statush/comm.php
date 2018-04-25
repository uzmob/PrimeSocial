<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();

$id = intval($_GET['id']);
$status = DB::$dbs->queryFetch("SELECT * FROM ".STATUS." WHERE `id` = ?",array($id));


if (empty($status)) {
    header("Location: ".HOME);
    exit();
}

switch ($_GET['act']) {
    
    default:
        header("Location: ".HOME);
        exit();
    break;
    
    case 'like':
    head(''. $lng['Baxolar'] .'');
    
    
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".STATUS_RATING." WHERE `user_id` = ? && `status_id` = ?", array($user['user_id'], $status['id'])) == TRUE) {
        echo DIV_LI . '<b>'. $lng['Statusga baxo berdingiz'] .'!</b>' . CLOSE_DIV;
    } else {
        $array_plus = array('+1','+2','+3','+4','+5');
        $array_minus = array('-1','-2','-3','-4','-5');
        
        if (isset($_GET['go'])) {
            $type = html($_GET['go']);
            $data = explode("_", $type);
            
            $err = array();
            if ($data[0] != 'plus' && $data[0] != 'minus') {
                $err[] = ''. $lng['Xatolik'] .'!';
            }
            
            if (empty($data[1]) || $data[1] > 5) {
                $err[] = ''. $lng['Xatolik'] .'!';    
            }
            
            if (!empty($err)) {
                echo DIV_ERROR;
                foreach ($err AS $value) {
                    echo $value . '<br />';
                }
                echo CLOSE_DIV;                
            } else {
                if ($data[0] == 'plus') {
                    $result = ($status['rating'] + $data[1]);

                    $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['statusingizni'] .' <b>+' . $data[1] . '</b> '. $lng['ga baxoladi'] .'.';
                } else {
                    $result = ($status['rating'] - $data[1]);
                    
                    $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['statusingizni'] .'statusingizni <b>-' . $data[1] . '</b> '. $lng['ga baxoladi'] .'.';
                }
                
                lenta($lenta, $status['user_id']);
                
                DB::$dbs->query("UPDATE ".STATUS." SET `rating` = ? WHERE `id` = ?",array($result, $status['id']));
                
                DB::$dbs->query("INSERT INTO ".STATUS_RATING." (`user_id`, `status_id`, `rating`, `type`, `time`) VALUES (?, ?, ?, ?, ?)", array($user['user_id'], $status['id'], $data[1], $data[0], time())); 
                
                
                header("Location: ?");
                exit();                  
            }        
            
        }
        echo '<div class="sts">';
        echo text($status['status']);
        echo CLOSE_DIV;
        echo DIV_AUT;
        echo '<b>'. $lng['Ushbu statusga baxoingiz'] .':</b><br /><br />';
            $key = 1;
            foreach ($array_plus AS $value) {
                echo '<a href="'.HOME.'/status/'.$status['id'].'/like/?go=plus_'.$key.'" style="color:#3EA100">'.$value.' </a>';
                ++$key;
            }
            echo ' </br> ';
            $key = 1;
            foreach ($array_minus AS $value) {
                echo '<a href="'.HOME.'/status/'.$status['id'].'/like/?go=minus_'.$key.'" style="color:#D66B53">'.$value.' </a>';
                ++$key;
            }        
        echo CLOSE_DIV;
    }
    echo DIV_AUT . '<b>'. $lng['Reyting tarihi'] .':</b>' . CLOSE_DIV;
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".STATUS_RATING." WHERE `status_id` = ?", array($status['id']));
    if ($all > 0) {
        $n = new navigator($all, 10, 'act=like&id='.$status['id']);
        $sql = DB::$dbs->query("SELECT * FROM ".STATUS_RATING." WHERE `status_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, 10 ", array($status['id']));
        while($post = $sql -> fetch()) {
            echo '<div class="lines"><b>' . vrem($post['time']) . '</b> ' . user_choice($post['user_id'], 'link') . ' '.($post['type'] == 'plus' ? '<span style="color: green">+'.$post['rating'].'</span> '. $lng['baxo berdi'] .'' : '<span style="color: red">+'.$post['rating'].'</span>').'</div>';
        }
        echo $n->navi(); 
        
    } else {
        echo DIV_AUT . ''. $lng['Hali hech kim ovoz bermagan'] .'' . CLOSE_DIV;
    } 
    echo DIV_LI . '- <a href="'.HOME.'/statush/'.$status['user_id'].'/">'. $lng['Statuslar tarihi'] .'</a>' . CLOSE_DIV;  
    echo DIV_LI . '- <a href="'.HOME.'/id'.$status['user_id'].'">'. $lng['Sahifaga qaytish'] .'</a>' . CLOSE_DIV;
    break;
    
    case 'comm':
    head(''. $lng['Statusga sharh'] .'');
    
    
        
        echo '<div class="sts">';
        echo text($status['status']);
        echo CLOSE_DIV;
		
		
    if (!empty($_POST['send']) && $user['chat_post'] >= $config['limit_PhotoComm']) {
        $comm = html($_POST['comm']);
        if (empty($comm)) {
            echo ERROR . ''. $lng['Bo`sh habar'] .'' . CLOSE_DIV;
        } else {
            $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['statusingizga habar yozdi'] .'';
            lenta($lenta, $status['user_id']);
            
            DB::$dbs->query("INSERT INTO ".STATUS_COMM." (`user_id`, `status_id`, `comm`, `time`) VALUES (?, ?, ?, ?)", array($user['user_id'], $status['id'], $comm, time())); 
            header("Location: ".HOME."/status/".$status['id']."/comm/");
            balls_operation(2);
        }
    }   
                             
        if (!empty($_GET['del'])) {
            $comm = DB::$dbs->queryFetch("SELECT * FROM ".STATUS_COMM." WHERE `id` = ? ORDER BY `id` DESC",array(num($_GET['del'])));
            if ($user['user_id'] == $comm['user_id'] || $ank['user_id'] == $user['user_id']) {
                DB::$dbs->query("DELETE FROM ".STATUS_COMM." WHERE `id` = ? ", array(num($_GET['del'])));
            }
        }

        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".STATUS_COMM." WHERE `status_id` = ?", array($status['id']));
        if ($all > 0) {
            $n = new navigator($all, 5, 'act=comm&id='.$status['id']);
            $sql = DB::$dbs->query("SELECT * FROM ".STATUS_COMM." WHERE `status_id` = ? ORDER BY `id` DESC LIMIT {$n->start()},5 ", array($status['id']));
            while($post = $sql -> fetch()) {
			echo '<div class="white">';
echo '<table cellspacing="0" cellpadding="0" style="margin-bottom:5px;" width="100%" ><tr>';
echo '<td class="grey" style="width:5%;border-radius: 6px 0 0 6px;"><center>';
echo '' . avatar($post['user_id'],40,40) . '';
echo '</center></td>';

echo '<td class="grey" style="width:95%;border-radius:  0 6px 6px 0;">';
echo '<span  style="float:right;"><a href="'.HOME.'/status/'.$id.'/comm/?otv='.$post['user_id'].'">'.icon('sharh.png').'</a> 
'.($user['user_id'] == $post['user_id'] || $ank['user_id'] == $user['id'] ? '<a href="'.HOME.'/status/'.$id.'/comm/?del='.$post['id'].'">'.icon('minus2.png').'</a>' : NULL) . '</span>
' . user_choice($post['user_id'], 'link') . ' <br/><span class="mini">' . vrem($post['time']) . '</span>
<br />';
echo '</td></tr></table>';
echo '' . text($post['comm']) . CLOSE_DIV;

            }
            echo $n->navi(); 
        } else {
            echo DIV_LI . ''. $lng['Sharhlar yo`q'] .'' . CLOSE_DIV;
        }      
                
        echo DIV_AUT;
            /* Foydalanuvchiga javob */
            if (!empty($_GET['otv'])) {
                $ank2 = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ?",array(num($_GET['otv'])));
                if (!empty($ank2)) {
                    $otv = '[b]'.$ank2['nick'].'[/b], ';
                }
            }
                
            if (!empty($ank2)) {
                echo ''. $lng['Foydalanuvchiga javob'] .': '.user_choice($ank2['user_id'], 'link').':<br />';
            }
            echo '<form action="#" method="POST">';
            echo '<textarea name="comm" style="width:95%;height:5pc;">'.(!empty($otv) ? $otv : NULL).'</textarea><br />';
            echo '<input type="submit" name="send" value="'. $lng['Yozish'] .'" />';
            bbsmile();
            echo '</form>';
     echo CLOSE_DIV;
     echo DIV_LI . '- <a href="'.HOME.'/statush/'.$status['user_id'].'/">'. $lng['Statuslar tarihi'] .'</a>' . CLOSE_DIV;  
     echo DIV_LI . '- <a href="'.HOME.'/id'.$status['user_id'].'">'. $lng['Sahifaga qaytish'] .'</a>' . CLOSE_DIV;
     break;
    
}        


require_once('../../core/stop.php');
?>