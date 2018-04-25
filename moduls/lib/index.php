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
    head(''. $lng['Kutubxona'] .'');   

    if ($_POST['add'] && privilegy('lib')) {
        $name = html($_POST['name']);
            
        if (empty($name)) {
            echo DIV_ERROR . ''. $lng['Bo`lim nomini kiriting'] .'' . CLOSE_DIV;
        } else {
            DB::$dbs->query("INSERT INTO ".LIB." (`name`) VALUES (?)", array($name));
            header("Location: ".HOME."/lib/"); 
        }
    }

echo '<div class="white">';
echo ''.icon('search.png').' <a href="'.HOME.'/search/lib/">'. $lng['Izlash'] .'</a>'; 
echo '</div>';


    $sql = html($_SESSION['search']);
    $count = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LIB_ARTICL." WHERE `title` LIKE '%".$sql."%' OR `info` LIKE '%".$sql."%'");
    
    if (empty($count)) {
        echo DIV_LI . ''. $lng['Natijalar topilmadi'] .'' . CLOSE_DIV;
    } else {
        $sql = DB::$dbs->query("SELECT * FROM ".LIB_ARTICL." WHERE `title` LIKE '%".$sql."%' OR `info` LIKE '%".$sql."%' ORDER BY `id` DESC LIMIT 5");
        while($articl = $sql -> fetch()) { 
echo '<div class="white">';
            echo ''.icon('pages.png').'  <a href="'.HOME.'/lib/'.$articl['folder_id'].'/'.$articl['folderc_id'].'/'.$articl['id'].'/">'.$articl['title'].'</a><br/>';
echo '</div>'; 
}             
    }


    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LIB."");
        
    if ($all == 0) {
        echo DIV_AUT . ''. $lng['Bo`limlar xali ochilmagan'] .'' . CLOSE_DIV;
    } else {
        $sql = DB::$dbs->query("SELECT * FROM ".LIB." ORDER BY `id` DESC ");
        while($folder = $sql -> fetch()) {
            $articls = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LIB_ARTICL." WHERE `folder_id` = ? ", array($folder['id']));
            echo '<div class="lines">'.icon('kitob.png').' <a href="'.HOME.'/lib/'.$folder['id'].'/">'.$folder['name'].'</a> <span class="count">'.$articls.'</span> </div>';
        }
    }
    
    if (privilegy('lib')) {
        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo '<input type="text" name="name" /> ';
        echo '<input type="submit" name="add" value="'. $lng['Yangi bo`lim'] .'" /></form>';
        echo CLOSE_DIV; 
    } 
    break;
    
    case 'folder':
    $folder = DB::$dbs->queryFetch("SELECT * FROM ".LIB." WHERE `id` = ? ", array(abs(num($_GET['folder']))));
    
    if (empty($folder)) {
        head(''. $lng['Bo`lim topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV;  
              
    } else {
        
        head(' ' . $folder['name']);
           
        
        if (isset($_GET['del'])) {
            if (!isset($_GET['go'])) {
                echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlang'] .':</b> 
				<a href="?del&go">['. $lng['O`chirish'] .']</a> <a href="'.HOME.'/lib/'.$folder['id'].'/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
            } else {
                DB::$dbs->query("DELETE FROM ".LIB_CAT." WHERE `folder_id` = ? ", array($folder['id']));
                DB::$dbs->query("DELETE FROM ".LIB." WHERE `id` = ? ", array($folder['id']));
                header("Location: ".HOME."/lib/"); 
            }    
        }

        if (isset($_GET['edit']) && privilegy('lib')) {
            if ($_POST['edit']) {
                $name = html($_POST['name']);
                
                if (empty($name)) {
                    echo DIV_ERROR . ''. $lng['Bo`lim nomini kiriting'] .'' . CLOSE_DIV;
                } else {
                    DB::$dbs->query("UPDATE ".LIB." SET `name` = ? WHERE `id` = ? ", array($name, $folder['id']));
                    header("Location: ".HOME."/lib/".$folder['id']."/"); 
                }
            }
            
            echo DIV_AUT;
            echo '<form action="#" method="POST">';
            echo ''. $lng['Bo`limni tahrirlash'] .':<br /><input type="text" value="'.$folder['name'].'" name="name" />';
            echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /></form>';
            echo CLOSE_DIV;             
        }
                
        if ($_POST['add'] && privilegy('lib')) {
            $name = html($_POST['name']);
                
            if (empty($name)) {
                echo DIV_ERROR . ''. $lng['Bo`lim nomini kiriting'] .'' . CLOSE_DIV;
            } else {
                DB::$dbs->query("INSERT INTO ".LIB_CAT." (`folder_id`, `name`) VALUES (?, ?)", array($folder['id'], $name));
                header("Location: ".HOME."/lib/".$folder['id']."/"); 
            }
        }
                
        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LIB_CAT." WHERE `folder_id` = ?", array($folder['id']));
            
        if ($all == 0) {
            echo DIV_AUT . ''. $lng['Ichki bo`limlar yo`q'] .'' . CLOSE_DIV;
        } else {
            $n = new Navigator($all,$config['write']['lib_cat'],'folder='.$folder['id'].'&select=folder'); 
            $sql = DB::$dbs->query("SELECT * FROM ".LIB_CAT." WHERE `folder_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['lib_cat']." ", array($folder['id']));
            while($folderc = $sql -> fetch()) {
                $articls = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LIB_ARTICL." WHERE `folderc_id` = ? ", array($folderc['id']));
                echo '<div class="lines">'.icon('box.png').'  <a href="'.HOME.'/lib/'.$folder['id'].'/'.$folderc['id'].'/">'.$folderc['name'].'</a> <span class="count"> '.$articls.'</span> </div>';
            }
            echo $n->navi(); 
        }        
    }

    if (privilegy('lib')) {    
        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo '<input type="text" name="name" /> ';
        echo '<input type="submit" name="add" value="'. $lng['Yangi bo`lim'] .'" /></form>';
        echo CLOSE_DIV; 
        
        if (!empty($folder)) {
            echo DIV_BLOCK;
            echo '<a href="?edit">'. $lng['Tahrirlash'] .'</a><br />';
            echo '<a href="?del">'. $lng['O`chirish'] .'</a><br />';
            echo CLOSE_DIV;  
        }
    }
    
    break;
    
    case 'articls':
    $folder = DB::$dbs->queryFetch("SELECT * FROM ".LIB." WHERE `id` = ? ", array(abs(num($_GET['folder']))));
    
    if (empty($folder)) {
        head(''. $lng['Bo`lim topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
              
    }
        
    $folderc = DB::$dbs->queryFetch("SELECT * FROM ".LIB_CAT." WHERE `id` = ? ", array(abs(num($_GET['folderc']))));
    
    if (empty($folderc)) {
        head(''. $lng['Ichki bo`lim topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
              
    }
    
    head('' . $folderc['name'] . '');
        

    if (isset($_GET['del']) && privilegy('lib')) {
        if (!isset($_GET['go'])) {
            echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlang'] .':</b> 
			<a href="?del&go">['. $lng['O`chirish'] .']</a>
			<a href="'.HOME.'/lib/'.$folderc['id'].'/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
        } else {
            DB::$dbs->query("DELETE FROM ".LIB_CAT." WHERE `id` = ? ", array($folderc['id']));
            header("Location: ".HOME."/lib/".$folder['id']."/"); 
        }    
    }

    if (isset($_GET['edit']) && privilegy('lib')) {
        if ($_POST['edit']) {
            $name = html($_POST['name']);
            if (empty($name)) {
                echo DIV_ERROR . ''. $lng['Ichki bo`lim nomini kiriting'] .'' . CLOSE_DIV;
            } else {
                DB::$dbs->query("UPDATE ".LIB_CAT." SET `name` = ? WHERE `id` = ? ", array($name, $folderc['id']));
                header("Location: ".HOME."/lib/".$folder['id']."/".$folderc['id']."/"); 
            }
        }
            
        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo ''. $lng['Ichki bo`limni tahrirlash'] .':<br /><input type="text" value="'.$folderc['name'].'" name="name" />';
        echo '<input type="submit" name="edit" value="'. $lng['O`zgartirish'] .'" /></form>';
        echo CLOSE_DIV;             
    }
            
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LIB_ARTICL." WHERE `folderc_id` = ?", array($folderc['id']));
            
    if ($all == 0) {
        echo DIV_AUT . ''. $lng['Maqolalar hali kiritilmagan'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,$config['write']['lib_articl'],'select=articls&folder='.$folder['id'].'&folderc='.$folderc['id']); 
        $sql = DB::$dbs->query("SELECT * FROM ".LIB_ARTICL." WHERE `folderc_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['lib_articl']." ", array($folderc['id']));
        while($articl = $sql -> fetch()) {
           echo '<div class="lines">'.icon('pages.png').'  <a href="'.HOME.'/lib/'.$folder['id'].'/'.$folderc['id'].'/'.$articl['id'].'/">'.$articl['title'].'</a>' . CLOSE_DIV;
        }
        echo $n->navi();
    }    
    
    echo DIV_AUT . '<b>'. $lng['Yangi maqola'] .':</b><br />
	<form action="'.HOME.'/lib/'.$folder['id'].'/'.$folderc['id'].'/add/" enctype="multipart/form-data" method="POST">
    '. $lng['Nomi'] .':<br />
	<input type="text" name="name" style="width:95%;"/><br />
    '. $lng['Matni'] .':<br />
	<textarea name="text" style="width:95%;height:5pc;"></textarea><br />
    '. $lng['Kitobni kiritish'] .' (.txt):<br />
    <input type="file" name="file" style="width:95%;"/><br />
    '. $lng['Ta`rif'] .':<br />
	<textarea name="info" style="width:95%;height:5pc;"></textarea><br />
    '. $lng['Screenshot'] .':<br />
    <input type="file" name="screen" style="width:95%;"/><br />
    <input type="submit" name="add" value="'. $lng['Maqola yaratish'] .'" /></form>' . CLOSE_DIV;
                        
    if (privilegy('lib')) {    
        echo DIV_BLOCK;
        echo '<a href="?edit">'. $lng['Tahrirlash'] .'</a><br />';
        echo '<a href="?del">'. $lng['O`chirish'] .'</a><br />';
        echo CLOSE_DIV;  
    }
    break;
    
    case 'articl':
    $folder = DB::$dbs->queryFetch("SELECT * FROM ".LIB." WHERE `id` = ? ", array(abs(num($_GET['folder']))));
    
    if (empty($folder)) {
        head(''. $lng['Bo`lim topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
              
    }
        
    $folderc = DB::$dbs->queryFetch("SELECT * FROM ".LIB_CAT." WHERE `id` = ? ", array(abs(num($_GET['folderc']))));
    
    if (empty($folderc)) {
        head(''. $lng['Ichki bo`lim topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
              
    }

    $articl = DB::$dbs->queryFetch("SELECT * FROM ".LIB_ARTICL." WHERE `id` = ? ", array(abs(num($_GET['articl']))));
    if (empty($articl)) {
        head(''. $lng['Maqola topilmadi'] .'');
        echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
        require_once('../../core/stop.php');
        exit(); 
              
    }    
    head('' . $articl['title']);

    if (isset($_GET['del'])) {
        if (!isset($_GET['go'])) {
            echo DIV_LI . '<b>'. $lng['O`chirishni tastiqlang'] .':</b> 
			<a href="?del&go">['. $lng['O`chirish'] .']</a> 
			<a href="'.HOME.'/lib/'.$folder['id'].'/'.$folderc['id'].'/'.$articl['id'].'/">['. $lng['Yo`q'] .']</a>' . CLOSE_DIV;
        } else {
            DB::$dbs->query("DELETE FROM ".LIB_COMM." WHERE `articl_id` = ? ", array($articl['id']));
            @unlink('../../files/lib/screen/'.$articl['screen']);
            @unlink('../../files/lib/jar/'.$articl['id'].'.jar');
            @unlink('../../files/lib/jad/'.$articl['id'].'.jad');
            @unlink('../../files/lib/text/'.$articl['text']);
            DB::$dbs->query("DELETE FROM ".LIB_ARTICL." WHERE `id` = ? ", array($articl['id']));
            header("Location: ".HOME."/lib/".$folder['id']."/".$folderc['id']."/"); 
        }    
    }
                
    
    echo DIV_BLOCK . '';
    if (!empty($articl['screen'])) {
        echo '<center><a href="'.HOME.'/files/lib/screen/'.$articl['screen'].'">
		<img src="'.HOME.'/files/lib/screen/'.$articl['screen'].'" style="width:95;height:200px;"/></a></center><br />';
    } 
    echo '' . text($articl['info']);
    echo CLOSE_DIV;


    nav;

 $simvol = $config['write']['lib_articl_str'];                // Sahifadagi belgilar soni

// Sahifalar bo`yicha navigatsiya

$tx = file_get_contents('../../files/lib/text/'.$articl['text']);
$strrpos = mb_strrpos($tx, " ");
$pages = 1;
// Sahifa raqamini topamiz
if (isset($_GET['page'])) {
    $page = abs(intval($_GET['page']));
    if ($page == 0)
        $page = 1;
        $start = $page - 1;
    }
    else {
        $page = $start + 1;
    }
    $t_si = 0;
    if ($strrpos) {
        while ($t_si < $strrpos) {
            $string = mb_substr($tx, $t_si, $simvol);
            $t_ki = mb_strrpos($string, " ");
            $m_sim = $t_ki;
            $strings[$pages] = $string;
            $t_si = $t_ki + $t_si;
            if ($page == $pages) {
                $page_text = $strings[$pages];
            }
                if ($strings[$pages] == "") {
                   $t_si = $strrpos++;
                }
                else {
                  $pages++;
                }
        }
                if ($page >= $pages) {
                    $page = $pages - 1;
                    $page_text = $strings[$page];
                }
                $pages = $pages - 1;
                if ($page != $pages) {
                    $prb = mb_strrpos($page_text, " ");
                    $page_text = mb_substr($page_text, 0, $prb);
                }
            }
            else {
                $page_text = $tx;
            }

            // Maqolani filtrdan chiqarish va ko`rgazish
            $page_text = htmlentities($page_text, ENT_QUOTES, 'UTF-8');
			$page_text = nl2br($page_text);

            echo DIV_BLOCK . $page_text . CLOSE_DIV;
            
            if ($pages > 1) {
               echo DIV_LI; 
               nav($pages,$page,$num,'');
               echo CLOSE_DIV;
            }
    
    echo DIV_BLOCK;
    echo ''.icon('yuklama.png').'  '. $lng['Yuklab olish'] .': <a href="'.HOME.'/lib/'.$folder['id'].'/'.$folderc['id'].'/'.$articl['id'].'/load/?jar">[JAR]</a> <a href="'.HOME.'/lib/'.$folder['id'].'/'.$folderc['id'].'/'.$articl['id'].'/load/?jad">[JAD]</a> <a href="'.HOME.'/lib/'.$folder['id'].'/'.$folderc['id'].'/'.$articl['id'].'/load/?txt">[TXT]</a><br />';

    $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LIB_COMM." WHERE `articl_id` = ?", array($articl['id']));
    echo ''.icon('chat.png').' '. $lng['Sharhlar'] .' ['.$comm.']<br />';
        if (privilegy('lib')) {
        echo ''.icon('del.png').'  <a href="?del">'. $lng['O`chirish'] .'</a>';      
    }
	echo CLOSE_DIV;
    


        

    if ($_POST) {
        $comm = html($_POST['comm']);
        
        if (empty($comm)) {
            $err = ''. $lng['Sharh bo`sh'] .'';
        }
                
        if (!empty($err)) {
            echo DIV_ERROR . $err . CLOSE_DIV;
        } else {
            DB::$dbs->query("INSERT INTO ".LIB_COMM." (`articl_id`, `user_id`, `comm`, `time`) VALUES (?, ?, ?, ?)", array($articl['id'], $user['user_id'], $comm, time()));
            balls_operation(2);
            echo DIV_MSG . ''. $lng['Sharh kiritildi'] .'' . CLOSE_DIV;                    
        }
    }
            
    $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LIB_COMM." WHERE `articl_id` = ?", array($articl['id']));
 

    if (!empty($_GET['del_comm'])) {
        DB::$dbs->query("DELETE FROM ".LIB_COMM." WHERE `id` = ? ", array(num($_GET['del_comm'])));
    }
                       
    if (empty($comm)) {
        echo DIV_BLOCK . ''. $lng['Sharhlar yo`q'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($comm,$config['write']['lib_comm'],'select=comm&id='.$articl['id']); 
        $sql = DB::$dbs->query("SELECT * FROM ".LIB_COMM." WHERE `articl_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['lib_comm']."", array($articl['id']));
        while($comm = $sql -> fetch()) {
		
		echo '<div class="white">';
echo '<table cellspacing="0" cellpadding="0" style="margin-bottom:5px;" width="100%" ><tr>';
echo '<td class="grey" style="width:5%;border-radius: 6px 0 0 6px;"><center>';
echo '' . avatar($comm['user_id'],40,40) . '';
echo '</center></td>';

echo '<td class="grey" style="width:95%;border-radius:  0 6px 6px 0;">';

            echo ' '. userLink($comm['user_id']) . ' 
			' . (privilegy('lib_moder') ? ' <a href="?del_comm='.$comm['id'].'" style="float:right;">'.icon('minus2.png').'</a>' : NULL) . '';
        echo '<br/><span class="mini">' . vrem($comm['time']) . '</span></td></tr></table>';
		echo '' . text($comm['comm']) . '';
		echo '</div>';
		
		
        }
        echo $n->navi();                   
    }
            
    echo DIV_AUT;
    echo '<form action="#" method="POST">';
    echo '<textarea name="comm" style="width:95%;height:5pc;"></textarea><br />';
    echo '<input type="submit" value="'. $lng['Kiritish'] .'" />';
    bbsmile(); 
	echo '</form>';
    echo CLOSE_DIV;
             
    break;
    
    case 'load':
    head(''. $lng['Faylni yuklash'] .'');
    $articl = DB::$dbs->queryFetch("SELECT * FROM ".LIB_ARTICL." WHERE `id` = ? ", array(abs(num($_GET['articl']))));
          
    
    echo DIV_BLOCK;
    if (isset($_GET['jar'])) {
    
        if (!file_exists('../../files/lib/jar/' . $articl['id'] . '.jar')) {
            $text = file_get_contents('../../files/lib/text/'.$articl['text']);
            $midlet_name = mb_substr($articl['title'], 0, 10);
            $midlet_name = iconv('UTF-8', 'windows-1251', $midlet_name);

            // Maqolaning matnini yozamiz
            $files = fopen("java/book.txt", 'w+');
            flock($files, LOCK_EX);
            $book_name = iconv('UTF-8', 'windows-1251', $articl['title']);
            $book_text = iconv('UTF-8', 'windows-1251', $text);
            $result = $book_text;
            fputs($files, $result);
            flock($files, LOCK_UN);
            fclose($files);
        // Arxiv yaratamiz
        require_once ('../../core/class/pclzip.lib.php');
        $archive = new PclZip('../../files/lib/jar/' . $articl['id'] . '.jar');
        $list = $archive->create('java', PCLZIP_OPT_REMOVE_PATH, 'java');
    }
    
    header("Location: ".HOME."/files/lib/jar/".$articl['id'].".jar");
    
    } elseif (isset($_GET['jad'])) {
        $text = file_get_contents('../../files/lib/text/'.$articl['text']);
        if (!file_exists('../../files/lib/jar/' . $articl['id'] . '.jar')) {
            $midlet_name = mb_substr($articl['title'], 0, 10);
            $midlet_name = iconv('UTF-8', 'windows-1251', $midlet_name);

            // Maqolaning matnini yozamiz
            $files = fopen("java/book.txt", 'w+');
            flock($files, LOCK_EX);
            $book_name = iconv('UTF-8', 'windows-1251', $articl['title']);
            $book_text = iconv('UTF-8', 'windows-1251', $text);
            $result = $book_text;
            fputs($files, $result);
            flock($files, LOCK_UN);
            fclose($files);
        // Arxiv yaratamiz
        require_once ('../../core/class/pclzip.lib.php');
        $archive = new PclZip('../../files/lib/jar/' . $articl['id'] . '.jar');
        $list = $archive->create('java', PCLZIP_OPT_REMOVE_PATH, 'java');
    }
    
        if (!file_exists('../../files/lib/jad/' . $articl['id'] . '.jad')) {
            $filesize = filesize('../../files/lib/jad/' . $articl['id'] . '.jar');
            $jad_text = 'Manifest-Version: 1.0
            MIDlet-1: ' . $articl['id'] . ', , br.BookReader
            MIDlet-Name: ' . $articl['id'] .'
            MIDlet-Vendor: Miledi
            MIDlet-Version: 1.5.3
            MIDletX-No-Command: true
            MIDletX-LG-Contents: true
            MicroEdition-Configuration: CLDC-1.0
            MicroEdition-Profile: MIDP-1.0
            TCBR-Platform: Generic version (all phones)
            MIDlet-Jar-Size: ' . $filesize. '
            MIDlet-Jar-URL: ' . HOME . '/files/lib/jar/' . $articl['id'] . '.jar';
            $files = fopen('../../files/lib/jad/' . $articl['id'] . '.jad', 'w+');
            flock($files, LOCK_EX);
            fputs($files, $jad_text);
            flock($files, LOCK_UN);
            fclose($files);
        }
        header("Location: ".HOME."/files/lib/jad/".$articl['id'].".jad");
    } elseif (isset($_GET['txt'])) {
        ob_clean();
        ob_implicit_flush();
        header('Content-Type: text/plain; charset=utf-8', true);
        
        header('Content-Disposition: attachment; filename='.$articl['id'].'.txt');
        $text = file_get_contents('../../files/lib/text/'.$articl['text']);
        echo $text;
        exit;
    } else {
        echo ''. $lng['Xatolik'] .'';
    }
    echo CLOSE_DIV;
    require_once('../../core/stop.php');
    break;
    
}


require_once('../../core/stop.php');
?>