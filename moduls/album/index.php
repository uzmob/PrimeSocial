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
    head(''. $lng['Fotoalbom'] .'');

    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".ALBUMS." WHERE `user_id` = ?", array($user['user_id']));    
    echo DIV_LI . ''.icon('foto.png').' <a href="'.HOME.'/album/user/'.$user['user_id'].'/">'. $lng['Mening fotoalbomim'] .' <span class="count">'.$all.'</span></a>' . CLOSE_DIV;

    if ($_POST['add'] && privilegy('album')) {
        $name_a = html($_POST['name']);
            
        if (empty($name_a)) {
            echo DIV_ERROR . ''. $lng['Bo`lim nomini kiriting'] .'' . CLOSE_DIV;
        } else {
		if (!empty($_FILES['file'])) {
                $name = $_FILES['file']['name']; # Fayl nomi
                $ext = strtolower(strrchr($name, '.')); # Fayl shakli
                $par = getimagesize($_FILES['file']['tmp_name']); # Rasm shakli
                $icon_uri = time().$ext;
                $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar
                    
                if ($par[0] > 256 || $par[1] > 256) {
                    $err .= ''. $lng['Rasm hajmi belgilangan miqdordan oshmoqda'] .'. [Max. 16x16]<br />';
                }
                    
                if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $pictures)) {
                    $err .= ''. $lng['Fayl formatida xatolik'] .'.<br />';
                }
                    
                if (empty($err)) {
                    copy($_FILES['file']['tmp_name'], '../../files/icons_album/'.$icon_uri);
                } else {
                    echo $err;
                }
            }
            $icon_uri = ($icon_uri ? $icon_uri : '');
            DB::$dbs->query("INSERT INTO ".ALBUMS_CAT." (`name`,`icon`) VALUES (?,?)", array($name_a,$icon_uri));
            header("Location: ".HOME."/album/"); 
        }
    }
        
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".ALBUMS_CAT."");
        
    if ($all == 0) {
        echo DIV_AUT . ''. $lng['Bo`limlar yo`q'] .'' . CLOSE_DIV;
    } else {
        $sql = DB::$dbs->query("SELECT * FROM ".ALBUMS_CAT." ORDER BY `id` DESC ");
        while($cat = $sql -> fetch()) {
            $albums = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".ALBUMS." WHERE `cat_id` = ? ", array($cat['id']));
            $photos = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".ALBUMS_PHOTOS." WHERE `cat_id` = ? ", array($cat['id']));
            
                echo '<div class="touch"><a href="'.HOME.'/album/'.$cat['id'].'/">';
				
			    if ($cat['icon']) {
	 echo ' <img src="/files/icons_album/'. ($cat['icon']).'"> '.$cat['name'].' ';
	} else {
	 echo ' '.icon('folder.png').' '.$cat['name'].' ';
	}
			
            echo '<span class="count">'.$albums.' / '.$photos.'</span>
			</a></div>';
        }
    }

    
	
    if (privilegy('album')) {
        echo DIV_AUT;
        echo '<form action="#" method="POST" enctype="multipart/form-data">';
        echo ''. $lng['Albo`m nomi'] .':<br /><input type="text" name="name" /><br/>';
        echo ''. $lng['Icon'] .': <span class="mini">16x16, jpg|jpeg|gif|png</span><br/>
		<input type="file" name="file" /><br/>';
        echo '<input type="submit" name="add" value="'. $lng['Yangi bo`lim'] .'" /></form>';
        echo CLOSE_DIV; 
    } 
    echo '<div class="sts"><center><b>'. $lng['Ma`lumotlar'] .'</b></center>'. $lng['Album info'] .'!</div>';
	
   
    break;
    
    case 'cat':
    $cat = DB::$dbs->queryFetch("SELECT * FROM ".ALBUMS_CAT." WHERE `id` = ? ", array(abs(num($_GET['cat']))));
    
    if (empty($cat)) {
        head(''. $lng['Bo`lim topilmadi'] .''); 
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;  
              
    } else {
        
        head(''. $lng['Bo`lim'] .': ' . $cat['name']);


        if (isset($_GET['del']) && privilegy('album')) {
            if (!isset($_GET['go'])) {
                echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlang'] .':</b> <a href="?del&go">['. $lng['O`chirish'] .']</a> <a href="'.HOME.'/album/'.$cat['id'].'/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
            } else {
                DB::$dbs->query("DELETE FROM ".ALBUMS_CAT." WHERE `id` = ? ", array($cat['id']));
                header("Location: ".HOME."/album/"); 
            }    
        }
            
        if (isset($_GET['edit']) && privilegy('album')) {
            if ($_POST['edit']) {
                $name = html($_POST['name']);
                
                if (empty($name)) {
                    echo DIV_ERROR . ''. $lng['Bo`lim nomini kiriting'] .'' . CLOSE_DIV;
                } else {
                    DB::$dbs->query("UPDATE ".ALBUMS_CAT." SET `name` = ? WHERE `id` = ? ", array($name, $cat['id']));
                    header("Location: ".HOME."/album/".$cat['id']."/"); 
                }
            }
                
            echo DIV_AUT;
            echo '<form action="#" method="POST">';
            echo ''. $lng['Albo`m nomi'] .':<br /><input type="text" value="'.$cat['name'].'" name="name" />';
            echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /></form>';
            echo CLOSE_DIV;             
        }

        if ($_POST['add']) {
            $name = html($_POST['name']);
            $info = html($_POST['info']);
                
            if (empty($name)) {
                echo DIV_ERROR . ''. $lng['Fotoalbom nomini kiriting'] .'' . CLOSE_DIV;
            } else {
                DB::$dbs->query("INSERT INTO ".ALBUMS." (`cat_id`, `name`, `info`, `user_id`, `time`) VALUES (?, ?, ?, ?, ?)", array($cat['id'], $name, $info, $user['user_id'], time()));
                header("Location: ".HOME."/album/".$cat['id']."/"); 
            }
        }
              
        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".ALBUMS." WHERE `cat_id` = ?", array($cat['id']));  
        
        if (empty($all)) {
            echo DIV_BLOCK . ''. $lng['Fotoalbomlar ochilmagan'] .'' . CLOSE_DIV;
        } else {
            $n = new Navigator($all,$config['write']['album_albums'],'cat='.$cat['id'].'&select=cat'); 
            $sql = DB::$dbs->query("SELECT * FROM ".ALBUMS." WHERE `cat_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['album_albums']." ", array($cat['id']));
            while($album = $sql -> fetch()) {

                $photos = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".ALBUMS_PHOTOS." WHERE `album_id` = ? ", array($album['id']));
                
                echo DIV_LI . ''.icon('folder.png').' <a href="'.HOME.'/album/'.$cat['id'].'/'.$album['id'].'/">'.$album['name'].'</a> 
				<span class="count">'.$photos.'</span> '.userLink($album['user_id']).'' . CLOSE_DIV;
            }
            echo $n->navi();             
        }
    
    if (privilegy('album')) {
        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo ''. $lng['Yangi albom'] .':<br /><input type="text" name="name" /><br />';
        echo ''. $lng['Ta`rif'] .':<br /><textarea name="info"></textarea><br />';
        echo '<input type="submit" name="add" value="'. $lng['Yaratish'] .'" /></form>';
        echo CLOSE_DIV;           
        }
            
        if (privilegy('album') && !empty($cat)) {
            echo DIV_BLOCK;
            echo '- <a href="?edit">'. $lng['Tahrirlash'] .'<br />';
            echo '- <a href="?del">'. $lng['O`chirish'] .'<br />';
            echo CLOSE_DIV;  
        }
    }
          
    break;
    
    case 'rating';
    
        $photo_id = abs(intval($_POST['photo_id']));
        $data = DB::$dbs->queryFetch("SELECT * FROM ".ALBUMS_PHOTOS." WHERE `id` = ? && `user_id` != ?", array($photo_id, $user['user_id']));
        if (empty($data)) {
            header("Location: ".HOME);
            exit();
        }
        
        $ocenka = abs(intval($_POST['ocenka']));
        if (empty($ocenka) || $ocenka > 5) {
            header("Location: ".HOME."/album/".$cat['id']."/".$album['id']."/view/".$data['id']."/");
            exit();
        }
        
        $check_vote = DB::$dbs->querySingle("SELECT COUNT(*) FROM ".ALBUMS_RATING." WHERE `user_id` = ? && `photo_id` = ? ", array($user['user_id'], $data['id']));
        if ($check_vote) {
            header("Location: ".HOME."/album/".$cat['id']."/".$album['id']."/view/".$data['id']."/");
            exit();    
        }
        
        DB::$dbs->query("INSERT INTO ".ALBUMS_RATING." (`user_id`, `photo_id`, `time`, `ocenka`) VALUES (?,?,?,?)", array($user['user_id'], $data['id'], time(), $ocenka));
        header("Location: ".HOME."/album/".$data['cat_id']."/".$data['album_id']."/view/".$data['id']."/?addrating");
        break;
    
    case 'rating_list';
        $cat = DB::$dbs->queryFetch("SELECT * FROM ".ALBUMS_CAT." WHERE `id` = ? ", array(abs(num($_GET['cat']))));
        if (empty($cat)) {
            head(''. $lng['Bo`lim topilmadi'] .'');
            echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
            require_once('../../core/stop.php');
            exit();
        }    
        
        $album = DB::$dbs->queryFetch("SELECT * FROM ".ALBUMS." WHERE `id` = ? ", array(abs(num($_GET['album']))));
        if (empty($album)) {
            head(''. $lng['Fotoalbom topilmadi'] .'');
            echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
            require_once('../../core/stop.php');
            exit();
        }  
        
        $photo = DB::$dbs->queryFetch("SELECT * FROM ".ALBUMS_PHOTOS." WHERE `id` = ? ", array(abs(num($_GET['photo']))));
        if (empty($photo)) {
            head(''. $lng['Foto topilmadi'] .'');
            echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
            require_once('../../core/stop.php');
            exit();
        }
        
        head(''. $lng['Fotoga baxolar'] .'');   
        $total = DB::$dbs->querySingle("SELECT COUNT(*) FROM ".ALBUMS_RATING." WHERE `photo_id` = ? ", array($photo['id']));
        if ($total) {
            $sql = DB::$dbs->query("SELECT * FROM ".ALBUMS_RATING." WHERE `photo_id` = ? ORDER BY `id` DESC", array($photo['id']));
            while($post = $sql -> fetch()) {
                echo DIV_BLOCK;
                echo vrem($post['time']), ': ', user_choice($post['user_id'], 'link') . '  '. $lng['baxoladi'] .', ', $post['ocenka'], ' '. $lng['ballga'] .'';   
                echo CLOSE_DIV;
            }            
        } else {
            echo DIV_BLOCK . ''. $lng['Hali hech kim baxolamadi'] .'' . CLOSE_DIV;
        }
       break;
        
    case 'album':
    $cat = DB::$dbs->queryFetch("SELECT * FROM ".ALBUMS_CAT." WHERE `id` = ? ", array(abs(num($_GET['cat']))));
    if (empty($cat)) {
        head(''. $lng['Bo`lim topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit();
    }    
    
    $album = DB::$dbs->queryFetch("SELECT * FROM ".ALBUMS." WHERE `id` = ? ", array(abs(num($_GET['album']))));
    if (empty($album)) {
        head(''. $lng['Fotoalbom topilmadi'] .''); 
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
        require_once('../../core/stop.php');
        exit();
    }  
    
    head('' . $album['name']);
    
/* Shahsiylik */
if ($ank['user_id'] != $user['user_id']) {
    if ($ank['private_photos'] == 1) {
        $sql = DB::$dbs->queryFetch("SELECT `id`, `status`, `id_friend` FROM `friends` WHERE ((`id_user` = ? AND `id_friend` = ?) OR (`id_friend` = ? AND `id_user` = ?)) && status = ? LIMIT 1",array($user['user_id'], $ank['user_id'], $user['user_id'], $ank['user_id'], 1));
        if (!$sql) {
            echo '<div class="sts"><center><b>' . $ank['nick'] . '</b> '. $lng['Albom shahsiyliki'] .'</center></div>';
            require_once('../../core/stop.php');
            exit();
        }
    } else if ($ank['private_photos'] == 2) {
            echo '<div class="sts"><center><b>' . $ank['nick'] . '</b> '. $lng['Albom shahsiyliki2'] .'</center></div>';
        require_once('../../core/stop.php');
        exit();
    }
}

		 
	
        if (isset($_GET['del']) && privilegy('album')) {
            if (!isset($_GET['go'])) {
                echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlang'] .':</b> <a href="?del&go">['. $lng['O`chirish'] .']</a> <a href="'.HOME.'/album/'.$album['id'].'/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
            } else {
                
            $sql = DB::$dbs->query("SELECT * FROM ".ALBUMS_PHOTOS." WHERE `album_id` = ? ", array($album['id']));
            while($file = $sql -> fetch()) {
                unlink('../../files/album/' . $file['url']);
            }
            DB::$dbs->query("DELETE FROM ".ALBUMS_PHOTOS." WHERE `album_id` = ? ", array($album['id']));
            DB::$dbs->query("DELETE FROM ".ALBUMS." WHERE `id` = ? ", array($album['id']));
			
                header("Location: ".HOME."/album/"); 
            }    
        }

        if (isset($_GET['edit']) && privilegy('album')) {
            if ($_POST['edit']) {
                $name = html($_POST['name']);
                $info = html($_POST['info']);
                
                if (empty($name)) {
                    echo DIV_ERROR . ''. $lng['Bo`lim nomini kiriting'] .'' . CLOSE_DIV;
                } else {
                    DB::$dbs->query("UPDATE ".ALBUMS." SET `name` = ? WHERE `id` = ? ", array($name, $album['id']));
                    DB::$dbs->query("UPDATE ".ALBUMS." SET `info` = ? WHERE `id` = ? ", array($info, $album['id']));
                    header("Location: ".HOME."/album/".$cat['id']."/".$album['id']."/"); 
                }
            }
            
            echo DIV_AUT;
            echo '<form action="#" method="POST">';
            echo ''. $lng['Nomi'] .':<br/><input type="text" value="'.$album['name'].'" name="name" /><br/>';
            echo ''. $lng['Ta`rif'] .':<br /><input type="text" value="'.$album['info'].'" name="info" /><br />';
            echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /></form>';
            echo CLOSE_DIV;             
        }
		

	
		
    if (!empty($_POST['upload']) && $album['user_id'] == $user['user_id']) {
        if (isset($_FILES['photo']['name'])) {
            foreach ($_FILES['photo']['name'] as $k=>$v) {
                
				
	
		
                $name = $_FILES['photo']['name'][$k];
                $ext = strtolower(strrchr($name, '.')); # Fayl formati
                $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bolgan formatlar
                $size = $_FILES['photo']['size'][$k]; # Fayl hajmi
                
                if (preg_match('/.phtml/i', $name) || preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $pictures)) {
                    
                } else {
                    $name_photo = 'img_' . time() . ''. $ext;
                    copy($_FILES['photo']['tmp_name'][$k], '../../files/album/'.$name_photo);
                    img_resize('../../files/album/'.$name_photo, '../../files/album/mini_'.$name_photo, $config['mini_photo_par'][0], $config['mini_photo_par'][1]); # Mini
                    DB::$dbs->query("INSERT INTO ".ALBUMS_PHOTOS." (`cat_id`, `album_id`, `url`, `user_id`, `size`, `type`, `time`) VALUES (?,?,?,?,?,?,?)", array($cat['id'], $album['id'], $name_photo, $user['user_id'], $size, $ext, time()));  
                }
            }
        }
        header("Location: ".HOME."/album/".$cat['id']."/".$album['id']."/");        
    }
  

    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".ALBUMS_PHOTOS." WHERE `album_id` = ?", array($album['id']));
            
    if ($all == 0) {
        echo DIV_AUT . ''. $lng['Rasmlar joylanmagan'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['album_photos'],'cat='.$cat['id'].'&album='.$album['id'].'&select=album'); 
        $sql = DB::$dbs->query("SELECT * FROM ".ALBUMS_PHOTOS." WHERE `album_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['album_photos']." ", array($album['id']));
        
        while($photo = $sql -> fetch()) {
            echo DIV_BLOCK;
            echo '<a href="'.HOME.'/album/'.$cat['id'].'/'.$album['id'].'/view/'.$photo['id'].'/">
			<img src="'.HOME.'/files/album/mini_'.$photo['url'].'" /></a> ';
            echo CLOSE_DIV;
        }
        echo $n->navi(); 
    }  
            


	
    if ($album['user_id'] == $user['user_id']) {
            echo DIV_BLOCK;
            echo '- <a href="?edit">'. $lng['Tahrirlash'] .'</a><br />';
            echo '- <a href="?del">'. $lng['O`chirish'] .'</a><br />';
            echo CLOSE_DIV; 
            echo DIV_BLOCK;
        echo '<form action="'.HOME.'/album/'.$cat['id'].'/'.$album['id'].'/" enctype="multipart/form-data" method="POST">';
        echo '<b>'. $lng['Foto kiritish'] .':</b><br /><input name="photo[]" type="file" multiple="true" /><br />';
        echo '<input type="submit" name="upload" value="'. $lng['Joylash'] .'" />';  
        echo '</form>';
        echo CLOSE_DIV; 
    }
        echo '<div class="white">';
            echo '- <a href="'.HOME.'/album/user/'.$album['user_id'].'/">'. $lng['Orqaga'] .'</a>';
        echo CLOSE_DIV; 
    if (!empty($album['info'])) {
        echo '<div class="sts"><center> ' . text($album['info']) . '</center></div>';
    }

	
    break;
    
    case 'view';
        $cat = DB::$dbs->queryFetch("SELECT * FROM ".ALBUMS_CAT." WHERE `id` = ? ", array(abs(num($_GET['cat']))));
        if (empty($cat)) {
            head(''. $lng['Bo`lim topilmadi'] .'');
            echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
            require_once('../../core/stop.php');
            exit();
        }    
        
        $album = DB::$dbs->queryFetch("SELECT * FROM ".ALBUMS." WHERE `id` = ? ", array(abs(num($_GET['album']))));
        if (empty($album)) {
            head(''. $lng['Fotoalbom topilmadi'] .'');
            echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
            require_once('../../core/stop.php');
            exit();
        }  
        
        $photo = DB::$dbs->queryFetch("SELECT * FROM ".ALBUMS_PHOTOS." WHERE `id` = ? ", array(abs(num($_GET['photo']))));
        if (empty($photo)) {
            head(''. $lng['Foto topilmadi'] .'');
            echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
            require_once('../../core/stop.php');
            exit();
        }
        
        head(''. $lng['Suratni ko`rish'] .'');
        if (isset($_GET['addrating'])) {
            echo DIV_MSG . ''. $lng['Reyting muvaffaqiyatli o`zgartirildi'] .'!' . CLOSE_DIV;
        }
        if (isset($_GET['update_ava'])) {
            echo DIV_MSG . ''. $lng['Avatar muvaffaqiyatli yangilandi'] .'' . CLOSE_DIV;
        }
        
        
        if ($_POST) {
    $comm = html($_POST['comm']);
                
    if (empty($comm)) {
        $err = ''. $lng['Sharh bo`sh'] .'';
    }
                
    if (!empty($err)) {
        echo DIV_ERROR . $err . CLOSE_DIV;
    } else {
        DB::$dbs->query("INSERT INTO ".ALBUMS_COMM." (`photo_id`, `user_id`, `comm`, `time`) VALUES (?, ?, ?, ?)", array($photo['id'], $user['user_id'], $comm, time()));
        balls_operation(2);
        echo DIV_MSG . ''. $lng['Sharh kiritildi'] .'' . CLOSE_DIV;                    
    }


    if (!empty($_GET['otv']) && $_GET['otv'] != $user['user_id']) {
        $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM ".USERS." WHERE `user_id` = ? ",array(abs(num($_GET['otv']))));
        if (!empty($ank)) {
            $msg = '[b]' . $ank['nick'] . '[/b], ' . $msg;
        }
        
        $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['sizning habaringizga javob berdi'] .' | <a href="'.HOME.'/album/'.$cat['id'].'/'.$album['id'].'/view/'.$photo['id'].'/"><b>'. $lng['Fotoalbom'] .'</b></a>';
        lenta($lenta, $ank['user_id']);
    }  
	}       

    
	echo '<div class="grey" style="padding-bottom:15px;">'.icon('yuklash.png').' <a href="'.HOME.'/files/album/'.$photo['url'].'">'. $lng['Yuklab olish'] .'</a>
	<span style="float:right;" class="mini">'.get_size($photo['size']) . ' / ' . $photo['type'] . '</span><br />';
    
            if ($user['user_id'] == $photo['user_id']) {
                echo ''.icon('ava.png').' <a href="'.HOME.'/album/'.$cat['id'].'/'.$album['id'].'/update_ava/'.$photo['id'].'/">'. $lng['Avatarga o`rnatish'] .'</a>';
            }
        echo '<span style="float:right;" class="mini">' . vrem($photo['time']) . '</span></div>';
            echo '<div class="white"><center>
			<a href="'.HOME.'/files/album/'.$photo['url'].'">
			<img src="'.HOME.'/files/album/'.$photo['url'].'" style="width:250px;height:220px;">
			</a></center></div>';
			

            echo DIV_BLOCK;
if ($user) {
            $check_vote = DB::$dbs->querySingle("SELECT COUNT(*) FROM ".ALBUMS_RATING." WHERE `user_id` = ? && `photo_id` = ? ", array($user['user_id'], $photo['id']));
            if (!$check_vote) {            
                if ($user['user_id'] != $photo['user_id']) {
                    echo '<form action="'.HOME.'/album/'.$cat['id'].'/'.$album['id'].'/rating/" method="POST">';
                    echo '<b>'. $lng['Baxolash'] .':</b><br />';
                    echo '<select name="ocenka">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                    </select>
                    <input type="hidden" name="photo_id" value="'.$photo['id'].'" />
                    <input type="submit" name="rating" value="'. $lng['Baxolash'] .'" /><br />
                    </form>';
                }
            }
}
            //SELECT SUM(column_name) FROM table_name;
            $rating = DB::$dbs->querySingle("SELECT SUM(ocenka) FROM ".ALBUMS_RATING." WHERE `photo_id` = ? ", array($photo['id']));
            
            $total_votes = DB::$dbs->querySingle("SELECT COUNT(*) FROM ".ALBUMS_RATING." WHERE `photo_id` = ? ", array($photo['id']));
            echo '<center>'. $lng['Foto reytingi'] .': <b>', $rating, '</b>  <a href="'.HOME.'/album/'.$cat['id'].'/'.$album['id'].'/rating_list/'.$photo['id'].'/" style="color:green;">'.$total_votes.'</a></center>';
            /* *** */
            

            echo CLOSE_DIV;
		
    $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".ALBUMS_COMM." WHERE `photo_id` = ?", array($photo['id']));
    echo '<div class="grey"><b>'. $lng['Sharhlar'] .'</b> '.$comm.'</div>';     

if ($user) {
 echo DIV_AUT;
if (!empty($_GET['otv'])) {
    $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM ".USERS." WHERE `user_id` = ? ",array(abs(num($_GET['otv']))));
    if (!empty($ank) && $ank['user_id'] != $user['id']) {
        echo ' <b>' . $ank['nick'] . '</b> '. $lng['ga javob'] .'
		<a href="'.HOME.'/album/'.$cat['id'].'/'.$album['id'].'/view/'.$photo['id'].'/">[x]</a><br />';
    } else {
        echo '<b>'. $lng['Habar'] .':</b><br />';
    }
}        

echo '<form action="'.(isset($_GET['otv']) ? '?otv='.(int)$_GET['otv'] : NULL).'" enctype="multipart/form-data" method="POST">';
echo '<textarea name="comm" style="width:97%;height:4pc;"></textarea><br />';
echo '<input type="submit" value="'. $lng['Sharh kiritish'] .'" /> ';
bbsmile();  
echo '</form></div>';	
}			       
   
        if (!empty($_GET['del'])) {
            $comm = DB::$dbs->queryFetch("SELECT * FROM ".ALBUMS_COMM." WHERE `id` = ? ORDER BY `id` DESC",array(num($_GET['del'])));
            if ($user['user_id'] == $comm['user_id'] || $ank['user_id'] == $user['user_id']) {
                DB::$dbs->query("DELETE FROM ".ALBUMS_COMM." WHERE `id` = ? ", array(num($_GET['del'])));
            }
        }
                        
if (empty($comm)) {
    echo DIV_BLOCK . ''. $lng['Sharhlar yo`q'] .'' . CLOSE_DIV;
} else {
    $n = new Navigator($comm,$config['write']['albums_comm'],'select=view&user='.$photo['id']); 
    $sql = DB::$dbs->query("SELECT * FROM ".ALBUMS_COMM." WHERE `photo_id` = ? ORDER BY `id` DESC LIMIT 5", array($photo['id']));
    
    while($comm = $sql -> fetch()) {
	
	echo '<div class="white">';
echo '<table cellspacing="0" cellpadding="0" style="margin-bottom:5px;" width="100%" ><tr>';
echo '<td class="grey" style="width:5%;border-radius: 6px 0 0 6px;"><center>';
echo '' . avatar($comm['user_id'],40,40) . '';
echo '</center></td>';

echo '<td class="grey" style="width:95%;border-radius:  0 6px 6px 0;">';
echo ''. userLink($comm['user_id']) . ' 
  <span  style="float:right;"> '. ($comm['user_id'] != $user['user_id'] ? '
		<a href="?otv='.$comm['user_id'].'"> '.icon('sharh.png').'</a> ' : NULL) . '  ' . (privilegy('album') ? ' 
		<a href="'.HOME.'/album/'.$cat['id'].'/'.$album['id'].'/view/'.$photo['id'].'/?del='.$comm['id'].'">'.icon('minus2.png').'</a> ' : NULL) . '';
        echo '</span><br/><span class="mini">' . vrem($comm['time']) . '</span></td></tr></table>';
        echo '' . text($comm['comm']) . '</div>';
    }
    echo $n->navi();                   
}

	echo '<div class="white">';
			if ($photo['user_id'] == $user['user_id']) {
                echo '- <a href="'.HOME.'/album/'.$cat['id'].'/'.$album['id'].'/?del='.$photo['id'].'">'. $lng['O`chirish'] .'</a>';
            }
            echo '<br/>- <a href="'.HOME.'/album/'.$cat['id'].'/'.$album['id'].'/">'. $lng['Orqaga'] .'</a>' . CLOSE_DIV;
           break;
    
    case 'update_ava':
        $cat = DB::$dbs->queryFetch("SELECT * FROM ".ALBUMS_CAT." WHERE `id` = ? ", array(abs(num($_GET['cat']))));
        if (empty($cat)) {
            head(''. $lng['Bo`lim topilmadi'] .'');
            echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
            require_once('../../core/stop.php');
            exit();
        }    
        
        $album = DB::$dbs->queryFetch("SELECT * FROM ".ALBUMS." WHERE `id` = ? ", array(abs(num($_GET['album']))));
        if (empty($album)) {
            head(''. $lng['Fotoalbom topilmadi'] .'');
            echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
            require_once('../../core/stop.php');
            exit();
        }  
        
        $photo = DB::$dbs->queryFetch("SELECT * FROM ".ALBUMS_PHOTOS." WHERE `id` = ? && user_id = ? ", array(abs(num($_GET['photo'])), $user['user_id']));
        if (empty($photo)) {
            head(''. $lng['Foto topilmadi'] .'');
            echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;
            require_once('../../core/stop.php');
            exit();
        }
        
        if (copy('../../files/album/' . $photo['url'], '../../files/photo/' . $user['user_id'] . '.jpg')) {
            img_resize('../../files/photo/'.$user['user_id'].'.jpg', '../../files/photo/mini_'.$user['user_id'].'.jpg', $config['mini_photo_par'][0], $config['mini_photo_par'][1]); # MiniDB::$dbs->query("UPDATE ".USERS." SET `photo` = ? WHERE `user_id` = ? ", array($user['user_id'].'.jpg', $user['user_id']));
            DB::$dbs->query("UPDATE ".USERS." SET `photo` = ? WHERE `user_id` = ? ", array($user['user_id'].'.jpg', $user['user_id']));

            header("Location: ".HOME."/album/".$cat['id']."/".$album['id']."/view/".$photo['id']."/?update_ava");
        } else {
            header("Location: ".HOME."/album/".$cat['id']."/".$album['id']."/view/".$photo['id']."/");
        }
        break;
        
    case 'user':
    $ank = DB::$dbs->queryFetch("SELECT * FROM ".USERS." WHERE `user_id` = ?",array(num($_GET['user'])));

    if (empty($ank)) {
        head(''. $lng['Foydalanuvchi topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;  
              
    } else {
        
        head(''. $lng['Fotoalbomlar'] .': ' . $ank['nick']);
        		
        	/* Shahsiylik */
if ($ank['user_id'] != $user['user_id']) {
    if ($ank['private_photos'] == 1) {
        $sql = DB::$dbs->queryFetch("SELECT `id`, `status`, `id_friend` FROM `friends` WHERE ((`id_user` = ? AND `id_friend` = ?) OR (`id_friend` = ? AND `id_user` = ?)) && status = ? LIMIT 1",array($user['user_id'], $ank['user_id'], $user['user_id'], $ank['user_id'], 1));
        if (!$sql) {
            echo '<div class="sts"><center><b>' . $ank['nick'] . '</b> '. $lng['Albom shahsiyliki'] .'</center></div>';
            require_once('../../core/stop.php');
            exit();
        }
    } else if ($ank['private_photos'] == 2) {
            echo '<div class="sts"><center><b>' . $ank['nick'] . '</b> '. $lng['Albom shahsiyliki2'] .'</center></div>';
        require_once('../../core/stop.php');
        exit();
    }
}		
        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".ALBUMS." WHERE `user_id` = ?", array($ank['user_id']));  
        
        if (empty($all)) {
            echo '<div class="white">'. $lng['Fotoalbomlar hali ochilmagan'] .' <br/>';
			
if ($ank['user_id'] == $user['user_id']) {
			echo '<a href="/album/"><u>'. $lng['Yaratish'] .'</u></a>';
}
        echo '</div>';
        } else {
            $n = new Navigator($all,$config['write']['album_albums'],'select=user&user='.$ank['user_id']); 
            $sql = DB::$dbs->query("SELECT * FROM ".ALBUMS." WHERE `user_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['album_albums']." ", array($ank['user_id']));
            while($album = $sql -> fetch()) {

                $photos = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".ALBUMS_PHOTOS." WHERE `album_id` = ? ", array($album['id']));
                
                echo DIV_LI . ''.icon('folder.png').'  
				<a href="'.HOME.'/album/'.$album['cat_id'].'/'.$album['id'].'/">'.$album['name'].'</a> <span class="count">'.$photos.'</span>';
				    if (!empty($album['info'])) {
        echo '<br/><span style="font-size:13px;color:#757575;">' . text($album['info']) . '</span>';
    }
	echo '</div>';
	
	
	
            }
            echo $n->navi();             
        }
		
if ($ank['user_id'] == $user['user_id']) {
		echo '<div class="white">+ <a href="/album/"><u>'. $lng['Yangi bo`lim'] .'</u></a></div>';
    }
    }
    break;

}

require_once('../../core/stop.php');
?>