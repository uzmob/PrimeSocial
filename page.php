<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */


require_once('core/start.php');

check_auth();

if (empty($_GET['id'])) {
    $page = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " WHERE `user_id` = ?", array(num($_SESSION['user_id'])));
} else {
    $page = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " WHERE `user_id` = ?", array(num($_GET['id'])));
}

if (empty($page)) {
    head(' ' . $lng['Xatolik'] . '!');
    echo DIV_TITLE . '' . $lng['Sahifa mavjud emas'] . '' . CLOSE_DIV;
    echo DIV_ERROR . '' . $lng['Foydalanuvchi topilmadi'] . '' . CLOSE_DIV;
    require_once('core/stop.php');
    exit();
}


if ($page['user_id'] != $user['user_id']) {
    $result = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GUESTS . " WHERE `user_id` = ? && `guest_id` = ?", array($page['user_id'], $user['user_id']));
    if ($result) {
        // Update
        DB::$dbs->query("UPDATE " . GUESTS . " SET `date` = ? WHERE `user_id` = ? && `guest_id` = ? ", array(time(), $page['user_id'], $user['user_id']));
    } else {
        // insert
        DB::$dbs->query("INSERT INTO " . GUESTS . " (`user_id`, `guest_id`, `date`) VALUES (?, ?, ?)", array($page['user_id'], $user['user_id'], time()));
    }
}


switch ($select) {

    default:
        head('' . $page['nick']);
        require_once(inc . 'style/themes/' . $styler . '/pages/userpage.php');
        break;

    case 'soo':
        head(' ' . $lng['Guruhga taklif'] . '');
        if ($user['user_id'] != $page['user_id'] && DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GROUPS . " WHERE `user_id` = ?", array($user['user_id'])) == TRUE) {
            $group = DB::$dbs->queryFetch("SELECT * FROM " . GROUPS . " WHERE `user_id` = ? LIMIT 1", array($user['user_id']));
            if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GROUPS_PEOPLES . " WHERE `group_id` = ? && `user_id` = ?", array($group['id'], $page['user_id'])) == FALSE) {
                if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GROUPS_APPS . " WHERE `group_id` = ? && `user_id` = ? ", array($group['id'], $user['user_id'])) == TRUE) {
                    echo DIV_BLOCK . '' . $lng['Siz ushbu foydalanuvchiga taklif jo`natgansiz'] . '' . CLOSE_DIV;
                } else {
                    if (!empty($_POST['send'])) {
                        /* Habarni shakllantiramiz */
                        $msg = '<a href="' . HOME . '/id' . $user['user_id'] . '">' . $user['nick'] . '</a> ' . $lng['sizni ushbu guruhga taklif etyapti'] . ': <a href="' . HOME . '/groups/' . $group['id'] . '/"><b>' . $group['name'] . '</b></a> . ' . $lng['Sizning harakatingiz'] . ': <a href="' . HOME . '/groups/app/' . $group['id'] . '/?yes"><b>' . $lng['A`zo bo`lish'] . '</b></a> / <a href="' . HOME . '/groups/app/' . $group['id'] . '/?no"><b>' . $lng['Rad etish'] . '</b></a>';
                        lenta($msg, $page['user_id']);

                        DB::$dbs->query("INSERT INTO " . GROUPS_APPS . " (`group_id`, `user_id`, `friend_id`) VALUES (?, ?, ?)", array($group['id'], $user['user_id'], $page['user_id']));

                        echo DIV_BLOCK . '' . $lng['Taklif muvaffaqiyatli jo`natildi'] . '' . CLOSE_DIV;
                    }

                    echo DIV_BLOCK . '<a href="' . HOME . '/groups/' . $group['id'] . '/"><b>' . $group['name'] . '</b></a> ' . $lng['ga taklif etmoqchisizmi'] . '?<br /><br />'
                        . '<form action="#" method="POST">'
                        . '<input type="submit" name="send" value="' . $lng['Amalni tastiqlash'] . '" />'
                        . '</form>'
                        . CLOSE_DIV;
                }

            }
        } else {
            echo DIV_BLOCK . '' . $lng['Xatolik'] . '!' . CLOSE_DIV;
        }
        break;

    case 'ref':
        head('' . $lng['Referal tizim'] . '');

        $all = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . " WHERE `ref` = ?", array($page['user_id']));
        if (empty($all)) {
            echo DIV_BLOCK . '' . $lng['Ro`yhat bo`sh'] . '' . CLOSE_DIV;
        } else {
            $n = new Navigator($all, 10, '');
            $sql = DB::$dbs->query("SELECT `user_id`, `recording_date` FROM " . USERS . " WHERE `ref` = ? ORDER BY `recording_date` DESC LIMIT {$n->start()}, 10", array($page['user_id']));
            while ($ank = $sql->fetch()) {
                echo DIV_BLOCK . user_choice($ank['user_id'], 'link') . ' ' . vrem($ank['recording_date']) . CLOSE_DIV;
            }
            echo $n->navi();
        }
        break;

    case 'stat':
        head('' . $lng['Foydalanuvchi faolligi statistikasi'] . '');
        echo DIV_LI . '' . $lng['Balans'] . ': $' . (empty($page['balls']) ? '0' : $page['balls']) . CLOSE_DIV;
        echo DIV_LI . '' . $lng['Reyting'] . ': ' . (empty($page['rating']) ? '0' : $page['rating']) . '% ' . CLOSE_DIV;
        echo DIV_LI . '' . $lng['Chatdagi sharhlar'] . ':</b> ' . (empty($page['chat_post']) ? 0 : $page['chat_post']) . CLOSE_DIV;
        echo DIV_LI . '' . $lng['Viktorinadagi javoblar'] . ':</b> ' . (empty($page['victorina']) ? 0 : $page['victorina']) . CLOSE_DIV;
        echo DIV_LI . '' . $lng['Jonli efirdagi sharhlar'] . ':</b> ' . (empty($page['guestbook_post']) ? 0 : $page['guestbook_post']) . CLOSE_DIV;

        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . FORUMS_POST . " WHERE `user_id` = ?", array($page['user_id']));
        echo DIV_LI . '<a href="' . HOME . '/posts/' . $page['user_id'] . '/">' . $lng['Forumdagi sharhlar'] . ':</b></a> ' . $all . CLOSE_DIV;

        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . FORUMS_THEME . " WHERE `user_id` = ?", array($page['user_id']));
        echo DIV_LI . '<a href="' . HOME . '/themes/' . $page['user_id'] . '/">' . $lng['Forumdagi mavzular'] . ':</b></a> ' . $all . CLOSE_DIV;

        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GROUPS_PEOPLES . " WHERE `user_id` = ?", array($page['user_id']));
        echo DIV_LI . '' . $lng['Guruhlarga a`zo'] . ':</b> ' . $all . CLOSE_DIV;

        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . BLOG . " WHERE `user_id` = ?", array($page['user_id']));
        echo DIV_LI . '' . $lng['Kundaliklari'] . ':</b> ' . $all . CLOSE_DIV;
        echo DIV_LI . '' . $lng['Ro`yhatdan o`tgan'] . ':</b> ' . vrem($page['recording_date']) . CLOSE_DIV;
        break;

    case 'posts':
        head('' . $lng['Forumdagi sharhlari'] . '');
        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . FORUM_POST . " WHERE `user_id` = ?", array($page['user_id']));

        if (empty($all)) {
            echo DIV_BLOCK . '' . $lng['Sharhlar yo`q'] . '' . CLOSE_DIV;
        } else {
            $n = new Navigator($all, $config['write']['forum_post'], 'select=posts&id=' . $page['user_id']);
            $sql = DB::$dbs->query("SELECT * FROM " . FORUM_POST . " WHERE `user_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, " . $config['write']['forum_post'] . "", array($user['user_id']));
            while ($post = $sql->fetch()) {
                echo DIV_BLOCK;
                echo '<b>' . userLink($post['user_id']) . '</b> [' . vrem($post['time']) . ']<br />';

                if (!empty($post['ct'])) {
                    $ct = DB::$dbs->queryFetch("SELECT `msg` FROM " . FORUMS_POST . " WHERE `id` = ? ", array($post['ct']));
                    echo DIV_CT . '<small><b>' . $lng['Sitata'] . ':</b></small><br />' . text($ct['msg']) . CLOSE_DIV;
                }

                echo text($post['msg']);

                if (!empty($post['file'])) {

                    $path = '../../files/forum/' . $post['file'];

                    $path_info = pathinfo($path);

                    echo '<br /><br />' . icon('yuklama.png') . ' <a href="' . HOME . '/files/forum/' . $post['file'] . '">
				<b>[' . $lng['Yuklab olish'] . ']</b></a> [' . $path_info['extension'] . ']';
                }

                echo '<br /> - <a href="' . HOME . '/forum/' . $post['forum_id'] . '/' . $post['forumc_id'] . '/' . $post['theme_id'] . '/">' . $lng['Mavzuga o`tish'] . '</a>';
                echo CLOSE_DIV;

            }
            echo $n->navi();
        }
        break;

    case 'themes':
        head('' . $lng['Forumdagi mavzular'] . '');
        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . FORUM_THEME . " WHERE `user_id` = ?", array($page['user_id']));

        if ($all == 0) {
            echo DIV_AUT . '' . $lng['Mavzular ochilmagan'] . '' . CLOSE_DIV;
        } else {
            $n = new Navigator($all, $config['write']['forum_theme'], 'select=themes&id=' . $page['user_id']);
            $sql = DB::$dbs->query("SELECT * FROM " . FORUM_THEME . " WHERE `user_id` = ? ORDER BY `time` DESC LIMIT {$n->start()}, " . $config['write']['forum_theme'] . " ", array($page['user_id']));
            while ($theme = $sql->fetch()) {

                $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . FORUMS_POST . " WHERE `theme_id` = ? ", array($theme['id']));
                $page = ceil(($posts / $config['write']['forum_post']));
                echo DIV_LI . '<a href="' . HOME . '/forum/' . $theme['forum_id'] . '/' . $theme['forumc_id'] . '/' . $theme['id'] . '/">' . $theme['name'] . '</a> [' . $posts . '] <a href="' . HOME . '/forum/' . $theme['forum_id'] . '/' . $theme['forumc_id'] . '/' . $theme['id'] . '/?p=' . $page . '">[>>]</a>' . CLOSE_DIV;
            }
            echo $n->navi();
        }
        break;

    case 'anceta':
        head('' . $lng['Anketa'] . '');

        echo '<div class="grey" style="font-size:12px;">';
        echo '' . $lng['Ro`yhatdan o`tgan'] . ': ' . vrem($page['recording_date']) . '<br/>
' . $lng['ID Raqami'] . ': ' . $page['user_id'] . '<br/>
<span style="color:#64A07A;">' . level($page['level']) . '</span>';

        echo '</div>';


        echo '<div class="white">';
        echo '<span style="float:right;margin-top: -40px;">';
        echo '' . avatar($page['user_id'], 60, 60) . '';
        echo '</span>';
        echo '' . userLink($page['user_id']) . '';
        if ($page['last_time'] > (time() - 2000)) {
            if ($page['is_mobile'] == 1) {
                echo ' ' . icon('pda.png') . '';
            } elseif ($page['is_mobile'] == 2) {
                echo ' ' . icon('pc.png') . '';
            }
        } else {
            echo '<br/><span class="mini">' . vrem($page['last_time']) . '</span>';
        }


        echo '</div>';


        echo DIV_LI . '<b>' . $page['surname'] . ' ' . $page['name'] . '</b>' . CLOSE_DIV;
        echo DIV_LI . '<b>' . $lng['Jins'] . ':</b> ' . gender($page['gender']) . CLOSE_DIV;
        echo(!empty($page['bday']) && !empty($page['bmonth']) && !empty($page['byear']) ? DIV_LI . '<b>' . $lng['Tug`ulgan sana'] . ':</b> ' . birthday($page['bday'], $page['bmonth'], $page['byear']) . CLOSE_DIV : NULL);
        echo(!empty($page['age']) ? DIV_LI . '<b>' . $lng['Yosh'] . ':</b> ' . $page['age'] . CLOSE_DIV : NULL);
        echo DIV_LI . '<b>' . $lng['Shahar'] . ':</b> ' . city($page['city']) . CLOSE_DIV;

        echo '<div class="grey" style="font-size:12px;padding:4px;">';
        echo '' . $lng['Tanishuv uchun'] . '';
        echo '</div>';
        if (empty($page['poznakom']) && empty($page['goal']) && empty($page['family_status']) && empty($page['childtren']) && empty($page['orientation'])) {
            echo DIV_LI . '' . $lng['Anketa to`ldirilmagan'] . '' . CLOSE_DIV;
        } else {
            echo(!empty($page['poznakom']) ? DIV_LI . '<b>' . $lng['Tanishaman'] . ':</b> ' . poznakom($page['user_id']) . CLOSE_DIV : NULL);
            echo(!empty($page['goal']) ? DIV_LI . '<b>' . $lng['Tanishishdan maqsadi'] . ':</b> ' . goal($page['user_id']) . CLOSE_DIV : NULL);
            echo(!empty($page['family_status']) ? DIV_LI . '<b>' . $lng['Oilaviy ahvoli'] . ':</b> ' . family_status($page['user_id']) . CLOSE_DIV : NULL);
            echo(!empty($page['children']) ? DIV_LI . '<b>' . $lng['Bolalar bormi'] . ':</b> ' . children($page['user_id']) . CLOSE_DIV : NULL);
        }

        echo(!empty($page['about']) ? DIV_LI . '<b>' . $lng['O`zi haqida'] . ':</b> ' . $page['about'] . CLOSE_DIV : NULL);

        echo '<div class="grey" style="font-size:12px;padding:4px;">';
        echo '' . $lng['Qiziqishlari'] . '';
        echo '</div>';

        echo(!empty($page['interes']) ? DIV_LI . '<b>' . $lng['Qiziqishlari'] . ':</b> ' . $page['interes'] . CLOSE_DIV : NULL);
        echo(!empty($page['music']) ? DIV_LI . '<b>' . $lng['Sevimli musiqasi'] . ':</b> ' . $page['music'] . CLOSE_DIV : NULL);
        echo(!empty($page['cinema']) ? DIV_LI . '<b>' . $lng['Sevimli filmi'] . ':</b> ' . $page['cinema'] . CLOSE_DIV : NULL);
        echo(!empty($page['books']) ? DIV_LI . '<b>' . $lng['Sevimli kitobi'] . ':</b> ' . $page['books'] . CLOSE_DIV : NULL);
        echo(!empty($page['smok']) ? DIV_LI . '<b>' . $lng['Chekishga munosobati'] . ':</b> ' . smok($page['user_id']) . CLOSE_DIV : NULL);
        echo(!empty($page['alco']) ? DIV_LI . '<b>' . $lng['Alkagolga munosobati'] . ':</b> ' . alco($page['user_id']) . CLOSE_DIV : NULL);

        if ($page['user_id'] == $user['user_id']) {
            echo DIV_LI . '- <a href="' . HOME . '/menu/anceta">' . $lng['Tahrirlash'] . '</a>' . CLOSE_DIV;
        }

        if (privilegy('anceta_update', $page['user_id'])) {
            echo '<div class="white">- <a href="' . HOME . '/panel/user/edit/' . $page['user_id'] . '">' . $lng['O`zgartirish'] . '</a><br/>';
        }
        echo(privilegy('anceta_delete', $page['user_id']) ? '- <a href="' . HOME . '/panel/user/delete/' . $page['user_id'] . '">' . $lng['O`chirish'] . '</a><br/>' : NULL);
        echo(privilegy('anceta_bann', $page['user_id']) ? '- <a href="' . HOME . '/panel/user/bann/' . $page['user_id'] . '">' . $lng['BAN'] . '</a></div>' : NULL);

        echo DIV_TOUCH . '<a href="' . HOME . '/stat/' . $page['user_id'] . '/">' . icon('chart.png') . ' ' . $lng['Statistika'] . '</a>' . CLOSE_DIV;


        break;

    case 'loveanc':
        head('' . $lng['Tanishuv anketasi'] . '');
        if (empty($page['poznakom']) && empty($page['goal']) && empty($page['family_status']) && empty($page['childtren']) && empty($page['orientation'])) {
            echo DIV_LI . '' . $lng['Anketa to`ldirilmagan'] . '' . CLOSE_DIV;
        } else {
            echo(!empty($page['poznakom']) ? DIV_LI . '<b>' . $lng['Tanishaman'] . ':</b> ' . poznakom($page['user_id']) . CLOSE_DIV : NULL);
            echo(!empty($page['goal']) ? DIV_LI . '<b>' . $lng['Tanishishdan maqsadi'] . ':</b> ' . goal($page['user_id']) . CLOSE_DIV : NULL);
            echo(!empty($page['family_status']) ? DIV_LI . '<b>' . $lng['Oilaviy ahvoli'] . ':</b> ' . family_status($page['user_id']) . CLOSE_DIV : NULL);
            echo(!empty($page['children']) ? DIV_LI . '<b>' . $lng['Bolalar bormi'] . ':</b> ' . children($page['user_id']) . CLOSE_DIV : NULL);
        }
        break;

    case 'group':
        head('' . $lng['Guruhlar'] . '');


        $all = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . GROUPS_PEOPLES . " WHERE `user_id` = ?", array($page['user_id']));

        if ($all > 0) {
            $sql = DB::$dbs->query("SELECT * FROM " . GROUPS_PEOPLES . " WHERE `user_id` = ? ", array($page['user_id']));
            while ($post = $sql->fetch()) {
                $group = DB::$dbs->queryFetch("SELECT * FROM " . GROUPS . " WHERE `id` = ? ", array($post['group_id']));
                $peoples = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GROUPS_PEOPLES . " WHERE `group_id` = ? ", array($group['id']));
                echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>
<td class="lines" width="5%">';
                echo '' . (empty($group['logo']) ? '<img src="' . HOME . '/style/img/nogroup.png" style="border-radius:55%;width:45px;height:45px;"/>' : '<img src="' . HOME . '/files/groups/' . $group['logo'] . '"  style="border-radius:55%;width:45px;height:45px;"/>') . '';

                echo '</td>';

                echo '<td class="lines" width="95%" >';
                echo '<a href="' . HOME . '/groups/' . $group['id'] . '/"><b>' . $group['name'] . '</b></a></br> ' . $peoples . ' ' . $lng['ishtrokchilar'] . '<br />';
                echo '</td></tr></table>';
            }
        }
        if ($page['user_id'] != $user['user_id']) {

            if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GROUPS . " WHERE `user_id` = ?", array($user['user_id'])) == TRUE) {
                $group = DB::$dbs->queryFetch("SELECT * FROM " . GROUPS . " WHERE `user_id` = ? LIMIT 1", array($user['user_id']));
                if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GROUPS_PEOPLES . " WHERE `group_id` = ? && `user_id` = ?", array($group['id'], $page['user_id'])) == FALSE) {
                    echo DIV_LI . '- <a href="' . HOME . '/soo/' . $page['user_id'] . '/"><u>' . $lng['Guruhga taklif qilish'] . '</u></a>' . CLOSE_DIV;
                }
            }
        }
        break;
}

require_once('core/stop.php');