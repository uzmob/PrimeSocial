<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */

define('inc', $_SERVER['DOCUMENT_ROOT'] . '/');
date_default_timezone_set('Asia/Tashkent'); // Vaqt


/* Ma`lumotlar bazasiga ulanish */
require_once(inc . 'core/db.php');
require_once('class/pdo.php');

$debug = false;
if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

/* Sayt nomi */
define("SITE", "PrimeSocial.ru");

/* Tablitsalar */
define("USERS", "`users`"); # Foydalanuvchilar
define("COUNTRY", "`country`"); # Davlatlar
define("REGION", "`region`"); # Rayonlar
define("CITY", "`city`"); # Shaharlar
define("BANN", "`bann`"); # Ban olganlar ro`yhati
define("BLACKLIST", "`blacklist`"); # Qoora ro`yhat
define("NEWS", "`news`"); # Sayt yangiliklari
define("NEWS_COMM", "`news_comm`"); # Yangiliklarga sharhlar
define("NEWS_RATING", "`news_rating`"); # Yangiliklar reytingi
define("POSITIONS", "`positions`"); # Lavozimlar
define("PRIVILEGE", "`privilege`"); # Privlegiyalar
define("SMILES_CAT", "`smiles_cat`"); # Smayllar katologi
define("SMILES", "`smiles`"); # Smayllar
define("GUESTBOOK", "`guestbook`"); # Jonli efir
define("CHAT_ROOM", "`chat_room`"); # Chat xonasi
define("CHAT_MSG", "`chat_msg`"); # Chatdagi postlar
define("CHAT_VOPROS", "`chat_vopros`"); # Viktorina uchun savollar
define("CONFIG", "`config`"); # Sayt sozlamalari
define("FORUMS", "`forums`"); # Forum
define("FORUMS_CAT", "`forums_cat`"); # Forum bo`limlari
define("FORUM_THEME", "`forums_theme`"); # Mavzular
define("FORUM_POST", "`forums_post`"); # Sharhlar
define("FORUM_VOTE", "`forums_vote`"); # So`rovnoma
define("FORUM_RATING", "`forums_rating`"); # Sharhlarga reyting
define("FORUM_NEW_POST", "`forums_new_post`"); # O`qilmagan mavzular
define("BLOG", "`blog`"); # Blog
define("BLOG_RATING", "`blog_rating`"); # Blog reytingi
define("BLOG_COMM", "`blog_comm`"); # Blog sharhi
define("GROUPS", "`groups`"); # Guruhlar
define("GROUPS_PEOPLES", "`groups_peoples`"); # Guruh ishtrokchilari
define("GROUPS_TOPIC", "`groups_topic`"); # Guruh mavzulari
define("GROUPS_POST", "`groups_post`"); # Guruhdagi mavzular posti
define("GROUPS_NEW_POST", "`groups_new_post`"); # Guruhdagi o`qilmagan mavzular
define("GROUPS_VOTE", "`groups_vote`"); # Guruhdagi so`rovlar
define("GROUPS_APPS", "`groups_apps`");
define("LIB", "`lib`"); # Kutubxona bo`limlari
define("LIB_CAT", "`lib_cat`"); # Kutubxona ichki bo`limlari
define("LIB_ARTICL", "`lib_articl`"); # Kutubxonadagi maqolalar
define("LIB_COMM", "`lib_comm`"); # Kutubxona sharhlari
define("LOADS", "`loads`"); # Yuklamalar
define("LOADS_CAT", "`loads_cat`"); # Yuklamalar bo`limlari
define("LOADS_FILE", "`loads_file`"); # Yuklamalar fayllari
define("LOADS_SCREEN", "`loads_screen`"); # Yuklamalar skrinlari
define("LOADS_COMM", "`loads_comm`"); # Yuklamalar postlari
define("LOADS_RATING", "`loads_rating`"); # Yuklamalar reytingi
define("LOADS_FILE_DOP", "`loads_file_dop`"); # Yuklamalarning qo`shimcha fayllari
define("GUEST", "`guest`"); # Foydalanuvchi mehmonxonasi
define("GUEST_VOTES", "`guest_votes`");
define("GUEST_LIKE", "`guest_like`");
define("GUEST_COMMENTS", "`guest_comments`");
define("FILES", "`files`"); # Shahsiy fayllar bo`limlari
define("FILES_FILE", "`files_file`"); # Shahsiy fayllar
define("FILES_COMM", "`files_comm`"); # Shahsiy fayllar sharhlari
define("DIALOG", "`dialog`"); # Pochta
define("DIALOG_MSG", "`dialog_msg`"); # Pochta habarlari
define("FRIENDS", "`friends`"); # Do`slar
define("PRESENTS", "`presents`"); # Sovg`alar
define("PRESENTS_LIST", "`presents_list`"); # Sovg`alar ro`yhati
define("ALBUMS_CAT", "`albums_cat`"); # Fotoalbomlar bo`limlari
define("ALBUMS", "`albums`"); # Fotoalbomlar
define("ALBUMS_PHOTOS", "`albums_photos`"); # Fotoalbomlar fotosi
define("ALBUMS_RATING", "`albums_rating`"); # Fotoalbomlar reytingi
define("ALBUMS_COMM", "`albums_comm`"); # Fotoalbomlar sharhlari
define("FORUMS_NEW_POST", "`forums_new_post`"); # Forumning yangi sharhlari
define("FORUMS_THEME", "`forums_theme`"); # Forum mavzulari
define("FORUMS_POST", "`forums_post`"); # Forum sharhlari
define("FORUMS_VOTE", "`forums_vote`"); # Forum so`rovnomalari
define("DEY", "`dey`"); # Foydalanuvchi amallari
define("PHOTO_RATING", "`photo_rating`"); # Foto reytingi
define("PHOTO_COMM", "`photo_comm`"); # Foto sharhlari
define("STATUS", "`status`"); # Status
define("LENTA", "`lenta`"); # Tasma
define("BLACKUSERS", "`blackusers`"); # Qora ro`yhat
define("STATUS_RATING", "`status_rating`"); # Status reytingi
define("STATUS_COMM", "`status_comm`"); # Status sharhlari
define("TOUCH_USER", "`touch_user`"); # Tiket
define("TOUCH_MSG", "`touch_msg`"); # Tiket habarlari
define("SPAM", "`spam`"); # Spam
define("GUESTS", "`guests`");

/* Dizayn divlari */
define("DIV_UP", '<div class="tepa">');
define("DIV_LI", '<div class="lines">');
define("DIV_AUT", '<div class="aut">');
define("DIV_ERROR", '<div class="error">');
define("DIV_MSG", '<div class="msg">');
define("DIV_GO", '<div class="footer">');
define("DIV_BLOCK", '<div class="lines">');
define("DIV_CT", '<div class="cit">');
define("DIV_TOUCH", '<div class="touch">');
define("CLOSE_DIV", '</div>');

$sett = DB::$dbs->queryFetch("SELECT * FROM " . CONFIG . "");
$config['max_upload_photo'] = 5; // Foto yuklashning max hajmi
$config['max_upload_forum'] = 2; // Forum mavzusidagi fayl yuklashning max hajmi
$config['max_upload_guestbook'] = (empty($sett['max_upload_guestbook']) ? 1 : $sett['max_upload_guestbook']); // Jonli efirdagi fayl yuklashning max hajmi
$config['max_upload_group'] = 1; // Guruhdagi logo yuklashning max hajmi
$config['max_upload_groupа_file'] = 5; // Guruhdagi fayl yuklashning max hajmi
# Logoning yuklashdagi minimal hajmi #
$config['mini_logo_par'][0] = 100;
$config['mini_logo_par'][1] = 100;

# Fotolarning max hajmi #
$config['photo_par'][0] = 2000;
$config['photo_par'][1] = 2000;

# Fotoning mini hajmi #
$config['mini_photo_par'][0] = 100;
$config['mini_photo_par'][1] = 100;

# Kutubxonadagi fotoning mini hajmi #
$config['mini_lib_par'][0] = 100;
$config['mini_lib_par'][1] = 100;

# Sahifadagi yozuvlar №
/* Yangiliklar */
$config['write']['news'] = (empty($sett['write_news']) ? 5 : $sett['write_news']);
$config['write']['news_comm'] = (empty($sett['write_news_comm']) ? 5 : $sett['write_news_comm']);

/* Chat */
$config['write']['room'] = (empty($sett['write_room']) ? 10 : $sett['write_room']);
/* Jonli efir */
$config['write']['guestbook'] = (empty($sett['write_guestbook']) ? 5 : $sett['write_guestbook']);
/* Forum */
$config['write']['forum_post'] = (empty($sett['write_forum_post']) ? 5 : $sett['write_forum_post']);
$config['write']['forum_theme'] = (empty($sett['write_forum_theme']) ? 5 : $sett['write_forum_theme']);
$config['write']['forum_razd'] = (empty($sett['write_forum_razd']) ? 5 : $sett['write_forum_razd']);
/* Blog */
$config['write']['blog'] = (empty($sett['write_blog']) ? 5 : $sett['write_blog']);
$config['write']['blog_comm'] = (empty($sett['write_blog_comm']) ? 5 : $sett['write_blog_comm']);
$config['write']['blog_comm_view'] = (empty($sett['write_blog_comm_view']) ? 3 : $sett['write_blog_comm_view']);
/* Guruhlar */
$config['write']['groups'] = (empty($sett['write_group']) ? 10 : $sett['write_group']);
$config['write']['groups_topic_msg'] = (empty($sett['write_group_topic']) ? 5 : $sett['write_group_topic']);
$config['write']['groups_topic'] = (empty($sett['write_group_msg']) ? 5 : $sett['write_group_msg']);
/* Kutubxona */
$config['write']['lib_cat'] = (empty($sett['write_lib_cat']) ? 20 : $sett['write_lib_cat']);
$config['write']['lib_articl'] = (empty($sett['write_lib_pod_cat']) ? 10 : $sett['write_lib_pod_cat']);
$config['write']['lib_articl_str'] = (empty($sett['write_lib_simvol']) ? 1500 : $sett['write_lib_simvol']);
$config['write']['lib_comm'] = (empty($sett['write_lib_comm']) ? 5 : $sett['write_lib_comm']);
/* Yuklamalar */
$config['write']['loads_cat'] = (empty($sett['write_load_cat']) ? 20 : $sett['write_load_cat']);
$config['write']['loads_file'] = (empty($sett['write_load_files']) ? 10 : $sett['write_load_files']);
$config['write']['loads_comm'] = (empty($sett['write_load_comm']) ? 5 : $sett['write_load_comm']);
/* Mehmonxona */
$config['write']['guest'] = 5;
/* Shahsiy fayllar */
$config['write']['files_file'] = 5;
$config['write']['files_comm'] = 5;
/* Sovg`alar */
$config['write']['pay_present'] = 50; # Sovg`a baxosi
/* Fotoalbomlar */
$config['write']['album_albums'] = 5;
$config['write']['album_photos'] = 5;
$config['write']['guest_comm'] = 10;
# Botlar
$config['bot']['umnik'] = 2;

//$fo['max_size'] = 15; // Yuklamalardagi max fayl kiritish uchun hajmi 41
/* Oddiy rang */
$color = array(
    '#423189',
    '#4b0082',
    '#ff00ff',
    '#911e42',
    '#ff0000',
    '#dc143c',
    '#ffc0cb',
    '#7b3f00',
    '#ff7518',
    '#ffbf00',
    '#808000',
    '#ffff00',
    '#00ff00',
    '#ccff00',
    '#98ff98',
    '#7fffd4',
    '#4c5866',
    '#c0c0c0',
    '#808080',
    '#000000',
    '#082567',
    '#0000ff',
    '#8b00ff'
);

/* Grandient */
$gradient_1 = array(
    '000000',
    '000000',
    '000000',
    '00ff00',
    '00ff00',
    '8b00ff',
    'ffaff0'
);

$gradient_2 = array(
    'ff0000',
    '00ff00',
    '0000ff',
    '00f0f0',
    '000000',
    '000000',
    '000000'
);
?>