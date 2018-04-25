<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');
require_once('func.php');

switch ($select) {
    
    default:
    head(''. $lng['Fayl almashinuv'] .'');
    
         

    if ($_POST['add'] && privilegy('zc')) {
        $name = html($_POST['name']);
        $type = abs(num($_POST['type']));
        
        if (empty($name)) {
            $err .= ''. $lng['Bo`lim nomini kiriting'] .'<br />';
        }
        
        if (empty($type)) {
            $err .= ''. $lng['Bo`lim turini tanlang'] .'<br />';
        }
        
        if (!empty($_FILES['icon']['name'])) {
            $name1 = $_FILES['icon']['name']; # Fayl nomi
            $ext = strtolower(strrchr($name1, '.')); # Fayl formati
            $par = getimagesize($_FILES['icon']['tmp_name']); # Rasm shakli
            $size = $_FILES['icon']['size']; # Fayl hajmi
            $file = time().$ext;
            $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar
            
            if (preg_match('/.phtml/i', $name) || preg_match('/.php/i', $name1) || preg_match('/.pl/i', $name1) || $name1 == '.htaccess' || !in_array($ext, $pictures)) {
                $err .= ''. $lng['Fayl shaklida xatolik'] .'.<br />';
            }            
        }
        
        if (!empty($err)) {
            echo DIV_ERROR . $err . CLOSE_DIV;
        } else {
            
            if (!empty($_FILES['icon']['name'])) {
                copy($_FILES['icon']['tmp_name'], '../../files/loads/icons/'.$file);
            }
            
            if (empty($_FILES['icon']['name'])) {
                $icon = '';
            } else {
                $icon = $file;
            }
            
            DB::$dbs->query("INSERT INTO ".LOADS." (`name`, `type`, `icon`) VALUES (?, ?, ?)", array($name, $type, $icon));
            header("Location: ".HOME."/loads/"); 
        }
    }
    $priceSumm = 50; // Boshlang`ich baxo
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS."");
        
    if ($all == 0) {
        echo DIV_AUT . ''. $lng['Bo`limlar ochilmagan'] .'' . CLOSE_DIV;
    } else {
        
        echo DIV_BLOCK . ''.icon('chart.png').' '. $lng['Eng zo`r fayllar'] .':<br/> 
		<a href="'.HOME.'/loads/new/day/">['. $lng['bugun'] .']</a> <a href="'.HOME.'/loads/new/wk/">['. $lng['hafta'] .']</a> 
		<a href="'.HOME.'/loads/new/month/">['. $lng['oy'] .']</a><br />'
         . ''.icon('efir.png').' <a href="'.HOME.'/loads/new/">'. $lng['So`ngi kiritilganlar'] .'</a><br />
        '.icon('search.png').' <a href="'.HOME.'/loads/search/">'. $lng['Fayllarni izlash'] .'</a>
         ' . CLOSE_DIV;
		 
  
	   
        $sql = DB::$dbs->query("SELECT * FROM ".LOADS." ORDER BY `id` DESC ");
        while($folder = $sql -> fetch()) {
            
            $cats = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_CAT." WHERE `folder_id` = ? ", array($folder['id']));
            $files = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_FILE." WHERE `folder_id` = ? ", array($folder['id']));
            
            echo '<div class="touch"> <a href="'.HOME.'/loads/'.$folder['id'].'/"><img src="' . (empty($folder['icon']) ? HOME . '/files/loads/icons/folder.gif' : HOME . '/files/loads/icons/' . $folder['icon']) . '" /> '.$folder['name'].' <span class="count">'.$cats.'/'.$files.'</span></a> </div>';
        }
    }
    
    if (privilegy('zc')) {
        echo DIV_AUT;
        echo '<form action="#" enctype="multipart/form-data" method="POST">';
        echo '<b>'. $lng['Yangi bo`lim'] .':</b><br /><input type="text" name="name" style="width:96%;"/><br />';
        echo ''. $lng['Turi'] .':<br /><select name="type" style="width:96%;">';
        echo '<option value="1">'. $lng['Rasm'] .'</option>';
        echo '<option value="2">'. $lng['Video'] .'</option>';
        echo '<option value="3">'. $lng['Musiqa'] .'</option>';
        echo '<option value="4">'. $lng['Java dasturlar'] .'</option>';
        echo '<option value="5">'. $lng['Android'] .'</option>';
        echo '<option value="6">'. $lng['Windows Mobile'] .'</option>';
        echo '<option value="7">'. $lng['iPhone'] .'</option>';
        echo '<option value="8">'. $lng['Bada'] .'</option>';
        echo '<option value="9">'. $lng['Flash'] .'</option>';
        echo '<option value="10">'. $lng['Mavzular'] .'</option>';
        echo '<option value="11">'. $lng['Symbian'] .'</option>';
        echo '</select> ';
        
        echo ''. $lng['ico'] .':<br /><input name="icon" type="file" style="width:96%;"/><br />';
        
        echo '<input type="submit" name="add" value="'. $lng['Ochish'] .'" /></form>';
        echo CLOSE_DIV; 
    }
    
    break;
    
    case 'folder':
    $folder = DB::$dbs->queryFetch("SELECT * FROM ".LOADS." WHERE `id` = ? ", array(abs(num($_GET['folder']))));
    
    if (empty($folder)) {
        head(''. $lng['Bo`lim topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
              
    }
    
    head(' ' . $folder['name']);

    if (isset($_GET['del']) && privilegy('zc')) {
        if (!isset($_GET['go'])) {
            echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlang'] .':</b> 
			<a href="?del&go">['. $lng['O`chirish'] .']</a> <a href="'.HOME.'/loads/'.$folder['id'].'/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
        } else {
            $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE." WHERE `folder_id` = ? ", array($folder['id']));
            while($file = $sql -> fetch()) {
                unlink('../../files/loads/files/' . $file['url']);
                @unlink('../../files/loads/files/mini_' . $file['url']);
            }
            @unlink('../../files/loads/icons/'.$folder['icon']);
            DB::$dbs->query("DELETE FROM ".LOADS_FILE." WHERE `folder_id` = ? ", array($folder['id']));
            DB::$dbs->query("DELETE FROM ".LOADS_CAT." WHERE `folder_id` = ? ", array($folder['id']));
            DB::$dbs->query("DELETE FROM ".LOADS." WHERE `id` = ? ", array($folder['id']));
            header("Location: ".HOME."/loads/"); 
        }    
    }

               
    if (isset($_GET['edit']) && privilegy('zc')) {
        
        /* Ikonkani o`chirish */
        if (isset($_GET['delicon'])) {
            unlink('../../files/loads/icons/'.$folder['icon']);
            DB::$dbs->query("UPDATE ".LOADS." SET `icon` = ? WHERE `id` = ? ", array('', $folder['id']));
            header("Locaion: " . HOME . '/loads/'.$folder['id'].'/?edit');
        }
        
        if ($_POST['edit']) {
            $name = html($_POST['name']);
            $type = abs(num($_POST['type']));
                
            if (empty($name) || empty($type)) {
                echo DIV_ERROR . ''. $lng['Bo`lim nomini kiriting'] .'' . CLOSE_DIV;
            } else {
                DB::$dbs->query("UPDATE ".LOADS." SET `name` = ?, `type` = ? WHERE `id` = ? ", array($name, $type, $folder['id']));
                header("Location: ".HOME."/loads/".$folder['id']."/"); 
            }
        }
            
        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo ''. $lng['Bo`limni tahrirlash'] .':<br /><input type="text" value="'.$folder['name'].'" name="name" style="width:96%;"/>';
        
        echo ''. $lng['Turi'] .':<br /><select name="type" style="width:96%;">';
        echo '<option '.(1 == $folder['type'] ? 'selected="selected"' : NULL).' value="1">'. $lng['Rasm'] .'</option>';
        echo '<option '.(2 == $folder['type'] ? 'selected="selected"' : NULL).' value="2">'. $lng['Video'] .'</option>';
        echo '<option '.(3 == $folder['type'] ? 'selected="selected"' : NULL).' value="3">'. $lng['Musiqa'] .'</option>';
        echo '<option '.(4 == $folder['type'] ? 'selected="selected"' : NULL).' value="4">'. $lng['Java dasturlar'] .'</option>';
        echo '<option '.(5 == $folder['type'] ? 'selected="selected"' : NULL).' value="5">'. $lng['Android'] .'</option>';
        echo '<option '.(6 == $folder['type'] ? 'selected="selected"' : NULL).' value="6">'. $lng['Windows Mobile'] .'</option>';
        echo '<option '.(7 == $folder['type'] ? 'selected="selected"' : NULL).' value="7">'. $lng['iPhone'] .'</option>';
        echo '<option '.(8 == $folder['type'] ? 'selected="selected"' : NULL).' value="8">'. $lng['Bada'] .'</option>';
        echo '<option '.(9 == $folder['type'] ? 'selected="selected"' : NULL).' value="9">'. $lng['Flash'] .'</option>';
        echo '<option '.(10 == $folder['type'] ? 'selected="selected"' : NULL).' value="10">'. $lng['Mavzular'] .'</option>';
        echo '<option '.(11 == $folder['type'] ? 'selected="selected"' : NULL).' value="11">'. $lng['Symbian'] .'</option>';
        echo '</select><br /><br />';
        
        if (!empty($folder['icon'])) {
            echo ''. $lng['ico'] .': <img src="'.HOME.'/files/loads/icons/'.$folder['icon'].'" wight="16" height="16" /> <a href="'.HOME.'/loads/'.$folder['id'].'/?edit&delicon">[x]</a><br /><br />'; 
        } else {
            echo ''. $lng['ico'] .': <img src="'.HOME.'/files/loads/icons/folder.gif" /> 
			<a href="'.HOME.'/loads/'.$folder['id'].'/?edit&uploadicon">['. $lng['Yuklash'] .']</a><br /><br />'; 
        }
        echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /></form>';

        if (isset($_GET['uploadicon'])) {
            if ($_POST['upload']) {
                if (isset($_FILES['file']['name'])) {
                    $name1 = $_FILES['file']['name']; # Fayl nomi
                    $ext = strtolower(strrchr($name1, '.')); # Fayl shakli
                    $file = time().$ext;
                    $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar
                        
                    if (preg_match('/.php/i', $name1) || preg_match('/.pl/i', $name1) || $name1 == '.htaccess' || !in_array($ext, $pictures)) {
                        $err .= ''. $lng['Fayl shaklida xatolik'] .'.<br />';
                    }            
                
                    if (!empty($err)) {
                        echo DIV_ERROR . $err . CLOSE_DIV;
                    } else {
                        
                        if (!empty($_FILES['file']['name'])) {
                            copy($_FILES['file']['tmp_name'], '../../files/loads/icons/'.$file);
                        }
                        
                        DB::$dbs->query("UPDATE ".LOADS." SET `icon` = ? WHERE `id` = ? ", array($file, $folder['id']));
                        header("Location: ".HOME."/loads/" . $folder['id'] . "/?edit&icon"); 
                    }
                }
            }
            echo DIV_AUT;
            echo '<form action="#" enctype="multipart/form-data" method="POST">';
            echo '<b>'. $lng['ico'] .':</b><br /><input name="file" type="file" style="width:96%;"/><br />';
            echo '<input type="submit" name="upload" value="'. $lng['Yuklash'] .'" /></form>';
            echo CLOSE_DIV;             
        }
         
        echo CLOSE_DIV;             
    }
        
    if ($_POST['add'] && privilegy('zc')) {
        $name = html($_POST['name']);

        if (empty($name)) {
            $err .= ''. $lng['Ichki bo`lim nomini kiriting'] .'<br />';
        }
        
        if (!empty($_FILES['icon']['name'])) {
            $name1 = $_FILES['icon']['name']; # Fayl nomi
            $ext = strtolower(strrchr($name1, '.')); # Fayl formati
            $par = getimagesize($_FILES['icon']['tmp_name']); # Rasm shakli
            $size = $_FILES['icon']['size']; # Fayl hajmi
            $file = time().$ext;
            $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar
            
            if (preg_match('/.phtml/i', $name) || preg_match('/.php/i', $name1) || preg_match('/.pl/i', $name1) || $name1 == '.htaccess' || !in_array($ext, $pictures)) {
                $err .= ''. $lng['Fayl shaklida xatolik'] .'.<br />';
            }            
        }
         
        if (!empty($err)) {
            echo DIV_ERROR . $err . CLOSE_DIV;
        } else {
            
            if (!empty($_FILES['icon']['name'])) {
                copy($_FILES['icon']['tmp_name'], '../../files/loads/icons/'.$file);
            }
            
            if (empty($_FILES['icon']['name'])) {
                $icon = '';
            } else {
                $icon = $file;
            }
            
            DB::$dbs->query("INSERT INTO ".LOADS_CAT." (`name`, `folder_id`, `icon`) VALUES (?, ?, ?)", array($name, $folder['id'], $file));
            header("Location: ".HOME."/loads/".$folder['id']."/"); 
        }
    }
    
          
 
 
    /* TOP Rasmlar */
    if ($folder['type'] == 1) {
        echo '<div class="white"><a href="'.HOME.'/loads/top/pictures/"><b>'. $lng['TOP Rasmlar'] .'</b></a></div>';
    }
     echo '<div class="grey">';
    echo '<form action="'.HOME.'/loads/search/" enctype="multipart/form-data" method="POST">';
    echo '<input type="text" name="q"  style="width:60%;"/> ';
    echo '<input type="submit" name="search" value="'. $lng['Izlash'] .'" /></form>';
    echo CLOSE_DIV;
	 
    if ($folder['type'] == 4 || $folder['type'] == 5 || $folder['type'] == 6 || $folder['type'] == 7 || $folder['type'] == 8 || $folder['type'] == 11) {
        echo DIV_BLOCK . '<a href="'.HOME.'/loads/top/appl/"><b>'. $lng['TOP 100'] .'</b></a>' . CLOSE_DIV;
    }
    
        
    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_CAT." WHERE `folder_id` = ?", array($folder['id']));
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Ichki bo`limlar topilmadi'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['loads_cat'],'folder='.$folder['id'].'&select=folder'); 
        $sql = DB::$dbs->query("SELECT * FROM ".LOADS_CAT." WHERE `folder_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['loads_cat']." ", array($folder['id']));
        while($folderc = $sql -> fetch()) {
            $files = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_FILE." WHERE `folderc_id` = ? ", array($folderc['id']));
            echo '<div class="touch"><a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/"><img src="' . (empty($folderc['icon']) ? HOME . '/files/loads/icons/folder.gif' : HOME . '/files/loads/icons/' . $folderc['icon']) . '" wight="16" height="16" /> '.$folderc['name'].' <span class="count">'.$files.'</span></a> </div>';
        }
        echo $n->navi();         
    }

        
    if (privilegy('zc')) {
     echo '<div class="lines">';
        echo '<form action="#" enctype="multipart/form-data" method="POST">';
        echo ''. $lng['Yangi ichki bo`lim'] .':<br /><input type="text" name="name" style="width:96%;"/><br />';
        
        echo ''. $lng['ico'] .':<br /><input name="icon" type="file" style="width:96%;"/><br />';
        
        echo '<input type="submit" name="add" value="'. $lng['Ochish'] .'" /></form>';
        echo CLOSE_DIV;
        
        echo DIV_BLOCK;
        echo '<a href="?edit">'. $lng['Tahrirlash'] .'</a><br />';
        echo '<a href="?del">'. $lng['O`chirish'] .'</a><br />';
        echo CLOSE_DIV; 
    }
echo '<div class="lines">';
echo '- <a href="'.HOME.'/loads/">'. $lng['Yuklamalar markazi'] .'</a>'; 
echo '</div>';     
    break;
    
    case 'folderc':
    $folder = DB::$dbs->queryFetch("SELECT * FROM ".LOADS." WHERE `id` = ? ", array(abs(num($_GET['folder']))));
    
    if (empty($folder)) {
        head(''. $lng['Bo`lim topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    } 
    
    $folderc = DB::$dbs->queryFetch("SELECT * FROM ".LOADS_CAT." WHERE `id` = ? ", array(abs(num($_GET['folderc']))));
    if (empty($folderc)) {
        head(''. $lng['Ichki bo`lim topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
    }    
    
    head('' . $folderc['name']);
    
    if (isset($_GET['del']) && privilegy('zc')) {
        if (!isset($_GET['go'])) {
            echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlash'] .':</b> 
			<a href="?del&go">['. $lng['O`chirish'] .']</a> <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
        } else {
            $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE." WHERE `folderc_id` = ? ", array($folderc['id']));
            while($file = $sql -> fetch()) {
                unlink('../../files/loads/files/' . $file['url']);
                @unlink('../../files/loads/files/mini_' . $file['url']);
            }
            @unlink('../../files/loads/icons/'.$folder['icon']);
            DB::$dbs->query("DELETE FROM ".LOADS_FILE." WHERE `folderc_id` = ? ", array($folderc['id']));
            DB::$dbs->query("DELETE FROM ".LOADS_CAT." WHERE `id` = ? ", array($folderc['id']));
            header("Location: ".HOME."/loads/".$folder['id']."/"); 
        }    
    }

    if (isset($_GET['edit']) && privilegy('zc')) {
        if ($_POST['edit']) {
            $name = html($_POST['name']);
            if (empty($name)) {
                echo DIV_ERROR . ''. $lng['Bo`lim nomini kiriting'] .'' . CLOSE_DIV;
            } else {
                DB::$dbs->query("UPDATE ".LOADS_CAT." SET `name` = ? WHERE `id` = ? ", array($name, $folderc['id']));
                header("Location: ".HOME."/loads/".$folder['id']."/".$folderc['id']."/"); 
            }
        }
            
        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo ''. $lng['Ichki bo`limni tahrirlash'] .':<br /><input type="text" value="'.$folderc['name'].'" name="name" />';
        echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /></form><br />';
        
        if (!empty($folderc['icon'])) {
            echo ''. $lng['ico'] .': <img src="'.HOME.'/files/loads/icons/'.$folderc['icon'].'" wight="16" height="16" /> <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/?edit&delicon">[x]</a><br /><br />'; 
        } else {
            echo ''. $lng['ico'] .': <img src="'.HOME.'/files/loads/icons/folder.gif" wight="16" height="16" /> <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/?edit&uploadicon">['. $lng['Yuklash'] .']</a><br /><br />'; 
        }

        if (isset($_GET['uploadicon'])) {
            if ($_POST['upload']) {
                if (isset($_FILES['file']['name'])) {
                    $name1 = $_FILES['file']['name']; # Fayl nomi
                    $ext = strtolower(strrchr($name1, '.')); # Fayl formati
                    $file = time().$ext;
                    $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar
                        
                    if (preg_match('/.phtml/i', $name) || preg_match('/.php/i', $name1) || preg_match('/.pl/i', $name1) || $name1 == '.htaccess' || !in_array($ext, $pictures)) {
                        $err .= ''. $lng['Fayl shaklida xatolik'] .'.<br />';
                    }            
                
                    if (!empty($err)) {
                        echo DIV_ERROR . $err . CLOSE_DIV;
                    } else {
                        
                        if (!empty($_FILES['file']['name'])) {
                            copy($_FILES['file']['tmp_name'], '../../files/loads/icons/'.$file);
                        }
                        
                        DB::$dbs->query("UPDATE ".LOADS_CAT." SET `icon` = ? WHERE `id` = ? ", array($file, $folderc['id']));
                        header("Location: ".HOME."/loads/" . $folder['id'] . "/".$folderc['id']."/?edit&icon"); 
                    }
                }
            }
            echo DIV_AUT;
            echo '<form action="#" enctype="multipart/form-data" method="POST">';
            echo '<b>'. $lng['ico'] .':</b><br /><input name="file" type="file" /><br />';
            echo '<input type="submit" name="upload" value="'. $lng['Yuklash'] .'" /></form>';
            echo CLOSE_DIV;             
        }
                
        echo CLOSE_DIV;             
    }
    
 
     echo '<div class="line">';
    echo '<form action="'.HOME.'/loads/search/" enctype="multipart/form-data" method="POST">';
    echo '<input type="text" name="q" style="width:60%;"/> ';
    echo '<input type="submit" name="search" value="'. $lng['Izlash'] .'" /></form>';
    echo CLOSE_DIV;
	
		
    if ($folder['type'] == 1) {
        echo '<div class="lines"><a href="'.HOME.'/loads/top/pictures/"><b>'. $lng['TOP Rasmlar'] .'</b></a></div>';
    }
    if ($folder['type'] == 4 || $folder['type'] == 5 || $folder['type'] == 6 || $folder['type'] == 7 || $folder['type'] == 8 || $folder['type'] == 11) {
        echo '<div class="lines"><a href="'.HOME.'/loads/top/appl/"><b>'. $lng['TOP 100'] .'</b></a></div>';
    }
    
    if ($folder['type'] == 1) {
    
        
        /* Rasm ko`rsatilishi */
        $array = array(30, 60, 100);
        
        if (!empty($_GET['prev'])) {
            $prev1 = $_GET['prev'];
            
            if ($prev1 == $array[0]) {
                unset($_SESSION['prev']);
            } elseif ($prev1 == $array[1]) {
                $_SESSION['prev'] = 1;
            } elseif ($prev1 == $array[2]) {
                $_SESSION['prev'] = 2;
            } else {
                $_SESSION['prev'] = 'no';
            }
        }
        
        if (empty($_SESSION['prev'])) {
            $prev = 'wight="'.$array[0].'" height="'.$array[0].'"';            
        } elseif ($_SESSION['prev'] == 1) {
            $prev = 'wight="'.$array[1].'" height="'.$array[1].'"';   
        } elseif ($_SESSION['prev'] == 2) {
            $prev = 'wight="'.$array[2].'" height="'.$array[2].'"';   
        } else {
            $prev = NULL; 
        }
    
        echo '<div class="lines">'.icon('screan.png').'  ' . (empty($_SESSION['prev']) ? '<b>['.$array[0].'x'.$array[0].']</b>' : '<a href="?prev='.$array[0].'">['.$array[0].'x'.$array[0].']</a>') . ' 
        ' . ($_SESSION['prev'] == 1 ? '<b>['.$array[1].'x'.$array[1].']</b>' : '<a href="?prev='.$array[1].'">['.$array[1].'x'.$array[1].']</a>') . ' ' .
        ($_SESSION['prev'] == 2 ? '<b>['.$array[2].'x'.$array[2].']</b>' : '<a href="?prev='.$array[2].'">['.$array[2].'x'.$array[2].']</a>') . ' ' . 
        ($_SESSION['prev'] == 'no' ? '<b>[x]</b>' : '<a href="?prev=no">[x]</a>');
        echo CLOSE_DIV;
    }
    /* *** */
    
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_FILE." WHERE `folderc_id` = ?", array($folderc['id']));
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Fayllar kiritilmagan'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['loads_file'],'folder='.$folder['id'].'&folderc='.$folderc['id'].'&select=folderc'); 
        $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE." WHERE `folderc_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['loads_file']." ", array($folderc['id']));
        while($file = $sql -> fetch()) {
            
            echo '<div class="lines">';
            /* Agar rasm bo`lsa */
            if ($folder['type'] == 1) {
                
                 echo ''.icon('rasm.png').' <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/">'.$file['name'].'</a> 
				<span class="count">'.get_size($file['size']).' / '.$file['type'].' ';
                 echo ' '.icon('chart.png').'  '.(empty($file['rating']) ? '0' : $file['rating']).'</span><br />';
				 
                if (empty($_SESSION['prev']) || $_SESSION['prev'] == 1 || $_SESSION['prev'] == 2 ) {
                    echo '<br/><a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/">
					<img src="'.HOME.'/files/loads/files/'.$file['url'].'" '.$prev.'/></a><br/>';
                }
            } else {
			    $screens = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_SCREEN." WHERE `file_id` = ? ", array($file['id']));
    
    if (!empty($screens)) {
        $sql22 = DB::$dbs->query("SELECT * FROM ".LOADS_SCREEN." WHERE `file_id` = ? ", array($file['id']));
        while($screen = $sql22 -> fetch()) {
            echo '<img src="'.HOME.'/files/loads/screen/'.$screen['url'].'" style="height:60px;"  /></a> ';
        }
    }
                 echo '<br />'.icon('zip.png').' <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/">'.$file['name'].'</a> ['.get_size($file['size']).' / '.$file['type'].']';
                 echo '  <span style="float:right;color:green">'.icon('chart.png').' '.(empty($file['rating']) ? '0' : $file['rating']).'</span><br />';
				 echo (!empty($file['info']) ? '' . SubstrMaus(text($file['info']), 100) . ' <br />' : NULL);
            }
            echo CLOSE_DIV;
        }
        echo $n->navi();          
    }
    
    
    
if (!empty($user)) {     
    echo DIV_AUT;
    echo '<form action="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/upload/" enctype="multipart/form-data" method="POST">';
    echo '<b>'. $lng['Fayl yuklash'] .':</b> ['.$folder['name'].']<br /><input type="file" name="file" style="width:96%;"/><br />';
    
    if ($folder['type'] == 3) {
        echo ''. $lng['Ijrochi'] .':<br /><input type="text" name="artist" style="width:96%;"/><br />';
        echo ''. $lng['Nomi'] .':<br /><input type="text" name="track" style="width:96%;"/><br />';
        echo ''. $lng['Albom nomi'] .':<br /><input type="text" name="album" style="width:96%;"/><br />';
    } else {
        echo ''. $lng['Fayl nomi'] .':<br /><input type="text" name="name" style="width:96%;"/><br />';
    }
    echo '<b>'. $lng['Qo`llanadigan fayl formatlari'] .':</b> ';
    echo type_view($folder['type']);
    echo '<br /><input type="submit" name="upload" value="'. $lng['Yuklash'] .'" />';
    echo '</form>';
    echo CLOSE_DIV;         
    }
    
    if (privilegy('zc')) {    
        echo '<div class="line">';
        echo '<a href="?edit">'. $lng['Tahrirlash'] .'</a><br />';
        echo '<a href="?del">'. $lng['O`chirish'] .'</a><br />';
        echo CLOSE_DIV; 
    }             

echo '<div class="lines">';
echo '- <a href="'.HOME.'/loads/">'. $lng['Yuklamalar markazi'] .'</a> / 
<a href="'.HOME.'/loads/'.$folder['id'].'/">'. $lng['Orqaga qaytish'] .'</a>'; 
echo '</div>';  

    
    require_once('../../core/stop.php');
    exit(); 
    break;

}


require_once('../../core/stop.php');
?>