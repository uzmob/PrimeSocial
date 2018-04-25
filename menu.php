<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */


require_once('core/start.php');

check_auth();

switch ($select) {


    default:
        head('' . $lng['Kabinet'] . '');


        if (empty($_GET['id'])) {
            $page = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " WHERE `user_id` = ?", array(num($_SESSION['user_id'])));
        } else {
            $page = DB::$dbs->queryFetch("SELECT * FROM " . USERS . " WHERE `user_id` = ?", array(num($_GET['id'])));
        }

        echo '<div class="white"> ' . userLink($page['user_id']) . '<br/>';
        ?>
        <html>


        <body>


        <span id="hours" style="font-size:11px;"></span>
        <script type="text/javascript">

            obj_hours = document.getElementById("hours");

            name_month = new Array("<? echo '' . $lng['Yanvar'] . ''; ?>", "<? echo '' . $lng['Fevral'] . ''; ?>", "<? echo '' . $lng['Mart'] . ''; ?>", "<? echo '' . $lng['Aprel'] . ''; ?>", "<? echo '' . $lng['May'] . ''; ?>", "<? echo '' . $lng['Iyun'] . ''; ?>", "<? echo '' . $lng['Iyul'] . ''; ?>", "<? echo '' . $lng['Avgust'] . ''; ?>", "<? echo '' . $lng['Sentyabr'] . ''; ?>", "<? echo '' . $lng['Oktyabr'] . ''; ?>", "<? echo '' . $lng['Noyabr'] . ''; ?>", "<? echo '' . $lng['Dekabr'] . ''; ?>");
            name_day = new Array("<? echo '' . $lng['Yakshanba'] . ''; ?>", "<? echo '' . $lng['Dushanba'] . ''; ?>", "<? echo '' . $lng['Seshanba'] . ''; ?>", "<? echo '' . $lng['Chorshanba'] . ''; ?>", "<? echo '' . $lng['Payshanba'] . ''; ?>", "<? echo '' . $lng['Juma'] . ''; ?>", "<? echo '' . $lng['Shanba'] . ''; ?>");

            function wr_hours() {
                time = new Date();

                time_sec = time.getSeconds();
                time_min = time.getMinutes();
                time_hours = time.getHours();
                time_wr = ((time_hours < 10) ? "0" : "") + time_hours;
                time_wr += ":";
                time_wr += ((time_min < 10) ? "0" : "") + time_min;
                time_wr += ":";
                time_wr += ((time_sec < 10) ? "0" : "") + time_sec;

                time_wr = " <? echo '' . $lng['Bugun'] . ''; ?>: " + name_day[time.getDay()] + ", " + time.getDate() + " " + name_month[time.getMonth()] + " " + time.getFullYear() + " <? echo '' . $lng['y. soat'] . ''; ?> " + time_wr;

                obj_hours.innerHTML = time_wr;
            }

            wr_hours();
            setInterval("wr_hours();", 1000);

        </script>


        </body>
        </html>
        <?php
        echo '</div>';

////// Mini tasma //////
        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . LENTA . " WHERE `user_id` = ? ", array($user['user_id']));

        if (empty($all)) {
            echo DIV_BLOCK . '' . $lng['Tasma bo`sh'] . '' . CLOSE_DIV;
        } else {
            echo '<div class="white" style="font-size:13px;">';
            echo '<form action="#" method="POST">';
            $n = new Navigator($all, $config['write']['guest'], '');
            $sql = DB::$dbs->query("SELECT * FROM " . LENTA . " WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 3", array($user['user_id']));

            while ($post = $sql->fetch()) {
                echo '' . vrem($post['time']) . '</b> <a href="?del=' . $post['id'] . '">[' . $lng['O`chr'] . '.]</a><br />' . text($post['text']) . '<hr/>';

                if ($post['status'] == 1) {
                    DB::$dbs->query("UPDATE " . LENTA . " SET `status` = '0' WHERE `id` = ? ", array($post['id']));
                }
            }
            echo '<a href="/lenta/"> &raquo; ' . $lng['Tasmga o`tish'] . '</a></div>';

        }
////// Mini tasma //////

        if (!empty($user['level'])) {
            echo '<div class="lines">';
            echo '' . icon('tizim.png') . ' <a href="' . HOME . '/panel"><u> ' . $lng['Apanel'] . '</u></a>';
            echo '</div>';
        }

        $all = DB::$dbs->querySingle("SELECT COUNT(*) FROM " . TOUCH_USER . " WHERE `user_id` = ? ", array($user['user_id']));
        echo '<div class="lines">';
        echo '' . icon('tiket.png') . ' <a href="' . HOME . '/touch/">' . $lng['Tiket'] . '</a> <span class="count"> ' . $all . '</span></a>';
        echo '</div>';

        echo '<div class="lines">';
        echo '' . icon('sozlash.png') . ' <a href="' . HOME . '/menu/sett">' . $lng['Sozlamalar'] . '</a>';
        echo '</div>';

        echo '<div class="lines">';
        echo '' . icon('vip.png') . ' <a href="' . HOME . '/shop/">' . $lng['Pullik hizmatlar'] . '</a>';
        echo '</div>';

        echo '<div class="lines">';
        echo '' . icon('palet.png') . ' <a href="' . HOME . '/menu/style">' . $lng['Sayt ko`rinishlari'] . '</a>';
        echo '</div>';

        echo '<div class="lines">';
        echo '' . icon('sts.png') . ' <a href="' . HOME . '/menu/status">' . $lng['Statuslar'] . '</a>';
        echo '</div>';

        echo '<div class="lines">';
        echo '' . icon('search.png') . ' <a href="' . HOME . '/search/">' . $lng['Saytda izlash'] . '</a>';
        echo '</div>';

        echo '<div class="lines">';
        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . BLACKUSERS . " WHERE `user_id` = ?  ", array($user['user_id']));
        echo '' . icon('trash.png') . '  <a href="' . HOME . '/blacklist/">' . $lng['Qora ro`yhat'] . ' <span class="count"> ' . $all . '</span></a>';
        echo '</div>';

        echo '<div class="lines">';
        $all = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . " WHERE `ref` = ?", array($user['user_id']));
        echo '' . icon('ref.png') . ' <a href="' . HOME . '/menu/ref">' . $lng['Referal tizim'] . ' <span class="count"> ' . $all . '  </span></a>';
        echo '</div>';

        echo '<div class="lines">';
        echo '' . icon('info.png') . ' <a href="' . HOME . '/faq">' . $lng['Ma`lumotlar'] . '</a>';
        echo '</div>';

        echo '<div class="lines">';
        echo '' . icon('exit.png') . ' <a href="' . HOME . '/exit">' . $lng['Chiqish'] . '</a>';
        echo '</div>';


        break;

    case 'shahsiy':
        head('' . $lng['Shahsiylik sozlamalari'] . '');

        if (!empty($_POST['private'])) {
            $private_page = abs((int)$_POST['private_page']);
            $private_guest = abs((int)$_POST['private_guest']);
            $private_usfiles = abs((int)$_POST['private_usfiles']);
            $private_photos = abs((int)$_POST['private_photos']);
            DB::$dbs->query("UPDATE " . USERS . " SET `private_page` = ?, `private_guest` = ?, `private_usfiles` = ?, `private_photos` = ? WHERE `user_id` = ?", array($private_page, $private_guest, $private_usfiles, $private_photos, $user['user_id']));
            echo DIV_MSG . '' . $lng['Sozlamalar muvaffaqiyatli saqlandi'] . '' . CLOSE_DIV;
        }
        echo '<div class="white">';
        echo '<form action="#" method="POST">
                ' . $lng['Sahifamni kimlar ko`ra oladi'] . ':<br/>
                <select name="private_page">
                    <option value="0">' . $lng['Hamma'] . '</option>
                    <option value="1" ' . ($user['private_page'] == 1 ? 'selected' : null) . '>' . $lng['Faqat do`stlarim'] . '</option>
                    <option value="2" ' . ($user['private_page'] == 2 ? 'selected' : null) . '>' . $lng['Faqat men'] . '</option>
                </select>';
        echo '</div>';
        echo '<div class="white">';
        echo '' . $lng['Mehmonxonamni kimlar ko`ra oladi'] . ':<br/>
                <select name="private_guest">
                    <option value="0">' . $lng['Hamma'] . '</option>
                    <option value="1" ' . ($user['private_guest'] == 1 ? 'selected' : null) . '>' . $lng['Faqat do`stlarim'] . '</option>
                    <option value="2" ' . ($user['private_guest'] == 2 ? 'selected' : null) . '>' . $lng['Faqat men'] . '</option>
                </select>';
        echo '</div>';
        echo '<div class="white">';
        echo '' . $lng['Shahsiy fayllarimni kimlar ko`ra oladi'] . ':<br/>
                <select name="private_usfiles">
                    <option value="0">' . $lng['Hamma'] . '</option>
                    <option value="1" ' . ($user['private_usfiles'] == 1 ? 'selected' : null) . '>' . $lng['Faqat do`stlarim'] . '</option>
                    <option value="2" ' . ($user['private_usfiles'] == 2 ? 'selected' : null) . '>' . $lng['Faqat men'] . '</option>
                </select>';
        echo '</div>';
        echo '<div class="white">';
        echo '' . $lng['Rasmlarimni kimlar ko`ra oladi'] . ':<br/>
                <select name="private_photos">
                    <option value="0">' . $lng['Hamma'] . '</option>
                    <option value="1" ' . ($user['private_photos'] == 1 ? 'selected' : null) . '>' . $lng['Faqat do`stlarim'] . '</option>
                    <option value="2" ' . ($user['private_photos'] == 2 ? 'selected' : null) . '>' . $lng['Faqat men'] . '</option>
                </select>';
        echo '</div>';
        echo '<div class="lines">';
        echo '<input type="submit" name="private" value="' . $lng['Saqlash'] . '" /></form>';
        echo '</div>';

        break;

    case 'guests':
        head('' . $lng['Mehmonlarim'] . '');

        $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GUESTS . " WHERE `user_id` = ?", array($user['user_id']));
        if (empty($all)) {
            echo DIV_BLOCK . '' . $lng['Sahifangizga hali hech kim kirmagan'] . '' . CLOSE_DIV;
        } else {
            if (isset($_GET['del'])) {
                DB::$dbs->query("DELETE FROM " . GUESTS . " WHERE `user_id` = ?", array($user['user_id']));
            }
            $n = new Navigator($all, 10, '');
            $sql = DB::$dbs->query("SELECT * FROM " . GUESTS . " WHERE `user_id` = ? ORDER BY `date` DESC LIMIT {$n->start()}, 10", array($user['user_id']));
            while ($post = $sql->fetch()) {
                echo '<div class="lines"><table cellspacing="0" cellpadding="0" width="100%" ><tr>';
                echo '<td width="5%">';
                echo '<center>' . avatar($post['guest_id'], 40, 40) . ' </center>';
                echo '</td>';
                echo '<td width="95%">';
                echo '&nbsp; ' . user_choice($post['guest_id'], 'link') . '<br/>
&nbsp; <span class="mini">' . vrem($post['date']) . '</span><br/>';
                echo '</td></tr></table></div>';

            }
            echo $n->navi();
            echo DIV_LI . '<a href="?del">' . $lng['Ro`yhatni tozalash'] . '</a>' . CLOSE_DIV;
        }
        echo CLOSE_DIV;
        break;
    case 'style':
        head('' . $lng['Sayt ko`rinishi'] . '');

        if (isset($_GET['ver'])) {
            $ver = trim(htmlspecialchars($_GET['ver']));
            if (!is_dir(inc . "style/themes/$ver")) {
                die('' . $lng['Bunday dizayn yo`q'] . '!');
            }
            DB::$dbs->query("UPDATE " . USERS . " SET `style` = ? WHERE `user_id` = ?", array($ver, $user['user_id']));
            header("Location: " . HOME . "/");
        }

        $path_them = opendir(inc . 'style/themes/');
        while ($thems = readdir($path_them)) {
            if ($thems == '.' || $thems == '..' || !is_dir(inc . "style/themes/$thems")) continue;
            $count = DB::$dbs->querySingle("SELECT COUNT(*) FROM `users` WHERE `style` = ?", array($thems));

            echo '<table cellspacing="0" cellpadding="0" width="100%" ><tr>
<td class="lines" width="5%">';
            echo(file_exists(inc . 'style/themes/' . $thems . '/theme.png') ? '<a href="/style/themes/' . $thems . '/theme.png"><img src="/style/themes/' . $thems . '/theme.png" alt="preview" style="width:50px;height:50px;border-radius:55%;"/></a>' : '<img="/style/img/theme.png" alt="preview" style="width:50px;height:50px;border-radius:55%;"/>');
            echo '</td><td class="lines"  width="90%">';
            echo '' . ($user['style'] != $thems ? '<a href="/menu/style?ver=' . $thems . '">' . trim(file_get_contents(inc . 'style/themes/' . $thems . '/name.txt')) . '</a>' : '<b>' . trim(file_get_contents(inc . 'style/themes/' . $thems . '/name.txt')) . '</b>') . ' <br/><span class="mini">' . ($thems == $user['style'] ? ' ' . $count . ' ' : ' ' . $count . ' ') . ' ' . $lng['ta foydanuvchida o`rnatilgan'] . ' </span><br/>';

            echo '</td></tr></table>';


        }
        closedir($path_them);


        break;

    case 'ref':
        head('' . $lng['Referal tizim'] . '');

        echo DIV_BLOCK . '<b>' . $lng['Taklif qilish uchun sizga manzil'] . ':</b><br /><textarea>' . HOME . '/reg/?ref=' . $user['user_id'] . '</textarea>' . CLOSE_DIV;
        $all = DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . " WHERE `ref` = ?", array($user['user_id']));
        if (empty($all)) {
            echo DIV_BLOCK . '' . $lng['Ro`yhat bo`sh'] . '' . CLOSE_DIV;
        } else {
            $n = new Navigator($all, 10, '');
            $sql = DB::$dbs->query("SELECT `user_id`, `recording_date` FROM " . USERS . " WHERE `ref` = ? ORDER BY `recording_date` DESC LIMIT {$n->start()}, 10", array($user['user_id']));
            while ($ank = $sql->fetch()) {
                echo DIV_BLOCK . user_choice($ank['user_id'], 'link') . ' ' . vrem($ank['recording_date']) . CLOSE_DIV;
            }
            echo $n->navi();
        }
        echo CLOSE_DIV;
        break;

    case 'status':
        head('' . $lng['Status'] . '');


        if (!empty($_POST['status'])) {
            $status = html($_POST['status']);

            if (strlen($status) > 500) {
                echo DIV_ERROR . '' . $lng['Juda uzun status'] . '' . CLOSE_DIV;
            } else {
                DB::$dbs->query("INSERT INTO " . STATUS . " (`user_id`, `time`, `status`) VALUES (?, ?, ?)", array($user['user_id'], time(), $status));

                if (isset($_GET['ank'])) {
                    header("Location: " . HOME . "/id" . $user['user_id']);
                } else {
                    header("Location: " . HOME . "/menu/status");
                }
            }
        }

        $status = DB::$dbs->queryFetch("SELECT * FROM " . STATUS . " WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 1", array($user['user_id']));
        echo DIV_AUT;
        if (isset($_GET['edit'])) {
            echo '<form action="' . HOME . '/menu/status' . (isset($_GET['ank']) ? '?ank' : NULL) . '" method="POST">';
            echo '' . $lng['Yangi status'] . ' [max. 500]<br />
		<textarea name="status" placeholder="' . $status['status'] . '" style="width:95%;height:5pc;"/></textarea><br/>
		<input type="submit" value="' . $lng['Yozish'] . '"/>';
            echo '</form>';
        } else {
            if (!empty($status)) {
                echo '<div class="white"><div class="sts" style="border-radius:4px;><a href="?edit">' . $status['status'] . '</a></div></div>';
            } else {
                echo '<form action="?" method="POST">';
                echo '' . $lng['Yangi status'] . ' [max. 500]<br /><input type="text" name="status"/><input type="submit" value="+"/>';
                echo '</form>';
            }
        }
        echo CLOSE_DIV;
        break;

    case 'photo':
        head('' . $lng['Avatar'] . '');


        if (!empty($_FILES['file'])) {
            $name = $_FILES['file']['name']; # Fayl nomi
            $ext = strtolower(strrchr($name, '.')); # Fayl formati
            $par = getimagesize($_FILES['file']['tmp_name']); # Rasm shakli
            $size = $_FILES['file']['size']; # Fayl hajmi
            $photo = $user['user_id'] . $ext;
            $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar

            if ($par[0] > $config['photo_par'][0] || $par[1] > $config['photo_par'][1]) {
                $err .= '' . $lng['Rasm hajmi katta'] . '. [Max. ' . $config['photo_par'][0] . 'x' . $config['photo_par'][1] . ']<br />';
            }

            if ($size > (1048576 * $config['max_upload_photo'])) {
                $err .= '' . $lng['Rasm hajmi katta'] . '. [Max. ' . $config['max_upload_photo'] . 'mb]<br />';
            }

            if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $pictures)) {
                $err .= '' . $lng['Rasm formati xato'] . '.<br />';
            }

            if (empty($err)) {
                copy($_FILES['file']['tmp_name'], 'files/photo/' . $user['user_id'] . $ext); # Original tarzda yuklaymiz
                img_resize('files/photo/' . $user['user_id'] . $ext, 'files/photo/mini_' . $user['user_id'] . $ext, $config['mini_photo_par'][0], $config['mini_photo_par'][1]); # Mini
                DB::$dbs->query("UPDATE " . USERS . " SET `photo` = ? WHERE `user_id` = ?", array($photo, $user['user_id']));
                DB::$dbs->query("DELETE FROM " . PHOTO_RATING . " WHERE `friend_id` = ? ", array($user['user_id']));
                DB::$dbs->query("DELETE FROM " . PHOTO_COMM . " WHERE `friend_id` = ? ", array($user['user_id']));
                header("Location: " . HOME . "/menu/photo");
            } else {
                echo $err;
            }
        }

        if (!empty($_POST['delete'])) {
            unlink("files/photo/" . $user['photo']);
            unlink("files/photo/mini_" . $user['photo']);
            DB::$dbs->query("UPDATE " . USERS . " SET `photo` = ? WHERE `user_id` = ?", array(NULL, $user['user_id']));
            DB::$dbs->query("DELETE FROM " . PHOTO_RATING . " WHERE `friend_id` = ? ", array($user['user_id']));
            DB::$dbs->query("DELETE FROM " . PHOTO_COMM . " WHERE `friend_id` = ? ", array($user['user_id']));
            header("Location: " . HOME . "/menu/photo");
        }

        echo DIV_AUT;
        if (empty($user['photo'])) {
            echo '' . avatar($user['user_id'], 100, 100) . '<br />';
            echo '<form action="?" enctype="multipart/form-data" method="POST">';
            echo '<b>' . $lng['Avatarni yangilash'] . ':</b><br/><span class="mini">max. 5mb; 1600x1600px; jpg, gif, png</span><br />
		<input type="file" name="file"/><br />';
            echo '<input type="submit" value="' . $lng['Yuklash'] . '"/>';
            echo '</form>';
        } else {
            echo '<a href="' . HOME . '/files/photo/' . $user['photo'] . '"><img src="' . HOME . '/files/photo/mini_' . $user['photo'] . '"/></a><br />';
            echo '<form action="?" method="POST"><input type="submit" name="delete" value="' . $lng['O`chirish'] . '"/></form>';
        }
        echo CLOSE_DIV;


        echo '<div class="sts">' . $lng['Avatar info'] . '</div>';

        break;

    case 'cover':
        head('' . $lng['Muqova'] . '');


        if (!empty($_FILES['file'])) {
            $name = $_FILES['file']['name']; # Fayl nomi
            $ext = strtolower(strrchr($name, '.')); # Fayl formati
            $par = getimagesize($_FILES['file']['tmp_name']); # Rasm shakli
            $size = $_FILES['file']['size']; # Fayl hajmi
            $cover = $user['user_id'] . $ext;
            $pictures = array('.jpg', '.jpeg', '.gif', '.png'); # Mumkun bo`lgan formatlar

            if ($par[0] > $config['photo_par'][0] || $par[1] > $config['photo_par'][1]) {
                $err .= '<div class="error">' . $lng['Rasm hajmi katta'] . '. [Max. ' . $config['photo_par'][0] . 'x' . $config['photo_par'][1] . ']</div>';
            }

            if ($size > (1048576 * $config['max_upload_photo'])) {
                $err .= '<div class="error">' . $lng['Rasm hajmi katta'] . '. [Max. ' . $config['max_upload_photo'] . 'mb]</div>';
            }

            if (preg_match('/.php/i', $name) || preg_match('/.pl/i', $name) || $name == '.htaccess' || !in_array($ext, $pictures)) {
                $err .= '<div class="error">' . $lng['Rasm formati xato'] . '.</div>';
            }

            if (empty($err)) {
                copy($_FILES['file']['tmp_name'], 'files/cover/' . $user['user_id'] . $ext); # Original tarzda yuklaymiz
                DB::$dbs->query("UPDATE " . USERS . " SET `cover` = ? WHERE `user_id` = ?", array($cover, $user['user_id']));
                header("Location: " . HOME . "/menu/cover");
            } else {
                echo $err;
            }
        }

        if (!empty($_POST['delete'])) {
            unlink("files/cover/" . $user['cover']);
            DB::$dbs->query("UPDATE " . USERS . " SET `cover` = ? WHERE `user_id` = ?", array(NULL, $user['user_id']));
            header("Location: " . HOME . "/menu/cover");
        }

        echo DIV_AUT;
        if (empty($user['cover'])) {
            echo '' . cover($user['user_id'], 100, 100) . '<br />';
            echo '<form action="?" enctype="multipart/form-data" method="POST">';
            echo '<b>' . $lng['Muqovani yangilash'] . ':</b><br/><span class="mini">max. 5mb; 1600x1600px; jpg, gif, png</span><br />
		<input type="file" name="file"/><br />';
            echo '<input type="submit" value="' . $lng['Yuklash'] . '"/>';
            echo '</form>';
        } else {
            echo '<a href="' . HOME . '/files/cover/' . $user['cover'] . '"><img src="' . HOME . '/files/cover/' . $user['cover'] . '" class="cover"/></a><br />';
            echo '<form action="?" method="POST"><input type="submit" name="delete" value="' . $lng['O`chirish'] . '"/></form>';
        }
        echo CLOSE_DIV;


        echo '<div class="sts">' . $lng['Muqova info'] . '</div>';

        break;
    case 'anceta':
        head('' . $lng['Anketani tahrirlash'] . '');


        if ($_POST) {
            $surname = html($_POST['surname']);
            $name = html($_POST['name']);
            $gender = num($_POST['gender']);
            $bday = abs(num($_POST['bday']));
            $bmonth = abs(num($_POST['bmonth']));
            $byear = abs(num($_POST['byear']));
            $about = html($_POST['about']);
            $interes = html($_POST['interes']);
            $music = html($_POST['music']);
            $cinema = html($_POST['cinema']);
            $books = html($_POST['books']);
            $smok = abs(num($_POST['smok']));
            $alco = abs(num($_POST['alco']));
            $narco = abs(num($_POST['narco']));

            if (!empty($bday) && $bday > 31) {
                $err .= '' . $lng['Tug`ulgan kun xato ko`rsatilgan'] . '';
            }

            if (!empty($bmonth) && $bmonth > 12) {
                $err .= '' . $lng['Tug`ulgan oy xato ko`rsatilgan'] . '';
            }

            if (!empty($bday) || !empty($bmonth) || !empty($byear)) {
                $age = calc_age($bday . '/' . $bmonth . '/' . $byear);
            }

            if ($smok > 6 || $alco > 5 || $narco > 6) {
                $err .= '' . $lng['Kiritish so`zlari xato ko`rsatilgan'] . '<br />';
            }

            if (empty($surname) || empty($name)) {
                $err .= '' . $lng['Barcha maydonchalarni to`ldirib chiqing'] . '<br />';
            }

            if (strlen($surname) < 2) {
                $err .= '' . $lng['Familiya juda qisqa'] . '. [Min. 2]<br />';
            }

            if (strlen($name) < 2) {
                $err .= '' . $lng['Juda qisqa ism'] . '. [Min. 2]<br />';
            }

            if ($err) {
                echo DIV_ERROR . $err . CLOSE_DIV;
            } else {
                DB::$dbs->query("UPDATE " . USERS . " SET `surname` = ?, `name` = ?, `gender` = ?, `bday` = ?, `bmonth` = ?, `byear` = ?, `age` = ?,
            `about` = ?, `interes` = ?, `music` = ?, `cinema` = ?, `books` = ?, `smok` = ?, `alco` = ?, `narco` = ? WHERE `user_id` = ?", array($surname, $name, $gender, $bday, $bmonth, $byear, $age, $about, $interes, $music, $cinema, $books, $smok, $alco, $narco, $user['user_id']));
                echo DIV_MSG . '' . $lng['Ma`lumotlar muvaffaqiyatli yangilandi'] . '' . CLOSE_DIV;
            }

        }

        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo '<b>' . $lng['Familiya'] . ':</b><br /><input type="text" name="surname" value="' . $user['surname'] . '" style="width:95%;"/><br /><br />';
        echo '<b>' . $lng['Ism'] . ':</b><br /><input type="text" name="name" value="' . $user['name'] . '" style="width:95%;"/><br /><br />';
        echo '<b>' . $lng['Jins'] . ':</b><br /><input type="radio" name="gender" value="0" ' . ($user['gender'] == 0 ? 'checked="checked"' : NULL) . ' /> ' . $lng['Ayol'] . '<br /><input type="radio" name="gender" value="1" ' . ($user['gender'] == 1 ? 'checked="checked"' : NULL) . ' /> ' . $lng['Erkak'] . '<br /><br />';

        echo '<b>' . $lng['Tug`ulgan kun'] . ':</b><br /><select name="bday" style="width:95%;">';

        for ($i == 1; $i < 32; ++$i) {
            echo '<option value="' . $i . '" ' . ($i == $user['bday'] ? 'selected="selected"' : NULL) . ' ">' . ($i == 0 ? '' . $lng['Ko`rsatilmagan'] . '' : $i) . '</option>';
        }
        echo '</select>';

        echo '<select name="bmonth" style="width:95%;">';
        echo '<option value="0" ' . (0 == $user['bmonth'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Ko`rsatilmagan'] . '</option>';
        echo '<option value="1" ' . (1 == $user['bmonth'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Yanvar'] . '</option>';
        echo '<option value="2" ' . (2 == $user['bmonth'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Fevral'] . '</option>';
        echo '<option value="3" ' . (3 == $user['bmonth'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Mart'] . '</option>';
        echo '<option value="4" ' . (4 == $user['bmonth'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Aprel'] . '</option>';
        echo '<option value="5" ' . (5 == $user['bmonth'] ? 'selected="selected"' : NULL) . ' ">' . $lng['May'] . '</option>';
        echo '<option value="6" ' . (6 == $user['bmonth'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Iyun'] . '</option>';
        echo '<option value="7" ' . (7 == $user['bmonth'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Iyul'] . '</option>';
        echo '<option value="8" ' . (8 == $user['bmonth'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Avgust'] . '</option>';
        echo '<option value="9" ' . (9 == $user['bmonth'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Sentyabr'] . '</option>';
        echo '<option value="10" ' . (10 == $user['bmonth'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Oktyabr'] . '</option>';
        echo '<option value="11" ' . (11 == $user['bmonth'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Noyabr'] . '</option>';
        echo '<option value="12" ' . (12 == $user['bmonth'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Dekabr'] . '</option>';
        echo '</select>';

        echo '<select name="byear" style="width:95%;">';
        echo '<option value="0" ' . (0 == $user['byear'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Ko`rsatilmagan'] . '</option>';
        for ($i = 2002; $i >= 1950; --$i) {
            echo '<option value="' . $i . '" ' . ($i == $user['byear'] ? 'selected="selected"' : NULL) . ' ">' . ($i == 0 ? '' . $lng['Ko`rsatilmagan'] . '' : $i) . '</option>';
        }
        echo '</select><br /><br />';

        echo '' . $lng['Shahar'] . ': <a href="' . HOME . '/menu/city" title="' . $lng['O`zgartirish'] . '">' . city($user['city']) . '</a><br /><br />';

        echo '<b>' . $lng['O`zingiz haqida'] . ':</b><br /><textarea name="about" style="width:95%;">' . $user['about'] . '</textarea><br />';
        echo '<b>' . $lng['Qiziqishlaringiz'] . ':</b><br /><textarea name="interes" style="width:95%;">' . $user['interes'] . '</textarea><br />';
        echo '<b>' . $lng['Sevimli musiqangiz'] . ':</b><br /><textarea name="music" style="width:95%;">' . $user['music'] . '</textarea><br />';
        echo '<b>' . $lng['Sevimli filmlaringiz'] . ':</b><br /><textarea name="cinema" style="width:95%;">' . $user['cinema'] . '</textarea><br />';
        echo '<b>' . $lng['Sevimli kitoblaringiz'] . ':</b><br /><textarea name="books" style="width:95%;">' . $user['books'] . '</textarea><br />';

        echo '<b>' . $lng['Chekishga a`loqangiz'] . ':</b><br /><select name="smok" style="width:95%;">';
        echo ' <option value="0" ' . (0 == $user['smok'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Ko`rsatilmagan'] . '</option>';
        echo ' <option value="1" ' . (1 == $user['smok'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Chekmaganman'] . '</option>';
        echo ' <option value="2" ' . (2 == $user['smok'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Tashaganman'] . '</option>';
        echo ' <option value="3" ' . (3 == $user['smok'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Tashayapman'] . '</option>';
        echo ' <option value="4" ' . (4 == $user['smok'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Faqat ichgan paytimda'] . '</option>';
        echo ' <option value="5" ' . (5 == $user['smok'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Paravoz kabi tutataman'] . ' ;)</option>';
        echo ' <option value="6" ' . (6 == $user['smok'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Chekaman'] . '</option>';
        echo '</select><br />';

        echo '<b>' . $lng['Ichishga a`loqangiz'] . ':</b><br /><select name="alco" style="width:95%;">';
        echo ' <option value="0" ' . (0 == $user['alco'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Ko`rsatilmagan'] . '</option>';
        echo ' <option value="1" ' . (1 == $user['alco'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Ichmaganman'] . '</option>';
        echo ' <option value="2" ' . (2 == $user['alco'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Tashaganman'] . '</option>';
        echo ' <option value="3" ' . (3 == $user['alco'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Faqat bayramlarda'] . '</option>';
        echo ' <option value="4" ' . (4 == $user['alco'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Tashayapman'] . '</option>';
        echo ' <option value="5" ' . (5 == $user['alco'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Ichaman'] . '</option>';
        echo '</select><br />';

        echo '<b>' . $lng['Odamlarda asosiysi'] . ':</b><br /><select name="narco" style="width:95%;">';
        echo ' <option value="0" ' . (0 == $user['narco'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Ko`rsatilmagan'] . '</option>';
        echo ' <option value="1" ' . (1 == $user['narco'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Aql va ijod'] . '</option>';
        echo ' <option value="2" ' . (2 == $user['narco'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Rahmdillik va rostgo`ylik'] . '</option>';
        echo ' <option value="3" ' . (3 == $user['narco'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Boylik va kuchlilik'] . '</option>';
        echo ' <option value="4" ' . (4 == $user['narco'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Jasurlik va qat`iyat'] . '</option>';
        echo ' <option value="5" ' . (5 == $user['narco'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Hazilkashlik va hayotni sevish'] . '</option>';
        echo ' <option value="6" ' . (6 == $user['narco'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Chiroy va sog`lomlik'] . '</option>';
        echo '</select><br />';

        echo '<input type="submit" value="' . $lng['Saqlash'] . '" /><br /><br />';

        echo '- <a href="' . HOME . '/menu/love" title="' . $lng['O`zgartirish'] . '">' . $lng['Tanishuv anketasi'] . '</a><br />';
        echo '</form>';
        echo CLOSE_DIV;

        break;

    case 'love':
        head('' . $lng['Tanishuv anketasi'] . '');

        if ($_POST) {
            $poznakom = abs(num($_POST['poznakom']));
            $age1 = abs(num($_POST['age1']));
            $age2 = abs(num($_POST['age2']));
            $goal = abs(num($_POST['goal']));
            $family_status = abs(num($_POST['family_status']));
            $children = abs(num($_POST['children']));
            $orientation = abs(num($_POST['orientation']));

            if ($poznakom > 2 || ($age1 > $age2) || $goal > 6 || $family_status > 6 || $children > 5 || $orientation > 3) {
                $err .= '' . $lng['Xatolik'] . '<br />';
            }

            if ($err) {
                echo DIV_ERROR . $err . CLOSE_DIV;
            } else {
                DB::$dbs->query("UPDATE " . USERS . " SET `poznakom` = ?, `age1` = ?, `age2` = ?, `goal` = ?, `family_status` = ?, `children` = ?, `orientation` = ? WHERE `user_id` = ?",
                    array($poznakom, $age1, $age2, $goal, $family_status, $children, $orientation, $user['user_id']));
                echo DIV_MSG . '' . $lng['Ma`lumotlar muvaffaqiyatli yangilandi'] . '' . CLOSE_DIV;
            }

        }


        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo '<b>' . $lng['Tanishaman'] . ':</b><br /><select name="poznakom" style="width:95%;">';
        echo '<option value="0" ' . (0 == $user['poznakom'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Ko`rsatilmagan'] . '</option>';
        echo '<option value="1" ' . (1 == $user['poznakom'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Qiz bola bilan'] . '</option>';
        echo '<option value="2" ' . (2 == $user['poznakom'] ? 'selected="selected"' : NULL) . ' ">' . $lng['O`g`il bola bilan'] . '</option>';
        echo '</select><br />';

        echo '<b>' . $lng['Yoshi2'] . ':</b><br /> <input type="text" name="age1" value="' . $user['age1'] . '" size="2" placeholder="' . $lng['dan'] . '" style="width:42%;"/>
	<input type="text" name="age2" value="' . $user['age2'] . '"  size="2" placeholder="' . $lng['gacha'] . '" style="width:42%;"/> <br />';

        echo '<b>' . $lng['Tanishishdan maqsadi'] . ':</b><br /><select name="goal" style="width:95%;">';
        echo '<option value="0" ' . (0 == $user['goal'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Ko`rsatilmagan'] . '</option>';
        echo '<option value="1" ' . (1 == $user['goal'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Do`stlik'] . '</option>';
        echo '<option value="2" ' . (2 == $user['goal'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Suhbat'] . '</option>';
        echo '<option value="3" ' . (3 == $user['goal'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Flirt'] . '</option>';
        echo '<option value="4" ' . (4 == $user['goal'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Sevgi'] . '</option>';
        echo '<option value="5" ' . (5 == $user['goal'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Uchrashuv uchun'] . '</option>';
        echo '<option value="6" ' . (6 == $user['goal'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Jiddiy a`loqa uchun'] . '</option>';
        echo '</select><br />';

        echo '<b>' . $lng['Oilaviy ahvoli'] . ':</b><br /><select name="family_status" style="width:95%;">';
        echo '<option value="0" ' . (0 == $user['family_status'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Ko`rsatilmagan'] . '</option>';
        echo '<option value="1" ' . (1 == $user['family_status'] ? 'selected="selected"' : NULL) . ' ">' . ($user['gender'] == 0 ? '' . $lng['Bo`shman'] . '' : '' . $lng['Bo`shman2'] . '') . '</option>';
        echo '<option value="2" ' . (2 == $user['family_status'] ? 'selected="selected"' : NULL) . ' ">' . ($user['gender'] == 0 ? '' . $lng['Turmushga chiqganman'] . '' : '' . $lng['Uylanganman'] . '') . '</option>';
        echo '<option value="3" ' . (3 == $user['family_status'] ? 'selected="selected"' : NULL) . ' ">' . ($user['gender'] == 0 ? '' . $lng['Bandman'] . '' : '' . $lng['Bandman2'] . '') . '</option>';
        echo '<option value="4" ' . (4 == $user['family_status'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Ikkinchi juftimni izlashdaman'] . '</option>';
        echo '<option value="5" ' . (5 == $user['family_status'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Sevganim yo`q'] . '</option>';
        echo '<option value="6" ' . (6 == $user['family_status'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Hammavaqt tayyorman'] . '</option>';
        echo '</select><br />';

        echo '<b>' . $lng['Bolalaringiz bormi'] . '?:</b><br /><select name="children" style="width:95%;">';
        echo '<option value="0" ' . (0 == $user['children'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Ko`rsatilmagan'] . '</option>';
        echo '<option value="1" ' . (1 == $user['children'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Hali yo`q'] . '</option>';
        echo '<option value="2" ' . (2 == $user['children'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Yo`q'] . '</option>';
        echo '<option value="3" ' . (3 == $user['children'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Bor'] . '</option>';
        echo '<option value="4" ' . (4 == $user['children'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Yo`q, ammo istayman'] . '</option>';
        echo '<option value="5" ' . (5 == $user['children'] ? 'selected="selected"' : NULL) . ' ">' . $lng['Ulg`ayishgan'] . '</option>';
        echo '</select><br />';


        echo '<input type="submit" value="' . $lng['Saqlash'] . '" /><br />';
        echo '</form>';
        echo CLOSE_DIV;
        break;

    case 'sett':
        head('' . $lng['Sozlamalar'] . '');

        if (isset($_POST['lng'])) {

            if (!isset($clng[$_POST['lng']])) echo DIV_ERROR . '' . $lng['Tanlangan til mavjud emas'] . '!' . CLOSE_DIV;

            else {

                DB::$dbs->query("UPDATE " . USERS . " SET `lng` = ? WHERE `user_id` = ?", array($_POST['lng'], $user['user_id']));
                echo DIV_MSG . '' . $lng['Til muvaffaqiyatli o`zgardi, sahifani yangilang'] . '!' . CLOSE_DIV;


            }

        }

        if ($_POST['mail']) {

            $email = html($_POST['email']);

            if (empty($email)) {
                $err .= '' . $lng['E-Mailni to`ldiring'] . '<br />';
            }

            if (strlen($email) < 8) {
                $err .= '' . $lng['E-Mail juda qisqa'] . '. [Min. 8]<br />';
            }

            if (!preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $email)) {
                $err .= '' . $lng['E-Mailni to`g`ri formatda ko`rsating'] . '<br />';
            }

            if ($err) {
                echo DIV_ERROR . $err . CLOSE_DIV;
            } else {
                DB::$dbs->query("UPDATE " . USERS . " SET `email` = ? WHERE `user_id` = ?", array($email, $user['user_id']));
                echo DIV_MSG . '' . $lng['E-Mail muvaffaqiyatli o`zgartirildi'] . '' . CLOSE_DIV;
            }

        }

        if ($_POST['pass']) {

            $password = html($_POST['password']);
            $password2 = html($_POST['password2']);

            if (empty($password) || empty($password2)) {
                $err .= '' . $lng['Barcha maydonchalarni to`ldiring'] . '<br />';
            }

            if (strlen($password) < 6) {
                $err .= '' . $lng['Juda qisqa parol'] . '. [Min. 6]<br />';
            }

            if ($password != $password2) {
                $err .= '' . $lng['Parollar to`g`ri kelmayapti'] . '.<br />';
            }

            if ($err) {
                echo DIV_ERROR . $err . CLOSE_DIV;
            } else {
                DB::$dbs->query("UPDATE " . USERS . " SET `password` = ? WHERE `user_id` = ?", array(md5($password), $user['user_id']));
                echo DIV_MSG . '' . $lng['Parol muvaffaqiyatli o`zgartirildi'] . '' . CLOSE_DIV;
            }

        }


        echo '<div class="lines">';
        echo '' . icon('gear.png') . '  <a href="' . HOME . '/menu/anceta">' . $lng['Anketani tahrirlash'] . '</a><br/>';
        echo '' . icon('qulf.png') . ' <a href="' . HOME . '/menu/shahsiy">' . $lng['Shahsiylik'] . '</a><br/>';
        echo '' . icon('ava.png') . ' <a href="' . HOME . '/menu/photo">' . $lng['Avatar'] . '</a><br/>';
        echo '' . icon('screan.png') . ' <a href="' . HOME . '/menu/cover">' . $lng['Muqova'] . '</a>';
        echo '</div>';


        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo '' . $lng['E-Mail'] . ':<br /><input type="text" name="email"   placeholder="' . $lng['E-Mail'] . '"/><br /><br />';
        echo '<input type="submit" name="mail" value="' . $lng['Yangi E-Mail'] . '" /></form>';
        echo '<hr>';
        echo CLOSE_DIV;

        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo '' . $lng['Parol'] . ':<br /><input type="password" name="password" placeholder="' . $lng['Yangi parol'] . '"/><br /><br />';
        echo '' . $lng['Takrorlang'] . ':<br /><input type="password" name="password2"  placeholder="' . $lng['Takrorlang'] . '"/><br /><br />';
        echo '<input type="submit" name="pass" value="' . $lng['Yangi parol'] . '" /></form>';
        echo '<hr>';
        echo CLOSE_DIV;

        echo DIV_AUT;
        echo '' . $lng['Sayt tili'] . ': <br />';
        echo '<form action="#" method="post">';
        foreach ($clng as $key => $value) echo '<input type="radio" name="lng" value="' . $key . '"' . ($key == $ulng ? ' checked' : NULL) . '/> <img src="/core/lng/' . $key . '/img.png" alt="' . $key . '" /> ' . $value . '<br />';
        echo '<input type="submit" value="' . $lng['Saqlash'] . '" /></form>';
        echo CLOSE_DIV;

        break;


    case 'city':
        head('' . $lng['Shaharni izlash'] . '');


        if ($_GET['save']) {
            $id = abs(intval($_GET['save']));

            if (DB::$dbs->querySingle("SELECT COUNT(`city_id`) FROM " . CITY . " WHERE `city_id` = ?", array($id)) == FALSE) {
                echo DIV_ERROR . '' . $lng['Shahar topilmadi'] . '' . CLOSE_DIV;
            } else {
                DB::$dbs->query("UPDATE " . USERS . " SET `city` = ? WHERE `user_id` = ?", array($id, $user['user_id']));
                echo DIV_MSG . '' . $lng['Shahar muvaffaqiyatli yangilandi'] . '' . CLOSE_DIV;
            }

        }

        if ($_POST) {
            $search = html($_POST['city']);

            $all = DB::$dbs->querySingle("SELECT COUNT(`country_id`) FROM " . CITY . " WHERE `name` LIKE '%" . $search . "%'");

            if ($all) {
                echo DIV_LI;
                echo '' . $lng['Natijalar'] . ' <b>' . $all . '</b>: ' . $lng['Shahar tanlang'] . '<br />';
                $sql = DB::$dbs->query("SELECT * FROM " . CITY . " WHERE `name` LIKE '%" . $search . "%'");
                while ($city = $sql->fetch()) {
                    $country = DB::$dbs->queryFetch("SELECT `name` FROM " . COUNTRY . " WHERE `country_id` = ? LIMIT 1", array($city['country_id']));
                    $region = DB::$dbs->queryFetch("SELECT `name` FROM " . REGION . " WHERE `region_id` = ? LIMIT 1", array($city['region_id']));

                    echo ' - <a href="' . HOME . '/menu/city?save=' . $city['city_id'] . '">' . $city['name'] . ' (' . $region['name'] . ', ' . $country['name'] . ')</a><br />';
                }
                echo CLOSE_DIV;
            }
        }

        echo DIV_AUT;
        echo '<form action="#" method="POST">';
        echo '' . $lng['Shahar'] . ': ' . $lng['��������� ������'] . '<br /><input type="text" name="city" value="" /><br /><br />';
        echo '<input type="submit" value="' . $lng['Izlash'] . '" /><br />';
        echo '</form>';
        echo CLOSE_DIV;

        break;

}


require_once('core/stop.php');