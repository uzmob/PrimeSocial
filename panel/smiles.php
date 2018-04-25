<?

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

head(''. $lng['Smayllar'] .''); 

if (privilegy('smiles') == FALSE) {
    header("Location: ".HOME."/panel");
    exit();
}

if (privilegy('smiles')) {
    switch ($select) {
            
        default:
        if ($_POST) {
            $name = html($_POST['name']);
            
            if (empty($name)) {
                echo DIV_ERROR . ''. $lng['Bo`lim nomini kiriting'] .'' . CLOSE_DIV;
            } else {
                DB::$dbs->query("INSERT INTO ".SMILES_CAT." (`name`) VALUES (?)", array($name));
                header("Location: ".HOME."/panel/smiles/"); 
            }
        }
        
        if (!empty($_GET['del'])) {
            DB::$dbs->query("DELETE FROM ".SMILES_CAT." WHERE `id` = ? ", array(num($_GET['del'])));
            
            $sql = DB::$dbs->query("SELECT `url` FROM ".SMILES." WHERE `cat_id` = ? ", array(num($_GET['del'])));
            while($smile = $sql -> fetch()) {
                unlink('../files/smiles/'.$smile['url']);
            }   
            DB::$dbs->query("DELETE FROM ".SMILES." WHERE `cat_id` = ? ", array(num($_GET['del'])));            
            header("Location: ".HOME."/panel/smiles/");           
        }
        
        if (!empty($_GET['edit'])) {
            if (!empty($_POST['edit1'])) {
                $name2 = html($_POST['name2']);
                
                if (empty($name2)) {
                    echo DIV_ERROR . ''. $lng['Bo`lim nomini kiriting'] .'' . CLOSE_DIV;
                } else {
                    DB::$dbs->query("UPDATE ".SMILES_CAT." SET `name` = ? WHERE `id` = ?", array($name2, num($_GET['edit'])));
                    header("Location: ".HOME."/panel/smiles/"); 
                }
            }
            
            $c = DB::$dbs->queryFetch("SELECT * FROM ".SMILES_CAT." WHERE `id` = ? ",array(num($_GET['edit'])));
            echo DIV_AUT;
            echo '<form action="#" method="POST">';
            echo ''. $lng['Nomini o`zgartirish'] .':<br /><input type="text" value="'.$c['name'].'" name="name2" />';
            echo '<input type="submit" name="edit1" value="'. $lng['O`zgartirish'] .'" /></form>';
            echo CLOSE_DIV;         
        }
        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".SMILES_CAT."");
        
        if (empty($all)) {
            echo DIV_BLOCK . ''. $lng['Bo`limlar ochilmagan'] .'' . CLOSE_DIV;
        } else {
            $sql = DB::$dbs->query("SELECT * FROM ".SMILES_CAT."");
            while($cat = $sql -> fetch()) {
                $smiles = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".SMILES." WHERE `cat_id` = ?", array($cat['id']));
                echo DIV_LI;
                echo '<a href="'.HOME.'/panel/smiles/'.$cat['id'].'/">'.$cat['name'].'</a> ['.$smiles.'] 
				<a href="?edit='.$cat['id'].'">['. $lng['O`zg'] .'.]</a> 
				<a href="?del='.$cat['id'].'">['. $lng['O`chr'] .'.]</a>';
                echo CLOSE_DIV;
            }            
        }
        
        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo '<input type="text" name="name" /><br />';
        echo '<input type="submit" value="'. $lng['Yangi katalog'] .'" /></form>';
        echo CLOSE_DIV;  
        break;
        
        case 'cat':
        $id = abs(num($_GET['id']));
        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".SMILES." WHERE `cat_id` = ? ", array($id));
        if (!empty($_POST['smile']) && !empty($_FILES['file'])) {
            $smile = html($_POST['smile']); # Fayl nomi
            $name = $_FILES['file']['name']; # Fayl nomi
            $ext = strtolower(strrchr($name, '.')); # Fayl formati
            $par = getimagesize($_FILES['file']['tmp_name']); # Rasm shakli
            $size = $_FILES['file']['size']; # Fayl hajmi
            $time = time();
            $photo = $time.$ext;
            $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar
            
            if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".SMILES." WHERE `name` = ? ", array($smile))) {
                $err .= ''. $lng['Bunday smayl bor'] .'<br />';
            }
            
            if ($par[0] > 100 || $par[1] > 100) {
                $err .= ''. $lng['Smayl shakli belgilangan miqdordan ortmoqda'] .'. [Max. 100х100]<br />';
            }
            
            if ($size > (1024 * 100)) {
                $err .= ''. $lng['Foto hajmi belgilangan miqdordan ortmoqda'] .'. [Max. 100kb]<br />';
            }
            
            if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $pictures)) {
                $err .= ''. $lng['Fayl formati noto`g`ri'] .'.<br />';
            }
            
            if (empty($err)) {
                copy($_FILES['file']['tmp_name'], '../files/smiles/'.$time.$ext);

                DB::$dbs->query("INSERT INTO ".SMILES." (`cat_id`, `url`, `name`) VALUES (?,?,?)", array($id, $photo, $smile));
                header("Location: ".HOME."/panel/smiles/".$id."/");
            } else {
                echo DIV_ERROR . $err . CLOSE_DIV;
            }            
        }
        
        if (!empty($_GET['del'])) {
            $s = DB::$dbs->queryFetch("SELECT `url`, `cat_id` FROM ".SMILES." WHERE `id` = ? ",array(num($_GET['del'])));
            $c = $s['cat_id'];
            unlink('../files/smiles/'.$s['url']);
            DB::$dbs->query("DELETE FROM ".SMILES." WHERE `id` = ? ", array(num($_GET['del'])));
            header("Location: ".HOME."/panel/smiles/".$c."/");           
        }
        
        if (!empty($_GET['edit'])) {
            if (!empty($_POST['edit1'])) {
                $name2 = html($_POST['name2']);
                
                if (empty($name2)) {
                    echo DIV_ERROR . ''. $lng['Smayl nomini kiriting'] .'' . CLOSE_DIV;
                } else {
                    DB::$dbs->query("UPDATE ".SMILES." SET `name` = ? WHERE `id` = ?", array($name2, num($_GET['edit'])));
                    header("Location: ".HOME."/panel/smiles/".$id."/"); 
                }
            }
            
            $s = DB::$dbs->queryFetch("SELECT * FROM ".SMILES." WHERE `id` = ? ",array(num($_GET['edit'])));
            echo DIV_AUT;
            echo '<form action="#" method="POST">';
            echo ''. $lng['Nomini o`zgartirish'] .':<br /><input type="text" value="'.$s['name'].'" name="name2" />';
            echo '<input type="submit" name="edit1" value="'. $lng['O`zgartirish'] .'" /></form>';
            echo CLOSE_DIV;         
        }
        
        if ($all == 0) {
            echo DIV_AUT . ''. $lng['Smayllar yo`q'] .'' . CLOSE_DIV;
        } else {
            $n = new Navigator($all,10,'id='.$id.'&select=cat'); 
            $sql = DB::$dbs->query("SELECT * FROM ".SMILES." WHERE `cat_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, 10", array($id));
            while($smile = $sql -> fetch()) {
                echo DIV_LI;
                echo '<img src="'.HOME.'/files/smiles/'.$smile['url'].'" /> ' . $smile['name'] . ' 
				<a href="?edit='.$smile['id'].'">['. $lng['O`zg'] .'.]</a> <a href="?del='.$smile['id'].'">['. $lng['O`chr'] .'.]</a>';
                echo CLOSE_DIV;
            }
            echo $n->navi();         
        }
        echo DIV_AUT;
        echo '<form action="?" enctype="multipart/form-data" method="POST">';
        echo '<b>'. $lng['Yangi smayl'] .':</b> [max. 100kb; 100x100px; jpg, gif, png]<br />
		<input type="file" name="file"/><br />';
        echo '<b>'. $lng['Nomi'] .':</b><br /><input type="text" name="smile"><br />';
        echo '<input type="submit" value="'. $lng['Kiritish'] .'"/>';
        echo '</form>';
        echo CLOSE_DIV;      
        break;
    
    }
} else {
    echo DIV_BLOCK . ''. $lng['Kirishda xatolik'] .'' . CLOSE_DIV;
}
echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>