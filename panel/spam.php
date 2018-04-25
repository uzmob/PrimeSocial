<?php

/**
* @package     Prime Social
* @link        http://primesocial.ru
* @copyright   Copyright (C) 2016 Prime Social
* @author      BoB | http://primesocial.ru/about
*/


require_once('../core/start.php');

check_auth();

if (privilegy('spam') == FALSE) {
    header("Location: ".HOME."/panel/");
    exit();
}

head(''. $lng['Spam tizimi'] .''); 

switch ($select) {
    
    default:
    $dialog = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".SPAM." WHERE `type` = ? ", array('mail'));
    echo DIV_LI . ''.icon('mail.png').' <a href="'.HOME.'/panel/spam/dialog/">'. $lng['Suhbatlar'] .'</a>'.($dialog > 0 ? ' <b>[' . $dialog .']</b>' : NULL) . CLOSE_DIV;
    break;
    
    case 'dialog':
    $all = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM ".SPAM." WHERE `type` = ? ", array('mail'));
    
    if (empty($all)) {
        echo DIV_BLOCK . ''. $lng['Ro`yhat bo`sh'] .'' . CLOSE_DIV;
    } else {
        if (!empty($_POST['send'])) {
            $post_id = abs(num($_POST['post_id']));
            $post = DB::$dbs->queryFetch("SELECT * FROM ".SPAM." WHERE `id` = ? ",array($post_id));
            
            if (empty($post)) {
                DIV_BLOCK . ''. $lng['Habarlar topilmadi'] .'' . CLOSE_DIV;
            } else {
                switch ($_POST['dey']) {
                    
                    case 1:
                    /* Удаляем сообщение */
                    DB::$dbs->query("DELETE FROM ".DIALOG_MSG." WHERE `id` = ? ",array($post['post_id']));
                    DB::$dbs->query("DELETE FROM ".SPAM." WHERE `id` = ? ",array($post['id']));
                    echo DIV_BLOCK . ''. $lng['Habarlar o`chirilgan'] .'' . CLOSE_DIV;
                    break;
                    
                    case 2:
                    /* Удалить сообщение и все похожие */
                    DB::$dbs->query("DELETE FROM ".DIALOG_MSG." WHERE `msg` LIKE '%".$post['msg']."%' && `user` = ? ",array($post['spam_user']));
                    DB::$dbs->query("DELETE FROM ".SPAM." WHERE `id` = ? ",array($post['id']));
                    echo DIV_BLOCK . ''. $lng['Habar o`chirildi'] .'' . CLOSE_DIV;
                    break;
                    
                    case 3:
                    /* Удалить все сообщение от нарушителя */
                    DB::$dbs->query("DELETE FROM ".DIALOG_MSG." WHERE `user` = ? ",array($post['spam_user']));
                    DB::$dbs->query("DELETE FROM ".SPAM." WHERE `id` = ? ",array($post['id']));
                    echo DIV_BLOCK . ''. $lng['Habar o`chirildi'] .'' . CLOSE_DIV;
                    break;
                    
                    case 4:
                    /* Разблокировать сообщение */
                    DB::$dbs->query("UPDATE ".DIALOG_MSG." SET `spam` = ? WHERE `id` = ? ",array('', $post['post_id']));
                    DB::$dbs->query("DELETE FROM ".SPAM." WHERE `id` = ? ",array($post['id']));
                    echo DIV_BLOCK . ''. $lng['Habarlar blokdan chiqarildi'] .'' . CLOSE_DIV;
                    break;
                    
                    case 5:
                    /* Разблокировать сообщение и все похожие */
                    DB::$dbs->query("UPDATE ".DIALOG_MSG." SET `spam` = ? WHERE `id` = ? && `msg` LIKE '%".$post['msg']."%'",array('', $post['post_id']));
                    DB::$dbs->query("DELETE FROM ".SPAM." WHERE `id` = ? ",array($post['id']));
                    echo DIV_BLOCK . ''. $lng['Habarlar blokdan chiqarildi'] .'' . CLOSE_DIV;
                    break;
                    
                    case 6:
                    /* Заблокировать нарушителя */
                    DB::$dbs->query("INSERT INTO ".BANN." (`user_id`, `moder`, `time_bann`, `prich`, `time`) VALUES (?, ?, ?, ?, ?)", array($post['spam_user'], $user['user_id'], (time() + 99999999), 'Рассылка спама.', time()));
                    DB::$dbs->query("DELETE FROM ".SPAM." WHERE `id` = ? ",array($post['id']));
                    echo DIV_BLOCK . ''. $lng['Foydalanuvchi muvaffaqiyatli bloklandi'] .'' . CLOSE_DIV;    
                    break;
                    
                    case 7:
                    /* Удалить нарушителя */
                    DB::$dbs->query("DELETE FROM ".USERS." WHERE `user_id` = ? ",array($post['spam_user']));
                    DB::$dbs->query("DELETE FROM ".SPAM." WHERE `id` = ? ",array($post['id']));
                    echo DIV_BLOCK . ''. $lng['Foydalanuvchi muvaffaqiyatli o`chirildi'] .'' . CLOSE_DIV;
                    break;
                    
                    case 8:
                    /* Удалить нарушителя + удалить все от него */
                    
                    # Удаление фотоальбомов/Фотографий
                    $sql = DB::$dbs->query("SELECT * FROM ".ALBUMS." WHERE `user_id` = ?", array($post['spam_user']));
                    while($album = $sql -> fetch()) {
                        $sql2 = DB::$dbs->query("SELECT * FROM ".ALBUMS_PHOTOS." WHERE `album_id` = ?", array($album['id']));
                        while($photo = $sql2 -> fetch()) {
                            @unlink('../album/'.$photo['url']);
                        }
                        DB::$dbs->query("DELETE FROM ".ALBUMS_PHOTOS." WHERE `album_id` = ? ",array($album['id']));
                    }
                    DB::$dbs->query("DELETE FROM ".ALBUMS." WHERE `user_id` = ? ",array($post['spam_user']));
                    
                    # Чистка черного списка
                    DB::$dbs->query("DELETE FROM ".BLACKUSERS." WHERE `user_id` = ? ",array($post['spam_user']));
                    
                    # Чистка в блоге
                    DB::$dbs->query("DELETE FROM ".BLOG." WHERE `user_id` = ? ",array($post['spam_user']));
                    DB::$dbs->query("DELETE FROM ".BLOG_COMM." WHERE `user_id` = ? ",array($post['spam_user']));
                    
                    # Чистка сообщений в чате
                    DB::$dbs->query("DELETE FROM ".CHAT_MSG." WHERE `user_id` = ? ",array($post['spam_user']));
                    
                    # Чистка сообщений в диалоге
                    DB::$dbs->query("DELETE FROM ".DIALOG_MSG." WHERE `user` = ? ",array($post['spam_user']));

                    # Удаление тем на форуме
                    DB::$dbs->query("DELETE FROM ".FORUMS_THEME." WHERE `user_id` = ? ",array($post['spam_user']));
                                        
                    # Чистка сообщений на форуме
                    DB::$dbs->query("DELETE FROM ".FORUMS_POST." WHERE `user_id` = ? ",array($post['spam_user']));
                    
                    # Чистка сообщений в беседке
                    DB::$dbs->query("DELETE FROM ".GUESTBOOK." WHERE `user_id` = ? ",array($post['spam_user']));
                    
                    # Чистка сообщений в личных гостевых
                    DB::$dbs->query("DELETE FROM ".GUEST." WHERE `autor_id` = ? ",array($post['spam_user']));
                    
                    # Удаление пользователя
                    DB::$dbs->query("DELETE FROM ".USERS." WHERE `user_id` = ? ",array($post['spam_user']));
                    
                    DB::$dbs->query("DELETE FROM ".SPAM." WHERE `id` = ? ",array($post['id']));
                    
                    echo DIV_BLOCK . ''. $lng['Muvaffaqiyatli bajarildi'] .'!' . CLOSE_DIV;
                    break;

                    case 9:
                    /* Удаление уведомления */
                    DB::$dbs->query("DELETE FROM ".SPAM." WHERE `id` = ? ",array($post['id']));
                    echo DIV_BLOCK . ''. $lng['Bildirishnoma muvaffaqiyatli o`chirildi'] .'' . CLOSE_DIV;
                    break;
                                        
                    default:
                    echo DIV_BLOCK . ''. $lng['Notanish operatsiya'] .'' . CLOSE_DIV;
                    break;
                    
                }
            }
        }
        $n = new Navigator($all,5,'');
        $sql = DB::$dbs->query("SELECT * FROM ".SPAM." WHERE `type` = ? ORDER BY `id` DESC LIMIT {$n->start()}, 5", array('mail'));
        while($post = $sql -> fetch()){
            echo DIV_BLOCK;
            echo '<b>'. $lng['Habarga arz qilish'] .':</b><br />' . text($post['msg']) . '<br /><br />'
            . '<b>'. $lng['Bildirdi'] .':</b> ' . user_choice($post['user_id'], 'link') . '<br />'
            . '<b>'. $lng['Qoidabuzar'] .':</b> ' . user_choice($post['spam_user'], 'link') . '<br />'
            . '<b>'. $lng['Sana'] .':</b> ' . vrem($post['time']) . '<br /><br />'
            . '<form action="#" method="POST">'
            . '<select name="dey">'
            . '<option value="0">'. $lng['Amalni tanlang'] .'</option>'
            . '<option value="1">'. $lng['Habarni o`chirish'] .'</option>'
            . '<option value="2">'. $lng['Habarni va shunga o`hshashlarni o`chirish'] .'</option>'
            . '<option value="3">'. $lng['Qoidabuzarning hamma habarlarini o`chirish'] .'</option>'
            . '<option value="4">'. $lng['Habarni blokdan chiqarish'] .'</option>'
            . '<option value="5">'. $lng['Habarni va shunga o`hshashlarni blokdan chiqarish'] .'</option>'
            . '<option value="6">'. $lng['Qoidabuzarni bloklash'] .'</option>'
            . '<option value="7">'. $lng['Qoidabuzarni o`chirish'] .'</option>'
            . '<option value="8">'. $lng['Qoidabuzarni o`chirish'] .'+ '. $lng['undagi hammasini o`chirish'] .'</option>'
            . '<option value="9">'. $lng['Tizim'] .'Bildirishnomani o`chirish</option>'
            . '</select><br />'
            . '<input type="hidden" name="post_id" value="'.$post['id'].'"/>'
            . '<input type="submit" name="send" value="'. $lng['Davomi'] .'" />'
            . '</form>';
            echo CLOSE_DIV;
        }
        echo $n->navi();        
    }
    break;
    
}   
echo '<div class="white"> - <a href="/panel/">'. $lng['Apanel'] .'</a></div>';
require_once('../core/stop.php');
?>