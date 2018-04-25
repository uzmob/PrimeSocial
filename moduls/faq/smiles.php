<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../../core/start.php');

head(''. $lng['Smayllar'] .'');

switch ($select) {
            
    default:
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".SMILES_CAT."");
        
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Bo`limlar ochilmagan'] .'' . CLOSE_DIV;
    } else {
        $sql = DB::$dbs->query("SELECT * FROM ".SMILES_CAT."");
        while($cat = $sql -> fetch()) {
            $smiles = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".SMILES." WHERE `cat_id` = ?", array($cat['id']));
            echo DIV_LI;
            echo ''.icon('smile.png').' <a href="'.HOME.'/smiles/'.$cat['id'].'/">'.$cat['name'].'</a> <span class="count">'.$smiles.'</span>';
            echo CLOSE_DIV;
        }            
    }  
    break;
        
    case 'cat':
    $id = abs(num($_GET['id']));
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".SMILES." WHERE `cat_id` = ? ", array($id));

    if ($all == 0) {
        echo DIV_AUT . ''. $lng['Smayllar yo`q'] .'' . CLOSE_DIV;
    } else {
        $n = new Navigator($all,10,'id='.$id.'&select=cat'); 
        $sql = DB::$dbs->query("SELECT * FROM ".SMILES." WHERE `cat_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, 10", array($id));
        while($smile = $sql -> fetch()) {
            echo DIV_LI;
            echo '<img src="'.HOME.'/files/smiles/'.$smile['url'].'"/> ' . $smile['name'];
            echo CLOSE_DIV;
        }
        echo $n->navi();         
    }
    break;
}
require_once('../../core/stop.php');
?>