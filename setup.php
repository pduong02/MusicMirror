<?php
spl_autoload_register(function($classname) {
    include "classes/$classname.php";
});

// $db = new Database();

// $db->query("drop table if exists songs;");
// $db->query("drop table if exists users;");

// // create users table
// $db->query("create table users (
//     userid int not null auto_increment,
//     email text not null,
//     name text not null,
//     password text not null,
//     primary key (userid)
// );");

// // create song table
// $db->query("create table songs (
//     songid int not null auto_increment,
//     userid int not null,
//     title text not null,
//     primary_artist text not null,
//     geniusid int not null,
//     image_url text not null,
//     primary key (songid),
//     foreign key (userid) references users(userid)
// );");

function searchGenius($title, $artist) {
    $search_term = str_replace(" ", "%20", $title . " " . $artist);

    $client_access_token = Config::$access_token;
    $genius_search_url = "http://api.genius.com/search?q={$search_term}&access_token={$client_access_token}";

    echo $genius_search_url;

    $curl = curl_init($genius_search_url);
    curl_setopt($curl, CURLOPT_URL, $genius_search_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    
    $resp = curl_exec($curl);
    curl_close($curl);
    return json_decode($resp, true);
}

function getSpotifyToken() {
    $url = "https://accounts.spotify.com/api/token";
    $headers = array(
        "Content-Type" => "application/x-www-form-urlencoded\r\n",
        "Authorization" => base64_encode(Config::$spotify_client_id.":".Config::$spotify_secret) . "\r\n"
    );

    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header'  => $headers["Content-Type"].$headers["Authorization"],
            'method'  => 'POST',
            'content' => http_build_query(array('grant-type' => 'client_credentials'))
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) { var_dump($result); }

    var_dump($result);
}

function searchSpotify($title, $artist) {
    $spotify_search_url = "https://api.spotify.com/v1/search?";

    if (!empty($title)) {
        $title = str_replace(" ", "%20", $title);
        $spotify_search_url .= "track=".$title;
        
        if (!empty($artist)) {
            $artist = str_replace(" ", "%20", $artist);
            $spotify_search_url .= "&artist=".$artist;
        }
    } else if (!empty($artist)) {
        $artist = str_replace(" ", "%20", $artist);
        $spotify_search_url .= "artist=".$artist;
    }

    $client_access_token = Config::$access_token;
    

    echo $genius_search_url;

    $curl = curl_init($genius_search_url);
    curl_setopt($curl, CURLOPT_URL, $genius_search_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, "Authorization: Bearer ");
    
    $resp = curl_exec($curl);
    curl_close($curl);
    return json_decode($resp, true);
}

function getGeniusSong($songid) {
    $client_access_token = Config::$access_token;
    $genius_search_url = "http://api.genius.com/songs/{$songid}";

    echo $genius_search_url;

    $curl = curl_init($genius_search_url);
    curl_setopt($curl, CURLOPT_URL, $genius_search_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

    $headers = array(
        "Authorization: Bearer ".Config::$access_token,
    );

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
    $resp = curl_exec($curl);
    curl_close($curl);
    return json_decode($resp, true);
}

$hits = searchGenius("I Wonder", "Kanye West")["response"]['hits'];
$id = $hits[0]['result']['id'];

echo "<pre>";

$resp = getGeniusSong($id)['response'];
$prod = $resp['song']['producer_artists'];
print_r($prod);

echo "</pre>";

