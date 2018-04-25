<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

head(''. $lng['Foydalanuvchilarni boshqarish'] .'');


$id = num($_GET['id']);
$page = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ?",array($id));

if (empty($page)) {
    $err .= ''. $lng['Foydalanuvchi topilmadi'] .'<br />';       
}
 

if ($err) {
    echo DIV_ERROR . $err . CLOSE_DIV;
} else {
    
    switch ($select) {
        
        default:
        
        break;
        
        case 'edit':
        if (privilegy('anceta_update', $id)) {
            if ($_POST) {
                $surname = html($_POST['surname']);
                $name = html($_POST['name']);
                $gender = num($_POST['gender']);
                $age = num($_POST['age']);
                $level = num($_POST['level']);
                        
                if (empty($surname) || empty($name)) {
                    $err .= ''. $lng['Hamma maydonchalarni to`ldiring'] .'<br />';
                }
                        
                if (strlen($surname) < 2) {
                    $err .= ''. $lng['Juda qisqa familiya'] .'. [Min. 2]<br />';
                }
                        
                if (strlen($name) < 2) {
                    $err .= ''. $lng['uda qisqa nom'] .'J. [Min. 2]<br />';
                }
                        
                if ($age > 75 || 10 > $age) {
                    $err .= ''. $lng['Yosh to`g`ri ko`rsatilmadi'] .'<br />';
                }
                
                if ($err) {
                    echo DIV_ERROR . $err . CLOSE_DIV;
                } else {
                    DB::$dbs->query("UPDATE ".USERS." SET `surname` = ?, `name` = ?, `gender` = ?, `age` = ?, `level` = ? WHERE `user_id` = ?",array($surname, $name, $gender, $age, $level, $page['user_id']));
                    echo DIV_MSG . ''. $lng['Ma`lumotlar muvaffaqiyatli yangilandi'] .'' . CLOSE_DIV;
                }
                                
            }
            
            echo DIV_BLOCK;
            echo '<form action="#" method="POST">';
            echo ''. $lng['Familiya'] .':<br /><input type="text" name="surname" value="'.$page['surname'].'" style="width:95%;"/><br /><br />';
            echo ''. $lng['Ism'] .':<br /><input type="text" name="name" value="'.$page['name'].'" style="width:95%;"/><br /><br />';
            echo ''. $lng['Jins'] .':<br /><input type="radio" name="gender" value="0" '.($page['gender'] == 0 ? 'checked="checked"' : NULL).' /> '. $lng['Ayol'] .'<br /><input type="radio" name="gender" value="1" '.($page['gender'] == 1 ? 'checked="checked"' : NULL).' /> '. $lng['Erkak'] .'<br /><br />';
                    
            echo ''. $lng['Yosh'] .':<br /><select name="age" style="width:95%;">';
            $i = 10;
            while ($i <= 75) {
                echo ' <option value="'.$i.'" '.($i == $page['age'] ? 'selected="selected"' : NULL).' ">'.$i.'</option>';
                ++$i;
            }
            echo '</select><br /><br />';
                    
            echo ''. $lng['Lavozim'] .':<br /><select name="level" style="width:95%;">';
            echo '<option value="0" '.((0 || NULL) == $page['level'] ? 'selected="selected"' : NULL).' ">'. $lng['Foydalanuvchi'] .'</option>';
            
            $sql = DB::$dbs->query("SELECT * FROM ".POSITIONS."");
            while($pos = $sql -> fetch()) {
                echo '<option value="'.$pos['id'].'" '.($pos['id'] == $page['level'] ? 'selected="selected"' : NULL).' ">'.$pos['position'].'</option>';
            }   
            echo '</select><br />';
                    
            echo '<input type="submit" value="'. $lng['Saqlash'] .'" />';
            echo '</form>';
            echo CLOSE_DIV;
        } else {
            echo DIV_BLOCK . ''. $lng['Kirishda xatolik'] .'' . CLOSE_DIV;
        }
        break;
        
        case 'delete':
        if (privilegy('anceta_delete', $id)) {
            if (empty($_GET['go'])) {
                echo DIV_BLOCK . ''. $lng['Foydalanuvchini o`chirmoqchisizmi'] .' | <b>'.$page['name'].'</b>?' . CLOSE_DIV;
                echo DIV_LI . '<a href="'.HOME.'/panel/user/delete/'.$page['user_id'].'?go=yes">'. $lng['Ha'] .'</a> | 
				<a href="'.HOME.'/panel/user/edit/'.$page['user_id'].'">'. $lng['Yo`q'] .'</a>' . CLOSE_DIV;
            } else {
                DB::$dbs->query("DELETE FROM ".USERS." WHERE `user_id` = ?",array($page['user_id']));
                echo DIV_MSG . ''. $lng['Foydalanuvchi muvaffaqiyatli o`chirildi'] .'' . CLOSE_DIV;
            }
        } else {
            echo DIV_BLOCK . ''. $lng['Kirishda xatolik'] .'' . CLOSE_DIV;
        }
        break;
        
        case 'bann':
        if (privilegy('anceta_bann', $id)) {
            if ($_POST) {
                $arr = array(0 => 0, 1 => 300, 2 => 600, 3 => 900, 4 => 1800, 5 => 3600, 6 => 7200, 7 => 21600, 8 => 43200, 9 => 86400, 10 => 259200, 11 => 604800,
                             12 => 864000, 13 => 1209600, 14 => 2592000, 15 => 7776000, 16 => 15552000);
                
                $bann_time = num($_POST['bann_time']);
                $prich = html($_POST['prich']);
                $uip = num($_POST['uip']);
                
                $time = time() + $arr[$bann_time];
                
                if (empty($prich)) {
                    $err .= ''. $lng['Sababni ko`rsating'] .'';
                }
                
                if ($err) {
                    echo DIV_ERROR . $err . CLOSE_DIV;
                } else {
                    DB::$dbs->query("INSERT INTO ".BANN." (`user_id`, `moder`, `time_bann`, `prich`, `time`) VALUES (?, ?, ?, ?, ?)", array($page['user_id'], $user['user_id'], $time, $prich, time()));
                    
                    if ($uip == TRUE) {
                        DB::$dbs->query("INSERT INTO ".BLACKLIST." (`ua`, `ip`) VALUES (?, ?)", array($page['browser'], $page['ip']));
                    }
                    
                    echo DIV_MSG . ''. $lng['Foydalanuvchi muvaffaqiyatli ban oldi'] .'' . CLOSE_DIV;
                }
            }
                
            echo DIV_BLOCK;
            echo '<form action="#" method="POST">
            <b>'. $lng['Saytga kirishni bloklash'] .':</b><br /><select name="bann_time">
            <option value="0"">'. $lng['Hamma vaqtga'] .'</option>
            <option value="1">5 '. $lng['daqiqa'] .'</option>
            <option value="2">10 '. $lng['daqiqa'] .'</option>
            <option value="3">15 '. $lng['daqiqa'] .'</option>
            <option value="4">30 '. $lng['daqiqa'] .'</option>
            <option value="5">1 '. $lng['soat'] .'</option>
            <option value="6">2 '. $lng['soat'] .'</option>
            <option value="7">6 '. $lng['soat'] .'</option>
            <option value="8">12 '. $lng['soat'] .'</option>
            <option value="9">1 '. $lng['sutka'] .'</option>
            <option value="10">3 '. $lng['sutka'] .'</option>
            <option value="11">1 '. $lng['hafta'] .'</option>
            <option value="12">10 '. $lng['sutka'] .'</option>
            <option value="13">2 '. $lng['hafta'] .'</option>
            <option value="14">1 '. $lng['oy'] .'</option>
            <option value="15">2 '. $lng['oy'] .'</option>
            <option value="15">6 '. $lng['oy'] .'</option>
            <option value="16">1 '. $lng['yil'] .'</option>
            </select><br /><br />';
                
            echo ''. $lng['User Agent va IP ni qora ro`yhatga qo`shish'] .': <input type="checkbox" name="uip" value="1" /><br />';
                
            echo ''. $lng['Sababi'] .':<br /><input type="text" name="prich" /><br />';
                    
            echo '<input type="submit" value="'. $lng['Ban berish'] .'" />';
            echo '</form>';
            echo CLOSE_DIV;   
        } else {
            echo DIV_BLOCK . ''. $lng['Kirishda xatolik'] .'' . CLOSE_DIV;
        }    
        break;
    
    }
}
require_once('../core/stop.php');
?>