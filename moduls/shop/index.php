<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

check_auth();

switch ($_GET['act']) {
    
    default:
    head(''. $lng['Do`kon'] .'');
    echo DIV_LI . ''.icon('jonvor.png').' <a href="'.HOME.'/shop/zver/">'. $lng['Anketaga jonvor'] .'</a>' . CLOSE_DIV;
    echo DIV_LI . ''.icon('rang.png').' <a href="'.HOME.'/shop/nick/">'. $lng['Nik rangi'] .'</a>' . CLOSE_DIV;
    echo DIV_LI . ''.icon('text.png').' <a href="'.HOME.'/shop/nickedit/">'. $lng['Nikni almashtirish'] .'</a>' . CLOSE_DIV;
    echo DIV_LI . ''.icon('star.png').' <a href="'.HOME.'/shop/vip/">'. $lng['VIP Sotib olish'] .'</a>' . CLOSE_DIV;
    echo DIV_LI . ''.icon('addus.png').' <a href="'.HOME.'/shop/icon/">'. $lng['Nik yoniga rasmcha'] .'</a>' . CLOSE_DIV;
	break;

    case 'icon':
    head(''. $lng['Nikga rasmcha'] .'');
    $price = 100; // Hizmat narxi
    if (!empty($_POST['send'])) {
        $icon = html($_POST['icon']);       
        
        if ($icon == 'none') {
            // Profildan ikonkani o`chiramiz, ballarni olib qolmaymiz
            if ($user['icon']) {
                $pos = strpos($user['icon'], 'site');
                if ($pos === false) {
                    @unlink('../files/icons_user/' . $user['icon']);
                }
                DB::$dbs->query("UPDATE ".USERS." SET `icon` = ? WHERE `user_id` = ?", array('', $user['user_id']));
                header("Location: ".HOME."/shop/icon/?delete");
            }      
        } elseif ($icon == 'upload' && $user['balls'] >= $price) {
            // Ikonkani kiritamiz va ballarni olib qolamiz
            if (!empty($_FILES['file'])) {
                if ($user['icon']) {
                    $pos = strpos($user['icon'], 'site');
                    if ($pos === false) {
                        @unlink('../files/icons_user/' . $user['icon']);
                    }
                }  
                $name = $_FILES['file']['name']; # Fayl nomi
                $ext = strtolower(strrchr($name, '.')); # Fayl formati
                $par = getimagesize($_FILES['file']['tmp_name']); # Rasm shakli
                $icon_uri = $user['user_id'].$ext;
                $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar
                
                if ($par[0] > 16 || $par[1] > 16) {
                    $err .= ''. $lng['Rasm hajmi belgilangan miqdordan oshyapti'] .'. [Max. 16x16]<br />';
                }
                
                if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $pictures)) {
                    $err .= ''. $lng['Fayl shaklida xatolik'] .'.<br />';
                }
                
                if (empty($err)) {
                    copy($_FILES['file']['tmp_name'], '../../files/icons_user/'.$user['user_id'].$ext); # Original tarzda yuklaymiz
                    DB::$dbs->query("UPDATE ".USERS." SET `icon` = ?, `balls` = ? WHERE `user_id` = ?", array($icon_uri, ($user['balls'] - $price), $user['user_id']));
                    header("Location: ".HOME."/shop/icon/?update");
                } else {
                    echo $err;
                }
            }
        } else if ($user['balls'] >= $price) {
            // Saytdagi ikonkani profilga biriktirib, ballni yechib olib qolamiz
            if (is_file('../../files/icons_user/site/' . $icon)) {
                $icon_uri = 'site/' . $icon;
                DB::$dbs->query("UPDATE ".USERS." SET `icon` = ?, `balls` = ? WHERE `user_id` = ?", array($icon_uri, ($user['balls'] - $price), $user['user_id']));
                header("Location: ".HOME."/shop/icon/?update");
            }
        }
    }
     
    if (isset($_GET['update'])) {
        echo DIV_MSG, ''. $lng['Rasmcha muvaffaqiyatli yangilandi'] .'', CLOSE_DIV;
    }
    if (isset($_GET['delete'])) {
        echo DIV_MSG, ''. $lng['Rasmcha muvaffaqiyatli o`chirildi'] .'', CLOSE_DIV;
    }  
    echo DIV_BLOCK, ''. $lng['O`rnatish baxosi'] .': <b>'.$price.'</b> '. $lng['ball'] .'.<br /><form action="#" method="POST" enctype="multipart/form-data">';
    echo '<input type="radio" name="icon" value="none" checked> '. $lng['Rasmchasiz'] .'<br />';
    echo '<input type="radio" name="icon" value="upload" '.($user['icon'] && strpos($user['icon'], 'site') === false ? 'checked' : null).'> '. $lng['Rasmcha yuklash'] .'<br />';
    echo '<b>'. $lng['Yoki ro`yhatdan tanlang'] .':</b><br />';
    $scan = scandir('../../files/icons_user/site');
    unset($scan[0], $scan[1]);
    
    foreach ($scan as $icon) {
        echo '<input type="radio" name="icon" value="'.$icon.'"> <img src="'.HOME.'/files/icons_user/site/'.$icon.'" alt="[icon]" /> &nbsp; ';
    }
    
    echo '<br/><br />'. $lng['Yuklanadigan rasmcha'] .' [16x16, jpg|jpeg|gif|png]:<br /><input type="file" name="file" /><br />
	<input type="submit" name="send" value="'. $lng['Yangilash/Yuklash'] .'" />';
    echo '</form>', CLOSE_DIV;
	break;
        
    case 'vip':
    head(''. $lng['VIP Sotib olish'] .'');
    $price = 250;
    if (!empty($_POST['send'])) {
        
        $err = array();
        
        if ($user['vip'] > time()) {
            $err[] = ''. $lng['Ushbu hizmat sizda yoqilgan'] .'';
        }
        
        if ($user['balls'] < $price) {
            $err[] = ''. $lng['Sizda ballar yetarli emas'] .'';
        }
        
        if (!empty($err)) {
            echo DIV_ERROR;
            foreach ($err AS $value) {
                echo $value . '<br />';
            }
            echo CLOSE_DIV;
        } else {
            DB::$dbs->query("UPDATE ".USERS." SET `vip` = ?, `balls` = ? WHERE `user_id` = ?", array((time() + 604800), ($user['balls'] - $price), $user['user_id']));
            echo DIV_MSG . ''. $lng['Hizmat muvaffaqiyatli faollashtirildi'] .'' . CLOSE_DIV;
        }        
        
    }
        
    echo '<div class="white"><form action="#" method="POST">
    <img src="/style/img/user/vip.gif"> <b>'. $lng['VIP Sotib olish'] .' ['. $lng['Ustinligi'] .']:</b><br />
     - '. $lng['Sizning nik va fotongiz saytning bosh sahifasida chiqadi'] .'<br />
     - '. $lng['Nikingiz oldida VIP rasmcha paydo bo`ladi. Bu bilan siz boshqa foydalanuvchilardan ajralib turasiz'] .' :)</div><div class="white">
   
    '. $lng['Baxosi'] .': <b>'.$price.' '. $lng['ball'] .'</b><br />
    '. $lng['Hizmat davomiyligi'] .': <b>7 '. $lng['kun'] .'.</b><br />
    <input type="submit" name="send" value="'. $lng['Faollashtirish'] .'" /><br />
    </form>
    ' . CLOSE_DIV;
    break;
    
    case 'nickedit':
    $price = 1000;

    head(''. $lng['Nikni o`zgartirish'] .'');
    
    if (!empty($_POST['send'])) {
        $nick = html($_POST['nick']);
        
        $err = array();
        if(!preg_match("#^([A-zА-я0-9\-\_\ ])+$#ui", $nick)) {
            $err[] = ''. $lng['Nikni to`g`ri shaklda ko`rsating'] .'';
        } 
        
        if (is_numeric($nick)) {
            $err[] = ''. $lng['Nik faqat sonlardan tashkil qila olmaydi'] .'';
        }
        
        if (strlen($nick) > 25 || strlen($nick) < 3) {
            $err[] = ''. $lng['Nik uzun yoki juda qisqa'] .'';
        }
        
        if (DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `nick` = ?", array($nick)) == TRUE) {
            $err[] = ''. $lng['Ushbu nik band'] .'';
        }
        
        if ($user['balls'] < $price) {
            $err[] = ''. $lng['Sizda ballar yetarlicha emas'] .'';
        }
        
        if (!empty($err)) {
            echo DIV_ERROR;
            foreach ($err AS $value) {
                echo $value . '<br />';
            }
            echo CLOSE_DIV;
        } else {
            DB::$dbs->query("UPDATE ".USERS." SET `nick` = ?, `balls` = ? WHERE `user_id` = ?", array($nick, ($user['balls'] - $price), $user['user_id']));
            echo DIV_MSG . ''. $lng['Nik muvaffaqiyatli o`zgartirildi'] .'' . CLOSE_DIV;
        }
    }
     
    echo DIV_BLOCK . '<form action="#" method="POST">
    <b>'. $lng['Nikni o`zgartirish'] .':</b> [1000 '. $lng['ball'] .']<br />
    '. $lng['Ruhsat berilgan belgilar'] .': а-Я, a-Z, 0-9<br />
    '. $lng['Uzunligi'] .': 3-25<br />
    '. $lng['Faqat sonlar ishtrok etadigan nik taqiqlangan'] .'<br /><br />
    <input type="text" name="nick" value="'.$user['nick'].'" /><br />
    <input type="submit" name="send" value="'. $lng['O`zgartirish'] .'" /><br />
    </form>
    ' . CLOSE_DIV;
    break;
    
    case 'zver':
    $zver = array(
        array('name' => ''. $lng['zver'] .'', 'url' => '1.png', 'price' => 200),
        array('name' => ''. $lng['zver2'] .'', 'url' => '2.png', 'price' => 180),
        array('name' => ''. $lng['zver3'] .'', 'url' => '3.png', 'price' => 150),
        array('name' => ''. $lng['zver4'] .'', 'url' => '4.png', 'price' => 160),
        array('name' => ''. $lng['zver5'] .'', 'url' => '5.png', 'price' => 100),
        array('name' => ''. $lng['zver6'] .'', 'url' => '6.png', 'price' => 100),
        array('name' => ''. $lng['zver7'] .'', 'url' => '7.png', 'price' => 80),
        array('name' => ''. $lng['zver8'] .'', 'url' => '8.png', 'price' => 80),
        array('name' => ''. $lng['zver9'] .'', 'url' => '9.png', 'price' => 80),
        array('name' => ''. $lng['zver10'] .'', 'url' => '10.png', 'price' => 80),
        array('name' => ''. $lng['zver11'] .'', 'url' => '11.png', 'price' => 80),
        array('name' => ''. $lng['zver12'] .'', 'url' => '12.png', 'price' => 80),
        array('name' => ''. $lng['zver13'] .'', 'url' => '13.png', 'price' => 80),
        array('name' => ''. $lng['zver14'] .'', 'url' => '14.png', 'price' => 80),
        array('name' => ''. $lng['zver15'] .'', 'url' => '15.png', 'price' => 80),
        array('name' => ''. $lng['zver16'] .'', 'url' => '16.png', 'price' => 80),
        array('name' => ''. $lng['zver17'] .'', 'url' => '17.png', 'price' => 80),
        array('name' => ''. $lng['zver18'] .'', 'url' => '18.png', 'price' => 80),
        array('name' => ''. $lng['zver19'] .'', 'url' => '19.png', 'price' => 80),
        array('name' => ''. $lng['zver20'] .'', 'url' => '20.png', 'price' => 80),
        array('name' => ''. $lng['zver21'] .'', 'url' => '21.png', 'price' => 80)
    );
    head(''. $lng['Uy hayvoni sotib olish'] .'');
     
    if (isset($_GET['go'])) {
        $zverID = abs(num($_GET['go']));
        
        $err = array();
        if (empty($zver[$zverID])) {
            $err[] = ''. $lng['Uy hayvoni topilmadi'] .'';
        }
        
        if ($zver[$zverID]['price'] > $user['balls']) {
            $err[] = ''. $lng['Ballaringiz yetarlicha emas'] .'';
        }
        
        if (empty($err)) {
            DB::$dbs->query("UPDATE ".USERS." SET `zver` = ?, `balls` = ? WHERE `user_id` = ?", array($zver[$zverID]['url'], ($user['balls'] - $zver[$zver[$zverID]['price']]), $user['user_id']));
            echo DIV_MSG . ''. $lng['Uy hayvoni anketangizga muvaffaqiyatli o`rnatildi'] .'' . CLOSE_DIV;            
        } else {
            echo DIV_ERROR;
            foreach ($err AS $value) {
                echo $value . '<br />';
            }
            echo CLOSE_DIV;            
        }
    }  
        foreach ($zver AS $key => $value) {
            echo DIV_LI . $value['name'] . '<br /><a href="?go='.$key.'"><img src="'.HOME.'/moduls/shop/zver/'.$value['url'].'" /></a> <br />
            [' . $value['price'] . ' '. $lng['ball'] .']' . CLOSE_DIV;
        }
    break;
    
    case 'nick':
    head(''. $lng['Nik rangi'] .'');
     
    if (isset($_GET['color']) || isset($_GET['gradient'])) {
        
        if (isset($_GET['color'])) {
            $id = abs(num($_GET['id']));
            if (empty($color[$id])) {
                echo DIV_ERROR . ''. $lng['Rang topilmadi'] .'' . CLOSE_DIV;
            } else {
                if ($user[balls] < 50) {
                    echo DIV_ERROR . ''. $lng['Sizda ballar yetarlicha emas'] .'' . CLOSE_DIV;
                } else {
                    $value = 'color:' . $id;
                    DB::$dbs->query("UPDATE ".USERS." SET `color_nick` = ?, `balls` = ? WHERE `user_id` = ?", array($value, ($user['balls'] - 50), $user['user_id']));
                    echo DIV_MSG . ''. $lng['Nik rangi muvaffaqiyatli o`rnatildi'] .'' . CLOSE_DIV;
                }
            }
        } else {
            $id = abs(num($_GET['id']));
            if (empty($gradient_1[$id])) {
                echo DIV_ERROR . ''. $lng['Rang topilmadi'] .'' . CLOSE_DIV;
            } else {
                if ($user[balls] < 200) {
                    echo DIV_ERROR . ''. $lng['Sizda ballar yetarlicha emas'] .'' . CLOSE_DIV;
                } else {
                    $value = 'gradient:' . $id;
                    DB::$dbs->query("UPDATE ".USERS." SET `color_nick` = ?, `balls` = ? WHERE `user_id` = ?", array($value, ($user['balls'] - 200), $user['user_id']));
                    echo DIV_MSG . ''. $lng['Nik rangi muvaffaqiyatli o`rnatildi'] .'' . CLOSE_DIV;
                }
            }            
        }
        
    }
        foreach ($color AS $key => $value):
            echo DIV_LI . '<a href="'.HOME.'/shop/nick/?id='.$key.'&color"><font color="'.$value.'">'.$user['nick'].'</font></a> [50 '. $lng['ball'] .']' . CLOSE_DIV;
        endforeach;
        
        foreach ($gradient_1 AS $key => $value):
            echo DIV_LI . '<a href="'.HOME.'/shop/nick/?id='.$key.'&gradient">'.GradientLetter($user['nick'], $value, $gradient_2[$key]).'</a> [200 '. $lng['ball'] .']' . CLOSE_DIV;
        endforeach;
        
    break;
}


    
require_once('../../core/stop.php');
?>