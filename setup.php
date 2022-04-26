<?php
spl_autoload_register(function($classname) {
    include("classes/$classname.php");
});

$db = new Database();

$db->query("drop table if exists songs;");
$db->query("drop table if exists users;");

// create users table
$db->query("create table users (
    userid int not null auto_increment,
    email text not null,
    name text not null,
    password text not null,
    last_refresh text not null,
    last_recs text,
    primary key (userid)
);");

// create song table
$db->query("create table songs (
    songid int not null auto_increment,
    userid int not null,
    title text not null,
    primary_artist text not null,
    geniusid int not null,
    image_url text not null,
    primary key (songid),
    foreign key (userid) references users(userid)
);");

