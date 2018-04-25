<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */

require_once('../../core/start.php');
check_auth();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = abs(num($_GET['user']));
$guest = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " WHERE `user_id` = ? ", array($id));

if (empty($guest)) {
    head('' . $lng['Devor topilmadi'] . '');
    echo DIV_BLOCK . '' . $lng['Xatolik'] . '!' . CLOSE_DIV;
    exit();
}
head('' . $lng['Devor'] . ': ' . $guest['nick']);


function check($id)
{

    global $user;
    $id = abs((int)$id);
    $post = DB::$dbs->queryFetch("SELECT * FROM " . GUEST . " WHERE `id` = ? ", array($id));

    if (privilegy('guest_moder')) {
        return TRUE;
    }

    if ($post['user_id'] == $user['user_id']) {
        return TRUE;
    }

    if ($post['autor_id'] == $user['user_id']) {
        return TRUE;
    }

    return FALSE;
}

/* Shahsiylik */
if ($guest['user_id'] != $user['user_id']) {
    if ($guest['private_guest'] == 1) {
        $sql = DB::$dbs->queryFetch("SELECT `id`, `status`, `id_friend` FROM `friends` WHERE ((`id_user` = ? AND `id_friend` = ?) OR (`id_friend` = ? AND `id_user` = ?)) && status = ? LIMIT 1", array($user['user_id'], $guest['user_id'], $user['user_id'], $guest['user_id'], 1));
        if (!$sql) {
            echo '<div class="sts"><center><b>' . $guest['nick'] . '</b> ' . $lng['devorini faqat uning do`stlari ko`ra olishadi'] . '</center></div>';
            $array = array();
            nav($array);
            require_once('../../core/stop.php');
            exit();
        }
    } else if ($guest['private_guest'] == 2) {
        echo '<div class="sts"><center><b>' . $guest['nick'] . '</b> ' . $lng['devorini hech kim ko`ra olmaydi'] . '</center></div>';
        $array = array();
        nav($array);
        require_once('../../core/stop.php');
        exit();
    }
}
/* */

switch ($select) {

    default:
        if (!empty($_GET['del']) && check($_GET['del'])) {
            $post = DB::$dbs->queryFetch("SELECT file FROM " . GUEST . " WHERE `id` = ?", array(num($_GET['del'])));
            if ($post['file']) {
                @unlink('../../files/guest/' . $post['file']);
            }
            DB::$dbs->query("DELETE FROM " . GUEST . " WHERE `id` = ?", array(num($_GET['del'])));
            header("Location: " . HOME . "/guest/" . $guest['user_id'] . "/");
        }

        if (!empty($_POST['post_delete']) && (privilegy('guest_moder') || $guest['user_id'] == $user['user_id'])) {
            foreach ($_POST as $name => $value) {
                $post = DB::$dbs->queryFetch("SELECT file FROM " . GUEST . " WHERE `id` = ?", array($name));
                if ($post['file']) {
                    @unlink('../../files/guest/' . $post['file']);
                }
                DB::$dbs->query("DELETE FROM " . GUEST . " WHERE `id` = ?", array($name));
            }
            header("Location: " . HOME . "/guest/" . $guest['user_id'] . "/");
        }

        if ((privilegy('guest_moder') || $guest['user_id'] == $user['user_id']) && !empty($_POST['clean'])) {
            $sql = DB::$dbs->query("SELECT file FROM " . GUEST . " WHERE `user_id` = ?", array($guest['user_id']));
            while ($post = $sql->fetch()) {
                if ($post['file']) {
                    @unlink('../../files/guest/' . $post['file']);
                }
            }
            DB::$dbs->query("DELETE FROM " . GUEST . " WHERE `user_id` = ? ", array($guest['user_id']));
            header("Location: " . HOME . "/guest/" . $guest['user_id'] . "/");
        }

        if (isset($_POST['add']) && $_POST['add']) {

            $msg = html($_POST['msg']);
            $type = html($_POST['type']);
            switch ($type) {

                case 'text':
                    /* Oddiy tekst joylash */
                    if (empty($msg)) {
                        $err = '' . $lng['Bo`sh habar'] . '<br />';
                    }

                    if (!empty($_GET['otv']) && $_GET['otv'] != $user['user_id']) {
                        $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM " . USERS . " WHERE `user_id` = ? ", array(abs(num($_GET['otv']))));
                        if (!empty($ank)) {
                            $msg = '[b]' . $ank['nick'] . '[/b], ' . $msg;
                        }

                        $lenta = '<a href="' . HOME . '/id' . $user['user_id'] . '"><b>' . $user['nick'] . '</b></a> ' . $lng['sizning habaringizga javob berdi'] . ' <a href="/guest/' . $guest['user_id'] . '/"><b>' . $lng['Devorizda'] . ' ' . $guest['nick'] . '</b></a>';
                        lenta($lenta, $ank['user_id']);

                        $var = TRUE;
                    }

                    if (empty($var)) {
                        $lenta = '<a href="' . HOME . '/id' . $user['user_id'] . '"><b>' . $user['nick'] . '</b></a> ' . $lng['devoringizda sharh qoldirdi'] . ' <a href="/guest/' . $guest['user_id'] . '/"><b>' . $lng['Devorizda'] . '</b></a>';
                        lenta($lenta, $guest['user_id']);
                    }

                    if (!empty($err)) {
                        echo DIV_ERROR . $err . CLOSE_DIV;
                    } else {
                        DB::$dbs->query("INSERT INTO " . GUEST . " (`user_id`, `autor_id`, `time`, `msg`) VALUES (?, ?, ?, ?)", array($guest['user_id'], $user['user_id'], time(), $msg));
                        header("Location: " . HOME . "/guest/" . $guest['user_id'] . "/");
                    }
                    break;

                case 'img':
                    // Rasm joylash
                    if (!empty($msg)) {
                        $_SESSION['msg'] = $msg;
                    }
                    if (!empty($_GET['otv'])) {
                        $_SESSION['otv'] = abs((int)$_GET['otv']);
                    }
                    header("Location: " . HOME . "/guest/" . $guest['user_id'] . "/img/");
                    break;

                case 'votes':
                    // So`rovnoma joylash
                    if (!empty($msg)) {
                        $_SESSION['msg'] = $msg;
                    }
                    if (!empty($_GET['otv'])) {
                        $_SESSION['otv'] = abs((int)$_GET['otv']);
                    }
                    header("Location: " . HOME . "/guest/" . $guest['user_id'] . "/votes/");
                    break;

                default:
                    header("Location: " . HOME . "/guest/" . $guest['user_id']);
                    break;

            }

        }

        if (isset($_GET['like'])) {
            echo DIV_BLOCK . '' . $lng['Sharhga muvaffaqiyatli baxo berdingiz'] . '' . CLOSE_DIV;
        }

        if (isset($_GET['vote'])) {
            echo DIV_BLOCK . '' . $lng['Siz muvaffaqiyatli ovoz berdingiz'] . '' . CLOSE_DIV;
        }
        if ($user) {
        echo '<div class="grey">';
        if (!empty($_GET['otv'])) {
            $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM " . USERS . " WHERE `user_id` = ? ", array(abs(num($_GET['otv']))));
            if (!empty($ank) && $ank['user_id'] != $user['id']) {
                echo '' . $lng['ga javob'] . ' <b>' . $ank['nick'] . '</b><br />';
            } else {
                echo '<b>' . $lng['Habar'] . ':</b><br />';
            }
        }
        echo '<form action="' . (isset($_GET['otv']) ? '?otv=' . (int)$_GET['otv'] : NULL) . '" method="POST">';
        echo '<textarea name="msg" style="width:95%;height:5pc;"></textarea><br />
        
        <select name="type" style="width:98%;">
            <option value="text">' . $lng['Tekst'] . '</option>
            <option value="img">' . $lng['Rasm'] . '</option>
            <option value="votes">' . $lng['So`rovnoma'] . '</option>
        </select>
        ';
        echo '<input type="submit" name="add" value="' . $lng['Yozish'] . '"/>';
        bbsmile();
        echo '</form>';
        echo CLOSE_DIV;
}

        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GUEST . " WHERE `user_id` = ? ", array($guest['user_id']));

        if (empty($all)) {
            echo DIV_BLOCK . '' . $lng['Habarlar yo`q'] . '' . CLOSE_DIV;
        } else {
            echo '<form action="#" method="POST">';
            $n = new Navigator($all, $config['write']['guest'], 'user=' . $guest['user_id']);
            $sql = DB::$dbs->query("SELECT * FROM " . GUEST . " WHERE `user_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, " . $config['write']['guest'] . "", array($guest['user_id']));

            while ($post = $sql->fetch()) {
                $ank = DB::$dbs->queryFetch("SELECT `nick` FROM " . USERS . " WHERE `user_id` = ?", array($post['autor_id']));


                echo '<div class="white">';
                echo '<table cellspacing="0" cellpadding="0" style="margin-bottom:5px;" width="100%" ><tr>';
                echo '<td class="grey" style="width:5%;border-radius: 6px 0 0 6px;"><center>';
                echo '' . avatar($post['autor_id'], 40, 40) . '';
                echo '</center></td>';

                echo '<td class="grey" style="width:95%;border-radius:  0 6px 6px 0;">';
                echo(privilegy('guest_moder') || $guest['user_id'] == $user['user_id'] ? '<input type="checkbox" name="' . $post['id'] . '" /> ' : NULL);
                echo ' ' . ($post['autor_id'] != $user['user_id'] ? '<a href="?otv=' . $post['autor_id'] . '"><b>' . $ank['nick'] . '</b></a>' : '' . user_choice($post['autor_id'], 'link') . '') . '
				  ' . (check($post['id']) ? ' <a href="?del=' . $post['id'] . '" style="float:right;">' . icon('minus2.png') . '</a> <br/>
				 <span class="mini">' . vrem($post['time']) . '</span>' : null) . ' </td></tr></table>' . text($post['msg']) . '<br />';

                if ($post['file']) {
                    $file = '../../files/guest/' . $post['file'];
                    $file = filesize($file);
                    $file_size = get_size($file);
                    echo '<b>' . $lng['Rasm biriktirilgan'] . ':</b> [' . $file_size . ']<br />
					<a href="', HOME, '/files/guest/', $post['file'], '">
					<img src="', HOME, '/files/guest/', $post['file'], '" alt="' . $lng['Rasm'] . '" style="height:90px;"/></a><br />';
                }

                if (!empty($post['vote'])) {

                    echo ' <b>' . $post['vote'] . '</b><br />';

                    if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GUEST_VOTES . " WHERE `post_id` = ? && `user_id` = ? ", array($post['id'], $user['user_id'])) == FALSE) {

                        for ($i = 1; $i <= 10; ++$i) {
                            echo(!empty($post['vote_' . $i]) ? '' . icon('votes.png') . ' <a href="' . HOME . '/guest/' . $guest['user_id'] . '/vote/' . $post['id'] . '/' . $i . '/">' . $post['vote_' . $i] . '</a><br />' : NULL);
                        }

                    } else {
                        for ($i = 1; $i <= 10; ++$i) {
                            $votes = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GUEST_VOTES . " WHERE `post_id` = ? && `variant` = ? ", array($post['id'], $i));
                            echo(!empty($post['vote_' . $i]) ? '<b>' . $i . '.</b> ' . $post['vote_' . $i] . ' [' . $votes . ' ' . $lng['kishi'] . ']<br />' : NULL);
                        }
                    }
                }

                $likes = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GUEST_LIKE . " WHERE `post_id` = ?", array($post['id']));
                $comments = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GUEST_COMMENTS . " WHERE `post_id` = ?", array($post['id']));
                echo '<span style="font-size:11px;"><a href="' . HOME . '/guest/' . $guest['user_id'] . '/' . $post['id'] . '/like/?p=' . (int)@$_POST['p'] . '">' . icon('like.png') . ' ' . $likes . '</a></span>
				<span style="float:right;font-size:11px;"><a href="' . HOME . '/guest/' . $guest['user_id'] . '/' . $post['id'] . '/comments/?p=' . (int)@$_POST['p'] . '">' . icon('comm.png') . ' ' . $comments . '</a></span>';
                echo CLOSE_DIV;
            }
            echo $n->navi();
            echo(privilegy('guest_moder') || $guest['user_id'] == $user['user_id'] ? DIV_LI . '<input type="submit" name="post_delete" value="' . $lng['Belgilangan habarlarni o`chirish'] . '"/> <input type="submit" name="clean" value="' . $lng['Devorni tozalash'] . '"/></form>' . CLOSE_DIV : NULL);
        }
        break;

    case 'img':
        if (!empty($_POST['add'])) {
            $msg = html($_SESSION['msg']);
            if (!empty($_FILES['file'])) {
                $name = $_FILES['file']['name']; # Fayl nomi
                $ext = strtolower(strrchr($name, '.')); # Fayl formati
                $par = getimagesize($_FILES['file']['tmp_name']); # Fayl o`lchami
                $size = $_FILES['file']['size']; # Fayl hajmi
                $time = time();
                $file = $time . $ext;

                if ($size > (1048576 * $config['max_upload_guestbook'])) {
                    $err .= '' . $lng['Rasm hajmi belgilangan miqdordan oshmoqda'] . '. [Max. ' . $config['max_upload_guestbook'] . 'Mb.]<br />';
                }

                if (preg_match('/.phtml/i', $name) || preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess') {
                    $err .= '' . $lng['Fayl formatida xatolik'] . '.<br />';
                }

            }

            if (!$ext && !$msg) {
                header("Location: " . HOME . "/guest/" . $guest['id'] . "/");
            }
            if ($ext) {
                copy($_FILES['file']['tmp_name'], '../../files/guest/' . $file);
            }
            $file = (!empty($file) ? $file : '');

            DB::$dbs->query("INSERT INTO " . GUEST . " (`user_id`, `autor_id`, `time`, `msg`, `file`) VALUES (?, ?, ?, ?, ?)", array($guest['user_id'], $user['user_id'], time(), $msg, $file));
            unset($_SESSION['msg']);
            header("Location: " . HOME . "/guest/" . $guest['user_id'] . "/");
        }
        //echo '<pre>', print_r($_SESSION), '</pre>';
        echo DIV_AUT;
        echo '<form action="" enctype="multipart/form-data" method="POST">';
        echo '<b>' . $lng['Rasm biriktirish'] . ':</b> [max. ' . $config['max_upload_guestbook'] . 'mb. / jpg, jpeg, png, gif]<br /><input type="file" name="file"/><br />';
        if (!empty($_SESSION['otv'])) {
            $ank = DB::$dbs->queryFetch("SELECT `user_id`, `nick` FROM " . USERS . " WHERE `user_id` = ? ", array(abs(num($_SESSION['otv']))));
            if (!empty($ank) && $ank['user_id'] != $user['id']) {
                echo '' . $lng['ga javob'] . ' <b>' . $ank['nick'] . '</b><br />';
            } else {
                echo '<b>' . $lng['Habar'] . ':</b><br />';
            }
        }
        echo '<textarea name="msg">', $_SESSION['msg'], '</textarea><br />';
        echo '<input type="submit" name="add" value="' . $lng['Yozish'] . '"/>';
        echo '</form>', CLOSE_DIV;
        break;

    case 'votes':
        //echo '<pre>', print_r($_SESSION), '</pre>';
        if (!empty($_POST)) {
            $msg = html($_POST['msg']);
            $vote = html($_POST['vote']);
            $vote_1 = html($_POST['vote_1']);
            $vote_2 = html($_POST['vote_2']);
            $vote_3 = html($_POST['vote_3']);
            $vote_4 = html($_POST['vote_4']);
            $vote_5 = html($_POST['vote_5']);
            $vote_6 = html($_POST['vote_6']);
            $vote_7 = html($_POST['vote_7']);
            $vote_8 = html($_POST['vote_8']);
            $vote_9 = html($_POST['vote_9']);
            $vote_10 = html($_POST['vote_10']);

            if (!empty($vote) && strlen($vote) < 20) {
                $err[] = '' . $lng['So`rovnomaning nomi juda qisqa'] . '. [min. 10]';
            }

            if (!empty($vote) && (empty($vote_1) || empty($vote_2))) {
                $err[] = '' . $lng['So`rovnomaning asosiy variantlarini to`ldiring'] . '';
            }

            if (empty($err)) {
                DB::$dbs->query("INSERT INTO " . GUEST . " (`user_id`, `autor_id`, `time`, `msg`, `file`, `vote`, `vote_1`, `vote_2`, `vote_3`, `vote_4`, `vote_5`, `vote_6`, `vote_7`, `vote_8`, `vote_9`, `vote_10`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array($guest['user_id'], $user['user_id'], time(), $msg, '', $vote, $vote_1, $vote_2, $vote_3, $vote_4, $vote_5, $vote_6, $vote_7, $vote_8, $vote_9, $vote_10));
                unset($_SESSION['msg']);
                header("Location: " . HOME . "/guest/" . $guest['user_id'] . "/");
            } else {
                echo DIV_ERROR;
                foreach ($err as $value) {
                    echo $value, '<br />';
                }
                echo CLOSE_DIV;
            }
        }

        echo DIV_AUT;
        echo '<form action="#" method="POST" enctype="multipart/form-data">';
        echo '<b>' . $lng['Nomi'] . ':</b> [min. 10]<br /><input type="text" name="vote" /><br />';
        echo '<b>' . $lng['Javob variantlari'] . ':</b> [min. 2]<br />';
        echo '<b>1.</b><input type="text" name="vote_1" /><br />';
        echo '<b>2.</b><input type="text" name="vote_2" /><br />';
        echo '<b>3.</b><input type="text" name="vote_3" /><br />';
        echo '<b>4.</b><input type="text" name="vote_4" /><br />';
        echo '<b>5.</b><input type="text" name="vote_5" /><br />';
        echo '<b>6.</b><input type="text" name="vote_6" /><br />';
        echo '<b>7.</b><input type="text" name="vote_7" /><br />';
        echo '<b>8.</b><input type="text" name="vote_8" /><br />';
        echo '<b>9.</b><input type="text" name="vote_9" /><br />';
        echo '<b>10.</b><input type="text" name="vote_10" /><br /><br />';
        echo '<textarea name="msg">', $_SESSION['msg'], '</textarea><br />';
        echo '<input type="submit" name="add" value="' . $lng['Yozish'] . '"/></form>';
        echo CLOSE_DIV;
        break;

    case 'vote':
        $variant = abs(num($_GET['vote_var']));
        $id = abs(num($_GET['post']));
        $post = DB::$dbs->queryFetch("SELECT * FROM " . GUEST . " WHERE `id` = ? && `user_id` = ?", array($id, $guest['user_id']));

        $err = array();

        if (empty($post)) {
            $err[] = '' . $lng['Sharh topilmadi'] . '!';
        }

        if (empty($post['vote'])) {
            $err[] = '' . $lng['So`rovnoma topilmadi'] . '';
        }

        if (empty($post['vote_' . $variant]) || $variant > 10) {
            $err[] = '' . $lng['Mavjud bo`lmagan variant'] . '';
        }

        if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GUEST_VOTES . " WHERE `post_id` = ? && `user_id` = ? ", array($post['id'], $user['user_id'])) == TRUE) {
            $err[] = '' . $lng['Siz ovoz bergansiz'] . '!';
        }

        if (empty($err)) {
            DB::$dbs->query("INSERT INTO " . GUEST_VOTES . " (`post_id`, `user_id`, `variant`) VALUES (?,?,?)", array($post['id'], $user['user_id'], $variant));

            header("Location: " . HOME . "/guest/" . $guest['user_id'] . "/?vote" . (!empty($_GET['p']) ? '&p=' . (int)$_GET['p'] : NULL));
        } else {
            echo DIV_BLOCK;
            foreach ($err AS $value) {
                echo $value . '<br />';
            }
            echo CLOSE_DIV;
            echo DIV_LI . '<a href="' . HOME . '/guest/' . $guest['user_id'] . '/">' . $lng['Orqaga'] . '</a>' . CLOSE_DIV;
        }
        break;

    case 'like':
        //echo '<pre>', print_r($_GET), '</pre>';
        $variant = abs(num($_GET['vote_var']));
        $id = abs(num($_GET['post']));
        $post = DB::$dbs->queryFetch("SELECT * FROM " . GUEST . " WHERE `id` = ? && `user_id` = ?", array($id, $guest['user_id']));

        $err = array();

        if (empty($post)) {
            $err[] = '' . $lng['Sharh topilmadi'] . '!';
        }

        if (DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GUEST_LIKE . " WHERE `post_id` = ? && `user_id` = ? ", array($post['id'], $user['user_id'])) == TRUE) {
            $err[] = '' . $lng['Siz sharhni baxolagansiz'] . '!';
        }

        if (empty($err)) {
            DB::$dbs->query("INSERT INTO " . GUEST_LIKE . " (`post_id`, `user_id`, `time`) VALUES (?,?,?)", array($post['id'], $user['user_id'], time()));

            $lenta = '<a href="' . HOME . '/id' . $user['user_id'] . '"><b>' . $user['nick'] . '</b></a> ' . $lng['sizning sharhingizni baxoladi'] . ': ' . text($post['msg']) . ' <a href="' . HOME . '/guest/' . $guest['user_id'] . '/"><b>' . $lng['Devorizda'] . ' ' . $guest['nick'] . '</b></a>';
            lenta($lenta, $post['user_id']);

            header("Location: " . HOME . "/guest/" . $guest['user_id'] . "/?like" . (!empty($_GET['p']) ? '&p=' . (int)$_GET['p'] : NULL));
        } else {
            echo DIV_BLOCK;
            foreach ($err AS $value) {
                echo $value . '<br />';
            }
            echo CLOSE_DIV;
            echo DIV_LI . '<a href="' . HOME . '/guest/' . $guest['user_id'] . '/">' . $lng['Orqaga'] . '</a>' . CLOSE_DIV;
        }
        break;

    case 'comments':
        //echo '<pre>', print_r($_GET), '</pre>';
        $variant = isset($_GET['vote_var']) ? abs(num($_GET['vote_var'])) : 0;
        $id = abs(num($_GET['post']));
        $post = DB::$dbs->queryFetch("SELECT * FROM " . GUEST . " WHERE `id` = ? && `user_id` = ?", array($id, $guest['user_id']));

        $err = array();

        if (empty($post)) {
            $err[] = '' . $lng['Sharh topilmadi'] . '!';
        }

        if (empty($err)) {
            if (!empty($_POST)) {
                $text = html($_POST['text']);

                $err = array();
                if (empty($text)) {
                    $err[] = '' . $lng['Sharh matnini yozing'] . '';
                }

                if (empty($err)) {
                    DB::$dbs->query("INSERT INTO " . GUEST_COMMENTS . " (`post_id`, `user_id`, `time`, `text`) VALUES (?,?,?,?)", array($post['id'], $user['user_id'], time(), $text));

                    $lenta = '<a href="' . HOME . '/id' . $user['user_id'] . '"><b>' . $user['nick'] . '</b></a> ' . $lng['sizni habaringizni sharhladi'] . ' <a href="' . HOME . '/guest/' . $guest['user_id'] . '/' . $post['id'] . '/comments/"><b>' . $lng['Devorizda'] . ' ' . $guest['nick'] . '</b></a>';
                    lenta($lenta, $post['user_id']);

                    header("Location: " . HOME . "/guest/" . $guest['user_id'] . "/" . $post['id'] . "/comments/");
                }
            }
            if ($user) {
            echo '<div class="grey"><form action="#" method="POST">
                    <textarea name="text" style="width:96%;"></textarea><br/>
                <input type="submit" name="send" value="' . $lng['Yozish'] . '" /></div>
            </form></div>';
}
            $comm = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GUEST_COMMENTS . " WHERE `post_id` = ?", array($post['id']));

            if (!empty($_GET['del_comm'])) {
                DB::$dbs->query("DELETE FROM " . GUEST_COMMENTS . " WHERE `id` = ? ", array(num($_GET['del_comm'])));
                header("Location: " . HOME . '/moduls/guest/index.php?select=comments&user=' . $guest['user_id'] . '&post=' . $post['id'] . '&del_comm=' . $comm['id'] . '&p=' . (int)$_GET['p']);
            }

            if (empty($comm)) {
                echo DIV_BLOCK . '' . $lng['Sharhlar yo`q'] . '' . CLOSE_DIV;
            } else {
                $n = new Navigator($comm, $config['write']['guest_comm'], 'select=comments&user=' . $guest['user_id'] . '&post=' . $post['id'] . '&');
                $sql = DB::$dbs->query("SELECT * FROM " . GUEST_COMMENTS . " WHERE `post_id` = ? ORDER BY `id` DESC LIMIT {$n->start()}, " . $config['write']['guest_comm'] . "", array($post['id']));
                while ($comm = $sql->fetch()) {
                    echo DIV_LI . userLink($comm['user_id']) . ' [' . vrem($comm['time']) . ']' . (privilegy('guest_moder') ? ' <a href="' . HOME . '/moduls/guest/index.php?select=comments&user=' . $guest['user_id'] . '&post=' . $post['id'] . '&del_comm=' . $comm['id'] . '&p=' . (int)$_GET['p'] . '">[' . $lng['O`chr'] . '.]</a>' : NULL) . CLOSE_DIV;
                    echo DIV_BLOCK . text($comm['text']) . CLOSE_DIV;
                }
                echo $n->navi();
            }
        } else {
            foreach ($err AS $value) {
                echo $value . '<br />';
            }
        }
        echo DIV_LI . '- <a href="' . HOME . '/guest/' . $guest['user_id'] . '/">' . $lng['Orqaga'] . '</a>' . CLOSE_DIV;
        break;
}


require_once('../../core/stop.php');
?>