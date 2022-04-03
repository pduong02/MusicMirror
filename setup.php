<?php
spl_autoload_register(function($classname) {
    include "classes/$classname.php";
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

// function searchGenius($title, $artist) {
//     $search_term = str_replace(" ", "%20", $title . " " . $artist);

//     $client_access_token = Config::$access_token;
//     $genius_search_url = "http://api.genius.com/search?q={$search_term}&access_token={$client_access_token}";

//     echo $genius_search_url;

//     $curl = curl_init($genius_search_url);
//     curl_setopt($curl, CURLOPT_URL, $genius_search_url);
//     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    
//     $resp = curl_exec($curl);
//     curl_close($curl);
//     return json_decode($resp, true);
// }

// $hits = searchGenius("City of Gods", "")["response"]['hits'];


// echo "<pre>";
// foreach ($hits as $hit) {
//     print_r($hit['result']);
// }

// echo "</pre>";