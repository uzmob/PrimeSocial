<?php

/**
 * @package     Prime Social
 * @link        http://primesocial.ru
 * @copyright   Copyright (C) 2016 Prime Social
 * @author      BoB | http://primesocial.ru/about
 */


function count_chat()
{
    return DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . CHAT_MSG . "");
}

function count_forum()
{
    $themes = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . FORUM_THEME . "");
    $posts = DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . FORUM_POST . "");
    return $themes . ' / ' . $posts;
}

function count_blog()
{
    return DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . BLOG . "");
}

function count_group()
{
    return DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GROUPS . "");
}

function count_lib()
{
    return DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . LIB_ARTICL . "");
}

function count_loads()
{
    return DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . LOADS_FILE . "");
}

function count_users()
{
    return DB::$dbs->querySingle("SELECT COUNT(`user_id`) FROM " . USERS . "");
}

function count_guestbook()
{
    return DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . GUESTBOOK . "");
}

function count_album()
{
    return DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . ALBUMS_PHOTOS . "");
}

function count_news()
{
    return DB::$dbs->querySingle("SELECT COUNT(`id`) FROM " . NEWS . "");
}

?>