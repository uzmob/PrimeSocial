<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');
require_once('func.php');
require_once('../../core/class/id.php'); 
check_auth();

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

$file = DB::$dbs->queryFetch("SELECT * FROM ".LOADS_FILE." WHERE `id` = ? ", array(abs(num($_GET['file']))));
if (empty($file)) {
    head(''. $lng['Fayl topilmadi'] .'');
    echo DIV_ERROR . ''. $lng['Xatolik'] .'!' . CLOSE_DIV; 
    require_once('../../core/stop.php');
    exit(); 
}  

/* **** */    
head('' . $file['name']); 


if ($_POST) {
    $comm = html($_POST['comm']);
                
    if (empty($comm)) {
        $err = ''. $lng['Sharh bo`sh'] .'';
    }
                
    if (!empty($err)) {
        echo DIV_ERROR . $err . CLOSE_DIV;
    } else {
        DB::$dbs->query("INSERT INTO ".LOADS_COMM." (`file_id`, `user_id`, `comm`, `time`) VALUES (?, ?, ?, ?)", array($file['id'], $user['user_id'], $comm, time()));
        balls_operation(2);
        echo DIV_MSG . ''. $lng['Sharh kiritildi'] .'' . CLOSE_DIV;                    
    }


    if (!empty($_GET['otv']) && $_GET['otv'] != $user['user_id']) {
        $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM ".USERS." WHERE `user_id` = ? ",array(abs(num($_GET['otv']))));
        if (!empty($ank)) {
            $msg = '[b]' . $ank['nick'] . '[/b], ' . $msg;
        }
        
        $lenta = '<a href="'.HOME.'/id'.$user['user_id'].'"><b>' . $user['nick'] . '</b></a> '. $lng['sizning habaringizga javob berdi'] .' | <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/"><b>'. $lng['Yuklamalarda'] .'</b></a>';
        lenta($lenta, $ank['user_id']);
    }  
	}

echo DIV_BLOCK;

/* Musiqa */
if ($folder['type'] == 3) {
    echo '<b>' . $file['name'] . '</b> [' . get_size($file['size']) . ']<br /><br />';
    ?>
    <object type="application/x-shockwave-flash" data="/mouduls/loads/inc/player.swf" height="20" width="290">
    <param name="movie" value="http://rugame.mobi/mp3/mp3_play2.swf">
    <param name="FlashVars" value="soundFile=<?php echo HOME."/files/loads/files/".$file['url']; ?>&amp;titles=">
    <param name="quality" value="high">
    <param name="wmode" value="transparent">
    <embed width="290" height="20" src="http://rugame.mobi/mp3/mp3_play2.swf" type="application/x-shockwave-flash" flashvars="soundFile=<?php echo HOME."/files/loads/files/".$file['url']; ?>;titles="></embed>
    </object>
    <?php
    
    echo '<br /><br /><b>'. $lng['Ijrochi'] .':</b> ' . $file['artist'] . '<br />';
    echo '<b>'. $lng['Nomi'] .':</b> ' . $file['track'] . '<br />';
    echo (!empty($file['album']) ? '<b>'. $lng['Albom'] .':</b> ' . $file['album'] . '<br />' : NULL);
    
    echo '<br />';
    echo ' ' . icon('download.png') . '  '. $lng['Yuklangan'] .': ' . $file['loads'] . ' <br />';
    echo ' ' . icon('overtime.png') . '  '. $lng['Joylangan'] .': ' . vrem($file['time']) . '<br />';    
    echo ' ' . icon('ank.png') . '  '. $lng['Joyladi'] .': ' . user_choice($file['user_id'], 'link') . '<br /><br />';
	
if (!empty($user)) {
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_RATING." WHERE `file_id` = ? && `user_id` = ? ", array($file['id'], $user['user_id'])) == FALSE) {
        echo '<b>'. $lng['Fayl reytingi'] .':</b> <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/rating/plus/">[+]</a> '.(empty($file['rating']) ? '0' : $file['rating']).' <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/rating/minus/">[-]</a><br />'; 
    } else {
        echo '<b>'. $lng['Fayl reytingi'] .':</b> '.(empty($file['rating']) ? '0' : $file['rating']).'<br />'; 
    }
	  }
    echo ' ' . icon('yuklama.png') . '  <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/download/">'. $lng['Yuklab olish'] .'</a> ['.$file['type'].']<br />';
    
    $id3 = &new MP3_Id(); 
    $result = $id3->read("../../files/loads/files/".$file['url']); 

    // Xatolik "Tag not found" 
    if (PEAR::isError($result) && $result->getCode() !== PEAR_MP3_ID_TNF) { 
    die($result->getMessage() . "\n"); 
    } 
    
    $result = $id3->study(); 
    if (PEAR::isError($result)) { 
    die($result->getMessage() . "\n"); 
    } 

    echo '<br />';
    // Maydonchani o`qib, ma`lumotni chiqaramiz
    echo '<b>'. $lng['Trek haqida ma`lumot'] .':</b><br />';
    echo ' - '. $lng['Nomi'] .': ' . $id3->getTag('name') . "<br />"; 
    echo ' - '. $lng['Ijrochi'] .': ' . $id3->getTag('artists') . "<br />"; 
    echo ' - '. $lng['Albom'] .': ' . $id3->getTag('album') . "<br />"; 
    echo ' - '. $lng['yil'] .': ' . $id3->getTag('year') . "<br />"; 
    echo ' - '. $lng['Sharh'] .': ' . $id3->getTag('comment') . "<br />"; 
    echo ' - '. $lng['Janr'] .': ' . $id3->getTag('genre') . "<br />"; 
    echo ' - '. $lng['Trek'] .': ' . $id3->getTag('track') . "<br /><br />"; 
    
    echo ' - MPEG ' . $id3->getTag('mpeg_ver') . ' Layer ' . $id3->getTag('layer') . "\n"; echo $id3->getTag('mode') . "<br />"; 
    echo ' - '. $lng['Fayl hajmi'] .': ' . $id3->getTag('filesize') . " Bytes<br />"; 
    echo ' - '. $lng['Bitrate'] .': ' . $id3->getTag('bitrate') . "kB/s<br />"; 
    echo ' - '. $lng['Davomiyligi'] .': ' . $id3->getTag('length') . " min<br />"; 
    echo ' - '. $lng['Samplerate'] .': ' . $id3->getTag('frequency') . "Hz<br />"; 
    

    if (privilegy('zc')) {
        echo '<br /><a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/edit/delete/">'. $lng['Faylni o`chirish'] .'</a><br />';
    }    
}

/* Dastur */
if ($folder['type'] == 4 || $folder['type'] == 5 || $folder['type'] == 6 || $folder['type'] == 7 || $folder['type'] == 8 || $folder['type'] == 11) {
    
    echo '<b>' . $file['name'] . '</b> [' . get_size($file['size']) . ']<br /><br />';
    
    /* Skrinshotlar */ 
    $screens = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_SCREEN." WHERE `file_id` = ? ", array($file['id']));
    
    if (!empty($screens)) {
        $sql = DB::$dbs->query("SELECT * FROM ".LOADS_SCREEN." WHERE `file_id` = ? ", array($file['id']));
        while($screen = $sql -> fetch()) {
            echo '<a href="'.HOME.'/files/loads/screen/'.$screen['url'].'"><img src="'.HOME.'/files/loads/screen/'.$screen['url'].'"  style="width:120px;"  /></a> &nbsp; ';
        }
    }
    
    echo '<br />';
	
if (!empty($user)) {
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_RATING." WHERE `file_id` = ? && `user_id` = ? ", array($file['id'], $user['user_id'])) == FALSE) {
        echo '<span style="float:right;"> <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/rating/plus/">' . icon('heart.png') . '</a> '.(empty($file['rating']) ? '0' : $file['rating']).' <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/rating/minus/">' . icon('dislike.png') . '</a></span><br />'; 
    } else {
        echo '<span style="float:right;">' . icon('heart.png') . ' '.(empty($file['rating']) ? '0' : $file['rating']).'</span><br />'; 
    }
    }
    echo (!empty($file['info']) ? '<b>'. $lng['Nomi'] .':</b> ' . text($file['info']) . ' <br />' : NULL);
    echo (!empty($file['lang']) ? '<b>'. $lng['Til'] .':</b> ' . $file['lang'] . ' <br />' : NULL);

    echo '<br />';
    echo ' ' . icon('download.png') . ' '. $lng['Yuklangan'] .': ' . $file['loads'] . ' <br />';
    echo ' ' . icon('overtime.png') . ' '. $lng['Joylangan'] .': ' . vrem($file['time']) . '<br />';    
    echo ' ' . icon('ank.png') . '  '. $lng['Joyladi'] .': ' . user_choice($file['user_id'], 'link') . '<br /><br />';
    
    if ($file['type'] == '.jar') {
        if (!file_get_contents('../../files/loads/files/' . $file['id'] . '.jad')) {
            include_once '../../core/class/pclzip.lib.php';
            $zip = new PclZip('../../files/loads/files/'.$file['url']);
        
            $content = $zip->extract(PCLZIP_OPT_BY_NAME, "META-INF/MANIFEST.MF", PCLZIP_OPT_EXTRACT_AS_STRING);
        
            $jad = eregi_replace("(MIDlet-Jar-URL:( )*[^(\n|\r)]*)", null, $content[0]['content']);
            $jad = eregi_replace("(MIDlet-Jar-Size:( )*[^(\n|\r)]*)(\n|\r)", null, $jad);
            $jad = trim($jad);
            $jad .= "\r\nMIDlet-Jar-Size: " . filesize($file_info['s_name']) . "";
            $jad .= "\r\nMIDlet-Jar-URL: $jar.jar";
        
            file_put_contents('../../files/loads/files/'.$file['id'].'.jad', $jad); 
            
            header("Location: ".HOME."/loads/".$folder['id']."/".$folderc['id']."/".$file['id']."/");             
        }
      
        $file2 = file('../../files/loads/files/'.$file['id'].'.jad');
    
        $total = count($file2);
        for ($p = 0; $p < $total; $p++) {
            $dt = explode(":", $file2[$p]);
                if ($dt[0] == "MIDlet-Vendor") {
                    $poz = $dt[0] . ':' . $dt[1] . '';
                }
        }
        $poz = str_replace('MIDlet-Vendor:', '', $poz);
        htmlspecialchars($poz);
        

        $total = count($file2);
        for ($p = 0; $p < $total; $p++) {
            $dt = explode(":", $file2[$p]);
                if ($dt[0] == "MIDlet-Version") {
                    $ver = $dt[0] . ':' . $dt[1] . '';
                }
        }
        $ver = str_replace('MIDlet-Version:', '', $ver);
        htmlspecialchars($ver);

        echo '<b>'. $lng['Dastur haqida ma`lumot'] .':</b><br />';
        echo '<b>'. $lng['Ishlab chiqaruvchi'] .':</b> ' . $poz . '<br />';
        echo '<b>'. $lng['Versiya'] .':</b> ' . $ver . '<br />';
    }
    

    echo '' . icon('yuklash.png') . ' '. $lng['Yuklab olish'] .': <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/download/">'.$file['name'].'</a> ['.$file['type'].']<br />';
    
    $files = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_FILE_DOP." WHERE `file_id` = ? ", array($file['id']));
    
    if (!empty($files)) {
        echo '<br /><b>'. $lng['Qo`shimcha versiyalari'] .':</b><br />';
        $sql = DB::$dbs->query("SELECT * FROM ".LOADS_FILE_DOP." WHERE `file_id` = ? ", array($file['id']));
        while($dop = $sql -> fetch()) {
            echo '' . icon('yuklash.png') . ' <a href="'.HOME.'/files/loads/files/'.$dop['url'].'">'.$dop['name'].'</a> ['. $lng['Til'] .': '.$dop['lang'].' / '. $lng['Hajmi'] .': '.get_size($dop['size']).' / '.$dop['type'].']<br />';
        }
    }
        
      
 
    
    if (privilegy('zc')) {
        echo '' . icon('screan.png') . ' <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/edit/screen/">'. $lng['Skrinshotlarni boshqarish'] .'</a><br />';
        echo '' . icon('box.png') . ' <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/edit/ver/">'. $lng['Versiyalarni boshqarish'] .'</a><br />';
        echo '' . icon('sozlash.png') . ' <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/edit/edit/">'. $lng['Faylni tahrirlash'] .'</a><br />';
        echo '' . icon('del.png') . ' <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/edit/delete/">'. $lng['Faylni o`chirish'] .'</a><br />';
    }
        
}

/* Mavzu, flesh, videolar */
if ($folder['type'] == 2 || $folder['type'] == 9 || $folder['type'] == 10) {
    echo '<b>' . $file['name'] . '</b> [' . get_size($file['size']) . ']<br /><br />';

    /* Skrinshotlar */ 
    $screens = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_SCREEN." WHERE `file_id` = ? ", array($file['id']));
    
    if (!empty($screens)) {
        $sql = DB::$dbs->query("SELECT * FROM ".LOADS_SCREEN." WHERE `file_id` = ? ", array($file['id']));
        while($screen = $sql -> fetch()) {
            echo '<a href="'.HOME.'/files/loads/screen/'.$screen['url'].'"><img src="'.HOME.'/files/loads/screen/'.$screen['url'].'" style="width:120px;"  /></a>';
        }
    }
    
    echo '<br />';

if (!empty($user)) {
    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_RATING." WHERE `file_id` = ? && `user_id` = ? ", array($file['id'], $user['user_id'])) == FALSE) {
        echo '<span style="float:right;"><a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/rating/plus/">' . icon('heart.png') . '</a> '.(empty($file['rating']) ? '0' : $file['rating']).' <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/rating/minus/">' . icon('dislike.png') . '</a></span><br />'; 
    } else {
        echo '<span style="float:right;">'.(empty($file['rating']) ? '0' : $file['rating']).'</span><br />'; 
    }
	}
    echo '' . icon('download.png') . ' '. $lng['Yuklangan'] .': ' . $file['loads'] . '<br />';
    echo '' . icon('overtime.png') . ' '. $lng['Joylangan'] .': ' . vrem($file['time']) . '<br />';
    echo ' ' . icon('ank.png') . '  '. $lng['Joyladi'] .': ' . user_choice($file['user_id'], 'link') . '<br /><br />';
    
    echo '' . icon('yuklash.png') . ' '. $lng['Yuklab olish'] .': <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/download/">'.$file['name'].'</a> ['.$file['type'].']<br />';
    

    


    if (privilegy('zc')) {
        echo '' . icon('screan.png') . ' <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/edit/screen/">'. $lng['Skrinshotlarni boshqarish'] .'</a><br />';
        echo '' . icon('del.png') . ' <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/edit/delete/">'. $lng['Faylni o`chirish'] .'</a><br />';
    }
}
/* Rasmlar */
if ($folder['type'] == 1) {
    echo '<img src="'.HOME.'/files/loads/files/'.$file['url'].'" style="width:240px;"/><br />';
    
if (!empty($user)) {
	    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_RATING." WHERE `file_id` = ? && `user_id` = ? ", array($file['id'], $user['user_id'])) == FALSE) {
        echo '<span style="float:right;"> <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/rating/plus/">' . icon('heart.png') . '</a> '.(empty($file['rating']) ? '0' : $file['rating']).' <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/rating/minus/">' . icon('dislike.png') . '</a></span><br />'; 
    } else {
        echo '<span style="float:right;"> '.(empty($file['rating']) ? '0' : $file['rating']).'</span><br />'; 
    }
	}
    $img = getimagesize('../../files/loads/files/'. $file['url']);
    echo ''. $lng['Shakli'] .': ' . $img[0] . 'x' . $img[1] . '<br />';
    echo ''. $lng['Hajmi'] .': ' . get_size($file['size']) . '<br />';
    echo ''. $lng['Yuklangan'] .': ' . $file['loads'] . ' <br />';
    echo ''. $lng['Joylangan'] .': ' . vrem($file['time']) . '<br /><br />';
    echo ' ' . icon('ank.png') . '  '. $lng['Joyladi'] .': ' . user_choice($file['user_id'], 'link') . '<br />';
    

    echo '' . icon('yuklash.png') . ' <b>'. $lng['Rasmni yuklash'] .':</b><br />';
    $img = getimagesize('../../files/loads/files/'. $file['url']);
    echo ' - <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/download/">' . $img[0] . 'x' . $img[1] . '</a> ['. $lng['Asl turi'] .']<br />';
    $arr = array('130x130','120x160','132x176','176x220','240x320');
    foreach($arr as $v) {
	   list ($W,$H) = explode('x',$v);
	   echo ' - <a href="'.HOME.'/moduls/loads/im.php?id='.$file['id'].'&H='.$H.'&W='.$W.'">'.$v.'</a><br/ >';
    }
    
}

echo CLOSE_DIV;


 


    $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".LOADS_COMM." WHERE `file_id` = ?", array($file['id']));
    echo '<div class="lines"><b>'. $lng['Sharhlar'] .'</b> '.$comm.'</div>';            


if (!empty($_GET['del_comm'])) {
    DB::$dbs->query("DELETE FROM ".LOADS_COMM." WHERE `id` = ? ", array(num($_GET['del_comm'])));
    header("Location: ".HOME."/moduls/loads/comm.php?folder=".$folder['id']."&folderc=".$folderc['id']."&file=".$file['id']."&".(int)$_GET['p']);
}
                        
if (empty($comm)) {
    echo DIV_BLOCK . ''. $lng['Sharhlar yo`q'] .'' . CLOSE_DIV;
} else {
    $n = new Navigator($comm,$config['write']['loads_comm'],'folder='.$folder['id'].'&folderc='.$folderc['id'].'&file='.$file['id']); 
    $sql = DB::$dbs->query("SELECT * FROM ".LOADS_COMM." WHERE `file_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, ".$config['write']['loads_comm']."", array($file['id']));
    
    while($comm = $sql -> fetch()) {
	
	echo '<div class="white">';
echo '<table cellspacing="0" cellpadding="0" style="margin-bottom:5px;" width="100%" ><tr>';
echo '<td class="grey" style="width:5%;border-radius: 6px 0 0 6px;"><center>';
echo '' . avatar($comm['user_id'],40,40) . '';
echo '</center></td>';

echo '<td class="grey" style="width:95%;border-radius:  0 6px 6px 0;">';
echo ''. userLink($comm['user_id']) . ' 
  <span  style="float:right;"> '. ($comm['user_id'] != $user['user_id'] ? '
		<a href="?otv='.$comm['user_id'].'"> '.icon('sharh.png').'</a> ' : NULL) . '  ' . (privilegy('zc_moder') ? ' <a href="'.HOME.'/moduls/loads/comm.php?folder='.$folder['id'].'&folderc='.$folderc['id'].'&file='.$file['id'].'&del_comm='.$comm['id'].'">'.icon('minus2.png').'</a> ' : NULL) . '';
        echo '</span><br/><span class="mini">' . vrem($comm['time']) . '</span></td></tr></table>';
        echo '' . text($comm['comm']) . '</div>';
    }
    echo $n->navi();                   
}


 echo DIV_AUT;
if (!empty($_GET['otv'])) {
    $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM ".USERS." WHERE `user_id` = ? ",array(abs(num($_GET['otv']))));
    if (!empty($ank) && $ank['user_id'] != $user['id']) {
        echo ' <b>' . $ank['nick'] . '</b> '. $lng['ga habar'] .' 
		<a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/">[x]</a><br />';
    } else {
        echo '<b>'. $lng['Habar'] .':</b><br />';
    }
}
   

if (!empty($user)) {
echo '<form action="'.(isset($_GET['otv']) ? '?otv='.(int)$_GET['otv'] : NULL).'" enctype="multipart/form-data" method="POST">';
echo '<textarea name="comm" style="width:95%;height:4pc;"></textarea><br />';
echo '<input type="submit" value="'. $lng['Sharh kiritish'] .'" />';
bbsmile();  
echo '</form>';
}
echo CLOSE_DIV;




if ($folder['type'] == 1) {
    if (privilegy('zc')) {
echo DIV_AUT;
        echo '<a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/'.$file['id'].'/edit/delete/">'. $lng['Faylni o`chirish'] .'</a>';
echo CLOSE_DIV;
    }
}   
 
echo '<div class="lines">';
echo '- <a href="'.HOME.'/loads/'.$folder['id'].'/'.$folderc['id'].'/">'. $lng['Orqaga qaytish'] .'</a>
 / <a href="'.HOME.'/loads/">'. $lng['Yuklamalar markazi'] .'</a>'; 
echo '</div>';   
require_once('../../core/stop.php');
?>