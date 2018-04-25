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
$ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ?",array($id));

if (empty($ank)) {
    header("Location: ".HOME);
    exit();
}

if (empty($ank['photo'])) {
    header("Location: ".HOME);
    exit();
}

switch ($_GET['act']) {
    
    default:
        header("Location: ".HOME);
        exit();
    break;
    
    case 'like':
    head(' '. $lng['Rasmga ovozlar'] .'  - ' . $ank['nick']);
    
    
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PHOTO_RATING." WHERE `user_id` = ? && `friend_id` = ?", array($user['user_id'], $ank['user_id'])) == TRUE) {
        echo DIV_LI . '<b>'. $lng['Ovoz bergansiz'] .'!</b>' . CLOSE_DIV;
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
                    $result = ($ank['photo_rating'] + $data[1]);
                    $rating = ($ank['rating'] + $data[1]);
                    
                    $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> <b>+' . $data[1] . '</b> '. $lng['ga baxo berdi'] .'  <a href="'.HOME.'/avatar/like/'.$ank['user_id'].'/"><b>'. $lng['avataringizga'] .'</b></a>';
                } else {
                    $result = ($ank['photo_rating'] - $data[1]);
                    $rating = ($ank['rating'] - $data[1]);
                    
                    $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a>  <b>-' . $data[1] . '</b> '. $lng['ga baxo berdi'] .'  <a href="'.HOME.'/avatar/like/'.$ank['user_id'].'/"><b>'. $lng['avataringizga'] .'</b></a>';
                }
                
                lenta($lenta, $ank['user_id']);
                
                DB::$dbs->query("UPDATE ".USERS." SET `photo_rating` = ?, `rating` = ? WHERE `user_id` = ?",array($result, $rating, $ank['user_id']));
                
                DB::$dbs->query("INSERT INTO ".PHOTO_RATING." (`user_id`, `friend_id`, `rating`, `type`, `time`) VALUES (?, ?, ?, ?, ?)", array($user['user_id'], $ank['user_id'], $data[1], $data[0], time())); 
                
                
                header("Location: ?");
                exit();                  
            }        
            
        }
        
        echo DIV_AUT;
        echo '' . avatar($ank['user_id'],70,70) . '';
        echo '<br /><b>'. $lng['Baxoingiz'] .':</b><br />';
            $key = 1;
            foreach ($array_plus AS $value) {
                echo '<a href="'.HOME.'/avatar/like/'.$ank['user_id'].'/?go=plus_'.$key.'">'.$value.' </a>';
                ++$key;
            }
            echo ' / ';
            $key = 1;
            foreach ($array_minus AS $value) {
                echo '<a href="'.HOME.'/avatar/like/'.$ank['user_id'].'/?go=minus_'.$key.'">'.$value.' </a>';
                ++$key;
            }        
        echo CLOSE_DIV;
    }
    echo DIV_LI . '<b>'. $lng['Reyting tarihi'] .':</b>' . CLOSE_DIV;
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".PHOTO_RATING." WHERE `friend_id` = ?", array($ank['user_id']));
    if ($all > 0) {
        echo DIV_AUT;
        $n = new navigator($all, 10, 'act=like&id='.$id);
        $sql = DB::$dbs->query("SELECT * FROM ".PHOTO_RATING." WHERE `friend_id` = ? ORDER BY `id` DESC LIMIT {$n->start()},10 ", array($ank['user_id']));
        while($post = $sql -> fetch()) {
            echo '<b>' . vrem($post['time']) . '</b> ' . user_choice($post['user_id'], 'link') . ' '.($post['type'] == 'plus' ? '<span style="color: green">+'.$post['rating'].'</span>' : '<span style="color: red">+'.$post['rating'].'</span>').' ga  ' . user_choice($post['friend_id'], 'link') . ' foydalanuvchiga baxo berdi<br />';
        }
        echo CLOSE_DIV;
        echo $n->navi(); 
    } else {
        echo DIV_AUT . ''. $lng['Hali hech kim baxo bermagan'] .'' . CLOSE_DIV;
    } 
    
    echo DIV_LI . '<a href="'.HOME.'/id'.$ank['user_id'].'">'. $lng['Sahifaga qaytish'] .'</a>' . CLOSE_DIV;
    break;
    
 
    
}        


require_once('../../core/stop.php');
?>