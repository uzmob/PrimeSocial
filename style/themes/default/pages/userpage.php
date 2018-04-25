<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */

$bann = DB::$dbs->queryFetch("SELECT * FROM " . BANN . " WHERE `user_id` = ? && `time_bann` > ?", array($page['user_id'], time()));
if ($bann['time_bann'] > time()) {
    echo '<b>' . $lng['Foydalanuvchiga Ban berilgan'] . '!</b><br /><b>' . $lng['Sababi'] . ':</b> ' . text($bann['prich']) . '<br /><br />';
}

echo '' . cover($page['user_id']) . '';
echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>';
echo '<td class="white" style="width:10%;">';
if ($page['user_id'] != $user['user_id']) {
    echo '<span style="float:left;margin-top:15px;">
		<a href="' . HOME . '/present/' . $page['user_id'] . '/"> ' . icon('ugift.png', 32, 32) . ' </a></span>';
}
if (!empty($page['zver'])) {
    echo '<img src="' . HOME . '/moduls/shop/zver/' . $page['zver'] . '" style="margin-top:10px;width:35px;height:35px;"/>';
}
echo '</td>';

echo '<td class="white" style="width:80%;"><center>';
if ($page['photo']) {
    echo '<a href="' . HOME . '/files/photo/' . $page['photo'] . '"> 
		<img src="' . HOME . '/files/photo/' . $page['photo'] . '" style="width:120px;height:120px;border-radius:4px;margin-top:-50%;"/></a>';
} else {
    if (empty($page['gender'])) {
        /* Ayol */
        echo '<img src="' . HOME . '/style/img/ayol.png" style="border: 3px solid #fff;width:120px;height:120px;border-radius:4px;margin-top:-50%;"/>';
    } else {
        /* Erkak */
        echo '<img src="' . HOME . '/style/img/erkak.png" style="border: 3px solid #fff;width:120px;height:120px;border-radius:4px;margin-top:-50%;"/>';
    }
}


echo '<br/><b>' . $page['surname'] . ' ' . $page['name'] . '</b><br/>' . userLink($page['user_id']) . ' ';

if ($page['last_time'] > (time() - 2000)) {
    if ($page['is_mobile'] == 1) {
        echo ' ' . icon('pda.png') . '';
    } elseif ($page['is_mobile'] == 2) {
        echo ' ' . icon('pc.png') . '';
    }
} else {
}


echo '<br/>';

echo '<span style="font-size:11px;color:#4587BE;border: 1px solid #68AEE6;border-radius:4px;padding:3px;">
		' . $lng['Reyting'] . ': ' . (empty($page['rating']) ? '0' : $page['rating']) . '%</span>';

echo ' <span style="font-size:11px;color:#5e9c76;border: 1px solid #5e9c76;border-radius:4px;padding:3px;">
	   ' . (empty($page['balls']) ? '0' : $page['balls']) . '$</span>';


echo(empty($page['photo']) ? ' ' : ' <span style="font-size:11px;border: 1px solid #D39B4A;border-radius:4px;padding:3px;"><a href="' . HOME . '/avatar/like/' . $page['user_id'] . '/" style="color:#B07D37;">Like</a></span>');

echo '<br/><span style="font-size:11px;">' . level($page['level']) . '</span>';


echo '</center></td>';

echo '<td class="white" style="width:10%;">';
if ($page['user_id'] != $user['user_id']) {
    echo '<span style="float:right;margin-top:15px;">
		<a href="' . HOME . '/mail/' . $page['user_id'] . '/"> ' . icon('umail.png', 32, 32) . ' </a></span>';
}
echo '</td>';
echo '</tr></table>';

/* Yangi sovg`alar */

$all = DB::$dbs->querySingle("SELECT COUNT(*) FROM " . PRESENTS_LIST . " WHERE `friend_id` = ?", array($page['user_id']));
if ($all) {
    echo '<div class="white"><center>';
    $sql = DB::$dbs->query("SELECT * FROM " . PRESENTS_LIST . " WHERE `friend_id` = ? ORDER BY `id` DESC LIMIT 5", array($page['user_id']));
    while ($list = $sql->fetch()) {
        $present = DB::$dbs->queryFetch("SELECT * FROM " . PRESENTS . " WHERE `id` = ? LIMIT 1", array($list['present_id']));
        echo '<img src="' . HOME . '/files/presents/' . $present['url'] . '" style="width:32px;height:32px;">';
    }
    echo '</center>' . CLOSE_DIV;
}


/* Shahsiylik */
if ($page['user_id'] != $user['user_id']) {
    if ($page['private_page'] == 1) {
        $sql = DB::$dbs->queryFetch("SELECT `id`, `status`, `id_friend` FROM `friends` WHERE ((`id_user` = ? AND `id_friend` = ?) OR (`id_friend` = ? AND `id_user` = ?)) && status = ? LIMIT 1", array($user['user_id'], $page['user_id'], $user['user_id'], $page['user_id'], 1));
        if (!$sql) {
		
if ($page['user_id'] != $user['user_id']) {
    echo '<div class="white" style="font-size:12px;"><center>';
///echo '- <a href="'.HOME.'/mail/'.$page['user_id'].'/">'. $lng['Habar jo`natish'] .' </a><br/>';  
///echo '- <a href="'.HOME.'/present/'.$page['user_id'].'/"> '. $lng['Sovg`a jo`natish'] .' </a><br/>';  

    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GROUPS . " WHERE `user_id` = ?", array($user['user_id'])) == TRUE) {
        $group = DB::$dbs->queryFetch("SELECT * FROM " . GROUPS . " WHERE `user_id` = ? LIMIT 1", array($user['user_id']));
        if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GROUPS_PEOPLES . " WHERE `group_id` = ? && `user_id` = ?", array($group['id'], $page['user_id'])) == FALSE) {
            echo '- <a href="' . HOME . '/soo/' . $page['user_id'] . '/">' . $lng['Guruhga taklif qilish'] . '</a><br/>';
        }
    }
    /* Do`stlar */
    $sql = DB::$dbs->queryFetch("SELECT `id`, `status`, `id_friend` FROM `friends` WHERE (`id_user` = ? AND `id_friend` = ?) LIMIT 1", array($user['user_id'], $page['user_id']));
    if (empty($sql)) {
        $sql2 = DB::$dbs->queryFetch("SELECT `id`, `status` FROM `friends` WHERE (`id_user` = ? AND `id_friend` = ?) LIMIT 1", array($page['user_id'], $user['user_id']));
        if ($sql2['status'] == 0) {
            if ($sql['id'] || $sql2['id'])
                echo '- <a href="' . HOME . '/friends/add/' . $page['user_id'] . '/">' . $lng['Do`stlik taklifini qabul qilish'] . '</a><br/>';
            else
                echo '- <a href="' . HOME . '/friends/add/' . $page['user_id'] . '/">' . $lng['Do`stlarim orasiga qo`shish'] . '</a><br/>';
        } else {
            if (!empty($sql))
                echo '- <a href="' . HOME . '/friends/add/' . $page['user_id'] . '/">' . $lng['Do`stlarim orasiga qo`shish'] . '</a><br/>';
            else {
                echo '- <a href="' . HOME . '/friends/delete/' . $page['user_id'] . '/?anceta">' . $lng['Do`stlarim orasidan o`chirish'] . '</a><br/>';
            }
        }
    } elseif ($sql['id'] && $sql['status'] == 0) {
        $ank = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " WHERE `user_id` = ?", array($sql['id_friend']));
        echo '-  ' . $lng['Taklif qilingan'] . '';
        echo '<a href="' . HOME . '/friends/delete/' . $ank['user_id'] . '/">[X]</a><br/>';
    } else {
        $ank = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " WHERE `user_id` = ?", array($sql['id_friend']));
        echo '- <a href="' . HOME . '/friends/delete/' . $ank['user_id'] . '/?anceta">' . $lng['Do`stlarim orasidan o`chirish'] . '</a><br/>';
    }
    if (DB::$dbs->querySingle("SELECT COUNT(*) FROM " . BLACKUSERS . " WHERE `user_id` = ? && `black_id` = ?", array($user['user_id'], $page['user_id'])) == FALSE) {
        echo '- <a href="' . HOME . '/blacklist/go/' . $page['user_id'] . '/">' . $lng['Qora ro`yhatga kiritish'] . '</a><br/>';
    } else {
        echo '- <a href="' . HOME . '/blacklist/go/' . $page['user_id'] . '/">' . $lng['Qora ro`yhatdan o`chirish'] . '</a><br/>';
    }

    echo '- <a href="' . HOME . '/dey/go/' . $page['user_id'] . '/">' . $lng['Amal bajarish'] . '</a><br/>';

    echo '</center></div>';
}
            echo '<div class="sts"><center><b>' . $page['nick'] . '</b> ' . $lng['SahifaShahsiyliki'] . ' </center>' . CLOSE_DIV;
            require_once('core/stop.php');
            exit();
        }
    } else if ($page['private_page'] == 2) {
        echo '<div class="sts"><center><b>' . $page['nick'] . '</b> ' . $lng['SahifaShahsiyliki2'] . '</center>' . CLOSE_DIV;
        require_once('core/stop.php');
        exit();
    }
}
/* */

$status = DB::$dbs->queryFetch("SELECT * FROM " . STATUS . " WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 1", array($page['user_id']));
$rating = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . STATUS_RATING . " WHERE `status_id` = ?", array($status['id']));
$comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . STATUS_COMM . " WHERE `status_id` = ?", array($status['id']));
if ($page['user_id'] != $user['user_id']) {
    if (!empty($status)) {
        echo '<div class="white">';
        echo '<div class="white" style="border-radius:4px;margin:10px;">';
        echo SubstrMaus(text($status['status']), 100);
        echo '<span class="count">' . icon('bubl.png') . '
			<a href="' . HOME . '/status/' . $status['id'] . '/comm/" style="color:#b9b9b9;font-size:12px;">' . $comm . '</a>
			' . icon('cls.png') . ' <a href="' . HOME . '/status/' . $status['id'] . '/like/" style="color:#b9b9b9;font-size:12px;">' . $rating . '</a> ';
        $status = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . STATUS . " WHERE `user_id` = ? ", array($page['user_id']));
        echo ' <a href="' . HOME . '/statush/' . $page['user_id'] . '/" style="color:#b9b9b9;font-size:12x;">' . icon('notes.png') . ' </a>
	' . $status . '</span></div>';
        echo '</div>';

    }
} else {
    echo '<div class="white">';
    echo '<div class="white" style="border-radius:4px;margin:10px;"><a href="' . HOME . '/menu/status?edit&ank">
		' . (!empty($status['status']) ? SubstrMaus(text($status['status']), 80) : '' . $lng['Statusni o`zgartirish'] . '') . '</a>';
    if (!empty($status)) {
        echo '<span class="count">' . icon('bubl.png') . ' 
		<a href="' . HOME . '/status/' . $status['id'] . '/comm/" style="color:#b9b9b9;font-size:12px;">' . $comm . '</a>
		' . icon('cls.png') . ' <a href="' . HOME . '/status/' . $status['id'] . '/like/" style="color:#b9b9b9;font-size:12px;">' . $rating . '</a>';
        $status = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . STATUS . " WHERE `user_id` = ? ", array($page['user_id']));
        echo ' <a href="' . HOME . '/statush/' . $page['user_id'] . '/" style="color:#b9b9b9;font-size:12px;">' . icon('notes.png') . ' ' . $status . '</a></span></div></div>';
    } else {

    }
}

/* Tanishuv anketasi */
if (empty($page['poznakom']) && empty($page['goal']) && empty($page['family_status']) && empty($page['childtren']) && empty($page['orientation'])) {
    echo ' ';
} else {
    echo '<div class="white">';
    echo '<div class="grey" style="margin-left: 10px;margin-right: 10px;border-radius:4px 4px 0 0;"> &nbsp; ' . $lng['Tanishuv anketasi'] . '</div>';
    echo '<div class="white" style="box-shadow: 0 8px 10px rgba(162,162,162,0.25), 0 2px 4px rgba(162,162,162,0.22);margin-left: 10px;margin-right: 10px;">';
    echo(!empty($page['poznakom']) ? DIV_LI . '<b>' . $lng['Tanishaman'] . ':</b> ' . poznakom($page['user_id']) . CLOSE_DIV : NULL);
    echo(!empty($page['goal']) ? DIV_LI . '<b>' . $lng['Tanishishdan maqsadi'] . ':</b> ' . goal($page['user_id']) . CLOSE_DIV : NULL);
    echo(!empty($page['family_status']) ? DIV_LI . '<b>' . $lng['Oilaviy ahvoli'] . ':</b> ' . family_status($page['user_id']) . CLOSE_DIV : NULL);
    echo(!empty($page['children']) ? DIV_LI . '<b>' . $lng['Bolalar bormi'] . ':</b> ' . children($page['user_id']) . CLOSE_DIV : NULL);
    echo '</div></div>';
}
/* Tanishuv anketasi */


/* Fotoalbomdagi so'ngi suratlar */
$albums = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . ALBUMS . " WHERE `user_id` = ?", array($page['user_id']));
$photos = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . ALBUMS_PHOTOS . " WHERE `user_id` = ?", array($page['user_id']));

$count_photos = DB::$dbs->querySingle("SELECT COUNT(*) FROM " . ALBUMS_PHOTOS . " WHERE `user_id` = ?", array($page['user_id']));
if ($count_photos) {
    echo '<div class="white">';
    echo '<a href="' . HOME . '/album/user/' . $page['user_id'] . '/"><div class="grey" style="margin-left: 10px;margin-right: 10px;border-radius:4px 4px 0 0;"> &nbsp; ' . $lng['Fotosuratlar'] . ' <span class="count"> ' . $albums . '/' . $photos . '</span></div></a>';
    echo '<div class="white" style="box-shadow: 0 8px 10px rgba(162,162,162,0.25), 0 2px 4px rgba(162,162,162,0.22);margin-left: 10px;margin-right: 10px;">';

    echo '<div class = "white" >';
    $sql = DB::$dbs->query("SELECT * FROM " . ALBUMS_PHOTOS . " WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 5", array($page['user_id']));
    while ($photo = $sql->fetch()) {
        echo '<a href="' . HOME . '/files/album/' . $photo['url'] . '"><img src="' . HOME . '/files/album/' . $photo['url'] . '" alt="" style="width:18%;height:60px;" /></a> ';
    }
    echo '</div></div></div>';
}

/* Fotoalbomdagi so'ngi suratlar */

echo '<div class="white">';
echo '<div class="grey" style="margin-left: 10px;margin-right: 10px;border-radius:4px 4px 0 0;"> &nbsp; ' . $lng['ID Raqami'] . ': ' . $page['user_id'] . '</div>';
echo '<div class="white" style="box-shadow: 0 8px 10px rgba(162,162,162,0.25), 0 2px 4px rgba(162,162,162,0.22);margin-left: 10px;margin-right: 10px;">';

echo DIV_TOUCH . '<a href="' . HOME . '/anceta/' . $page['user_id'] . '/">' . icon('a.png') . ' ' . $lng['Anketa'] . '</a>' . CLOSE_DIV;

if ($page['user_id'] == $user['user_id']) {
    $mehmon = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GUESTS . " WHERE `user_id` = ? AND `status` = ? ", array($user['user_id'], 1));
    echo DIV_TOUCH . '<a href="' . HOME . '/menu/guests">' . icon('kim.png') . '  ' . $lng['Mehmonlar'] . ' 
	<span class="count"> ' . ($mehmon > 0 ? ' <b>+' . $mehmon . '</b>' : NULL) . '</span></a>' . CLOSE_DIV;
}
$all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . FRIENDS . " WHERE (`id_user` = ? OR `id_friend` = ?) AND (`status` = ?) ", array($user['user_id'], $user['user_id'], 1));
echo DIV_TOUCH . '<a href="' . HOME . '/friends/">' . icon('users.png') . ' ' . $lng['Do`stlar'] . ' <span class="count"> ' . $all . '</span></a>' . CLOSE_DIV;


$guest = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GUEST . " WHERE `user_id` = ? ", array($page['user_id']));
echo DIV_TOUCH . '<a href="' . HOME . '/guest/' . $page['user_id'] . '/">' . icon('chat2.png') . ' ' . $lng['Mehmonxona'] . ' <span class="count"> ' . $guest . '</span></a>' . CLOSE_DIV;

$files = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . FILES . " WHERE `user_id` = ? ", array($page['user_id']));
echo DIV_TOUCH . '<a href="' . HOME . '/files/' . $page['user_id'] . '/">' . icon('yuklama.png') . ' ' . $lng['Shahsiy fayllar'] . ' <span class="count">' . $files . '</span></a>' . CLOSE_DIV;

$albums = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . ALBUMS . " WHERE `user_id` = ?", array($page['user_id']));
$photos = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . ALBUMS_PHOTOS . " WHERE `user_id` = ?", array($page['user_id']));
echo DIV_TOUCH . '<a href="' . HOME . '/album/user/' . $page['user_id'] . '/">' . icon('foto.png') . ' ' . $lng['Fotoalbom'] . ' <span class="count"> ' . $albums . '/' . $photos . '</span></a>' . CLOSE_DIV;

$group = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . GROUPS_PEOPLES . " WHERE `user_id` = ?", array($page['user_id']));
echo DIV_TOUCH . '<a href="' . HOME . '/group/' . $page['user_id'] . '/">' . icon('guruh.png') . ' ' . $lng['Guruhlar'] . ' <span class="count">' . $group . '</span></a>' . CLOSE_DIV;

$all = DB::$dbs->querySingle("SELECT COUNT(*) FROM " . PRESENTS_LIST . " WHERE `friend_id` = ?", array($page['user_id']));
echo DIV_TOUCH . '<a href="' . HOME . '/present/list/' . $page['user_id'] . '/">' . icon('sovga.png') . ' ' . $lng['Sovg`alar'] . ' <span class="count">' . $all . '</span></a>' . CLOSE_DIV;

if ($page['user_id'] == $user['user_id']) {
    echo DIV_TOUCH . '<a href="' . HOME . '/menu">' . icon('icons.png') . ' ' . $lng['Kabinet'] . '</a>' . CLOSE_DIV;
}

echo '</div></div>';


if ($page['user_id'] != $user['user_id']) {
    echo '<div class="white" style="font-size:12px;">';
///echo '- <a href="'.HOME.'/mail/'.$page['user_id'].'/">'. $lng['Habar jo`natish'] .' </a><br/>';  
///echo '- <a href="'.HOME.'/present/'.$page['user_id'].'/"> '. $lng['Sovg`a jo`natish'] .' </a><br/>';  

    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GROUPS . " WHERE `user_id` = ?", array($user['user_id'])) == TRUE) {
        $group = DB::$dbs->queryFetch("SELECT * FROM " . GROUPS . " WHERE `user_id` = ? LIMIT 1", array($user['user_id']));
        if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GROUPS_PEOPLES . " WHERE `group_id` = ? && `user_id` = ?", array($group['id'], $page['user_id'])) == FALSE) {
            echo '- <a href="' . HOME . '/soo/' . $page['user_id'] . '/">' . $lng['Guruhga taklif qilish'] . '</a><br/>';
        }
    }
    /* Do`stlar */
    $sql = DB::$dbs->queryFetch("SELECT `id`, `status`, `id_friend` FROM `friends` WHERE (`id_user` = ? AND `id_friend` = ?) LIMIT 1", array($user['user_id'], $page['user_id']));
    if (empty($sql)) {
        $sql2 = DB::$dbs->queryFetch("SELECT `id`, `status` FROM `friends` WHERE (`id_user` = ? AND `id_friend` = ?) LIMIT 1", array($page['user_id'], $user['user_id']));
        if ($sql2['status'] == 0) {
            if ($sql['id'] || $sql2['id'])
                echo '- <a href="' . HOME . '/friends/add/' . $page['user_id'] . '/">' . $lng['Do`stlik taklifini qabul qilish'] . '</a><br/>';
            else
                echo '- <a href="' . HOME . '/friends/add/' . $page['user_id'] . '/">' . $lng['Do`stlarim orasiga qo`shish'] . '</a><br/>';
        } else {
            if (!empty($sql))
                echo '- <a href="' . HOME . '/friends/add/' . $page['user_id'] . '/">' . $lng['Do`stlarim orasiga qo`shish'] . '</a><br/>';
            else {
                echo '- <a href="' . HOME . '/friends/delete/' . $page['user_id'] . '/?anceta">' . $lng['Do`stlarim orasidan o`chirish'] . '</a><br/>';
            }
        }
    } elseif ($sql['id'] && $sql['status'] == 0) {
        $ank = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " WHERE `user_id` = ?", array($sql['id_friend']));
        echo '-  ' . $lng['Taklif qilingan'] . '';
        echo '<a href="' . HOME . '/friends/delete/' . $ank['user_id'] . '/">[X]</a><br/>';
    } else {
        $ank = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " WHERE `user_id` = ?", array($sql['id_friend']));
        echo '- <a href="' . HOME . '/friends/delete/' . $ank['user_id'] . '/?anceta">' . $lng['Do`stlarim orasidan o`chirish'] . '</a><br/>';
    }
    if (DB::$dbs->querySingle("SELECT COUNT(*) FROM " . BLACKUSERS . " WHERE `user_id` = ? && `black_id` = ?", array($user['user_id'], $page['user_id'])) == FALSE) {
        echo '- <a href="' . HOME . '/blacklist/go/' . $page['user_id'] . '/">' . $lng['Qora ro`yhatga kiritish'] . '</a><br/>';
    } else {
        echo '- <a href="' . HOME . '/blacklist/go/' . $page['user_id'] . '/">' . $lng['Qora ro`yhatdan o`chirish'] . '</a><br/>';
    }

    echo '- <a href="' . HOME . '/dey/go/' . $page['user_id'] . '/">' . $lng['Amal bajarish'] . '</a><br/>';

    echo '</div>';
}


?>