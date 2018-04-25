<?php
require_once('../../core/start.php');
head(''. $lng['Tanishuv'] .'');


function get_user($page) {
global $lng;
echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>
<td class="lines" style="vertical-align: top;" width="10%">';
echo '' . avatar($page['user_id'],60,60) . '';
echo '</td>';

echo '<td class="lines" style="vertical-align: top;" width="90%" >';
    echo '<a href="'.HOME.'/id'.$page['user_id'].'">' . userLink($page['user_id']) . '  </a> ' . ($page['age'] ? $page['age'] : ' ') . '';
	
    if ($page['last_time'] > (time() - 2000)) {
        if ($page['is_mobile'] == 1) {
            echo ' '.icon('pda.png').'<br/>';
        } elseif ($page['is_mobile'] == 2) {
            echo ' '.icon('pc.png').'<br/>';
        }
    } else {
        echo '<br/><span class="mini">' . vrem($page['last_time']) . '</span><br/>';
    }
	
	
    $city = DB::$dbs->queryFetch("SELECT name FROM ".CITY." WHERE `city_id` = ?",array($page['city']));
    if ($city) {
        echo $city['name'], ' ';
    } else {
        echo '';
    }
        
        echo '<div style="font-size:12px;">';
	    if (empty($page['poznakom']) && empty($page['goal']) && empty($page['family_status']) && empty($page['childtren']) && empty($page['orientation'])) {
        echo '';
    } else {
        echo (!empty($page['poznakom']) ? '<b>'. $lng['Tanishaman'] .':</b> ' . poznakom($page['user_id']) . ' ' : NULL);
        echo (!empty($page['goal']) ? '<br/><b>'. $lng['Tanishishdan maqsadi'] .':</b> ' . goal($page['user_id']) . ' ' : NULL);
        echo (!empty($page['family_status']) ? '<br/><b>'. $lng['Oilaviy ahvoli'] .':</b> ' . family_status($page['user_id']) . ' ' : NULL);
        echo (!empty($page['children']) ? '<br/><b>'. $lng['Bolalar bormi'] .':</b> ' . children($page['user_id']) . ' ' : NULL);      
    }
        echo '</div>';

echo '</td></tr></table>';

    //// Status	
	$status = DB::$dbs->queryFetch("SELECT * FROM ".STATUS." WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 1",array($page['user_id']));
    if ($page['user_id'] != $user['user_id']) {
        if (!empty($status)) {
		echo '<div class="sts" style="font-size:12px;">';
            echo '' . SubstrMaus(text($status['status']), 100) . '</div>';
        }
    } else {
		echo '' . (!empty($status['status']) ? '<div class="sts" style="font-size:12px;">'.SubstrMaus(text($status['status']), 100).' </div>' : '') . ' ';
    }
	//// Status	 

    
    
}
switch ($select) {
    
    default:
        $online_women = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `gender` = ? && `last_time` > ?", array(0, (time() - 2000)));
        $online_man = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `gender` = ? && `last_time` > ?", array(1, (time() - 2000)));
        $online_all = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `last_time` > ?", array((time() - 2000)));
        ?>
<table cellspacing="0" cellpadding="0" width="100%" ><tr>
<td width="50%"><center>
<div class="grey">
<b><? echo ''. $lng['Hozir saytda'] .''; ?>:</b><br/>
<a href="<?= HOME ?>/tanishuv/search/women/online/"><?= $online_women?>  <?echo ''.icon('girl.png').''; ?></a> 
<a href="<?= HOME ?>/tanishuv/search/man/online/"><?= $online_man?>  <?echo ''.icon('boy.png').''; ?></a>
</div> 
</center></td>

<td width="50%" ><center>
        <div class="grey"><b><? echo ''. $lng['Jami saytda'] .''; ?>:</b><br/>
            <a href="<?= HOME ?>/tanishuv/search/all/"><?= $online_all ?> <?echo ''.icon('users.png').''; ?></a>
        </div>
</center></td></tr></table>

<div class="lines">
        <form action="<?= HOME ?>/tanishuv/search/" method="POST"><? echo ''. $lng['Izlayapman'] .''; ?>:<br/>
                <select name="gender" style="width:100%;">
                    <option value="0"><? echo ''. $lng['Ayol'] .''; ?></option>
                    <option value="1"><? echo ''. $lng['Erkak'] .''; ?></option>
                </select></div>
				<div class="lines"><? echo ''. $lng['Yoshi2'] .''; ?>: <? echo ''. $lng['dan'] .''; ?> / <? echo ''. $lng['gacha'] .''; ?> <br/>
				<table cellspacing="0" cellpadding="0" width="100%" ><tr>
                <td style="padding:5px;" width="50%">
                <input type="text" name="age1" value="18" style="width: 90%;"/>
				</td><td style="padding:5px;" width="50%" >
                <input type="text" name="age2" value="40" style="width: 90%;"/>
				</td></tr></table></div>
<div class="lines"><? echo ''. $lng['Mamlakat'] .''; ?>:<br/>
                <select name="country" style="width:100%;">
                    <option value="3159">Россия</option>
                    <option value="9787">Узбекистан</option>
                    <option value="9908">Украина</option>
                    <?php
                    $sql = DB::$dbs->query("SELECT * FROM ".COUNTRY." ");
                    while($country = $sql -> fetch()) {
                        echo '<option value="'.$country['country_id'].'">'.$country['name'].'</option>';
                    }
                    ?>
                </select>
</div><div class="lines"><? echo ''. $lng['Shaharlar'] .''; ?> (<? echo ''. $lng['3 dan ko`p bo`lmasin'] .''; ?>):<br/>
<input type="text" name="city" placeholder="Москва Newyork Toshkent" style="width:95%;"/>
</div><div class="white"><? echo ''. $lng['Foto bilan'] .''; ?>: 
                            <input type="checkbox" name="photo" value="1" checked/> / 
                        <? echo ''. $lng['Hozir saytda'] .''; ?>: 
                            <input type="checkbox" name="online" value="1"/></div>
							<div class="white">
            <input type="submit" name="search" value="<? echo ''. $lng['Izlash'] .''; ?>" style="display: block;"/>
        </form>
        </div>
		<div class="grey"><? echo ''. $lng['Yangilar tanishishni istamoqda'] .''; ?></div>
            <table>
            <?php
            $sql = DB::$dbs->query("SELECT * FROM ".USERS." ORDER BY user_id DESC LIMIT 5");
            while($page = $sql -> fetch()) {
                get_user($page);
            }
            ?>
            </table>
        </div>
            <div class="touch"><a href="<?= HOME ?>/tanishuv/search/women/"><? echo ''. $lng['Barcha qizlarni ko`rish'] .''; ?></a></div>
            <div class="touch"><a href="<?= HOME ?>/tanishuv/search/man/"><? echo ''. $lng['Barcha bollarni ko`rish'] .''; ?></a></div>
        </div>
        <?php
        break;
        
    case 'search':
        if (!empty($_POST)) {
            $gender = abs((int)$_POST['gender']);
            $age1 = abs((int)$_POST['age1']);
            $age2 = abs((int)$_POST['age2']);
            $country = abs((int)$_POST['country']);
            $city = html($_POST['city']);
            $online = abs((int)$_POST['online']);
            $photo = abs((int)$_POST['photo']);
            
            if ($city) {
                $data_city = explode(" ", $city);
                if (count($data_city) == 1) {
                    $city_array = DB::$dbs->queryFetch("SELECT city_id FROM ".CITY." WHERE `name` = ?",array(html($city)));
                    if ($city_array) {
                        $city_sql = " && city = '".$city_array['city_id']."'";
                    }
                } else if (count($data_city) == 2) {
                    $city_array = DB::$dbs->queryFetch("SELECT city_id FROM ".CITY." WHERE `name` = ?",array(html($data_city[0])));
                    $city_array2 = DB::$dbs->queryFetch("SELECT city_id FROM ".CITY." WHERE `name` = ?",array(html($data_city[1])));
                    
                    if ($city_array && $city_array2) {
                        $city_sql = " && (city = '".$city_array['city_id']."' || city = '".$city_array2['city_id']."')";
                    } else if (empty($city_array) && !empty($city_array2)) {
                        $city_sql = " && city = '".$city_array2['city_id']."'";
                    } else if (!empty($city_array) && empty($city_array2)) {
                        $city_sql = " && city = '".$city_array['city_id']."'";
                    }
                } else if (count($data_city) == 3) {
                    $city_array = DB::$dbs->queryFetch("SELECT city_id FROM ".CITY." WHERE `name` = ?",array(html($data_city[0])));
                    $city_array2 = DB::$dbs->queryFetch("SELECT city_id FROM ".CITY." WHERE `name` = ?",array(html($data_city[1])));
                    $city_array3 = DB::$dbs->queryFetch("SELECT city_id FROM ".CITY." WHERE `name` = ?",array(html($data_city[2])));
                    
                    if ($city_array && $city_array2 && $city_array3) {
                        $city_sql = " && (city = '".$city_array['city_id']."' || city = '".$city_array2['city_id']."' || city = '".$city_array3['city_id']."')";
                    } else if (empty($city_array) && empty($city_array2) && !empty($city_array3)) {
                       $city_sql = " && city = '".$city_array3['city_id']."'";
                    } else if (empty($city_array) && !empty($city_array2) && !empty($city_array3)) {
                        $city_sql = " && (city = '".$city_array2['city_id']."' || city = '".$city_array3['city_id']."')";
                    } else if (!empty($city_array) && !empty($city_array2) && empty($city_array3)) {
                        $city_sql = " && (city = '".$city_array['city_id']."' || city = '".$city_array2['city_id']."')";
                    }
                    
                    
                }
            }

            $sql = "SELECT * FROM ".USERS." WHERE gender = '".$gender."'";
            
            if (!empty($age1) && !empty($age2)) {
                //`age` >= 30 && `age` <= 39
                $sql .= " && (age >= '".$age1."' && age <= '".$age2."')";
            }
            if (!empty($country)) {
                 $sql .= " && country = '".$country."'";
            }
            if (!empty($city_sql)) {
                $sql .= $city_sql;
            }
            if (!empty($photo)) {
                $sql .= " && photo LIKE '%.jpg'";
            }
            if (!empty($online)) {
                $sql .= " && last_time > '" . (time() - 2000) . "'";
            }
        } else if ($_GET['act'] == 'women') {
            
            if ($_GET['online'] == 1) {
                $sql = "SELECT * FROM ".USERS." WHERE gender = '0' && last_time > '" . (time() - 2000)."'";
            } else {
                $sql = "SELECT * FROM ".USERS." WHERE gender = '0'";
            }
            
        } else if ($_GET['act'] == 'man') {
            
            if ($_GET['online'] == 1) {
                $sql = "SELECT * FROM ".USERS." WHERE gender = '1' && last_time > '" . (time() - 2000)."'";
            } else {
                $sql = "SELECT * FROM ".USERS." WHERE gender = '1'";
            }
            
        } else {
            $sql = "SELECT * FROM ".USERS;
        }
        
	    /* VIP */
    $vip = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM ".USERS." WHERE `vip` > ? ", array(time()));
    if (!empty($vip)) {
        
        $ank = DB::$dbs->queryFetch("SELECT `photo`, `user_id` FROM ".USERS." WHERE `vip` > ? AND `last_time` > ? ORDER BY RAND() DESC LIMIT 1", array(time(), (time()-2000)));
        if (!empty($ank)) {
		echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>
<td class="lines" width="5%">';
echo '' . avatar($ank['user_id'],50,50) . '';
echo '</td>';

echo '<td class="lines" width="95%" >';
echo user_choice($ank['user_id'], 'link') . '<br /><a href="'.HOME.'/shop/vip/"><span class="mini">+ '. $lng['Ulanish'] .'</span></a>';
echo '</td></tr></table>';
        }
        
    }
        echo '<div class="lines">'. $lng['Izlash natijalari'] .':</div><table>';
	
        $sql = DB::$dbs->query($sql);
        $i = 0;
        while($page = $sql -> fetch()) {
            get_user($page);
            $i++;
        }
        if (empty($i)) {
            echo '<div class="error">'. $lng['Topilmadi'] .'</div>';
        }
        echo '</table>';
        break;
}

require_once('../../core/stop.php');
?>