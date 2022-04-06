<?php

class MMController {
    private $db;
    private $action;

    public function __construct($action) {
        $this->db = new DataBase();
        $this->action = $action;
    }

    public function run() {
        switch ($this->action) {
            case "library":
                $this->showLibrary();
                break;
            case "addsong":
                $this->addSong();
                break;
            case "reflection":
                $this->getReflection();
                break;
            case "home":
                $this->home();
                break;
            case "newuser":
                $this->initializeLibrary();
                break;
            case "logout":
                $this->destroySession();

            case "login":
                // check email with regex
                // store user name/email in cookie
            default:
                $this->login();                
        }
    }

    public function destroySession() {
        session_unset();
        session_destroy();
        session_start();
    }

    public function login() {
        if (isset($_POST["email"]) && isset($_POST["name"]) && $this->validateLogin($_POST)) {
            setcookie("email",$_POST["email"],time()+3600);
            setcookie("name",$_POST["name"],time()+3600);

            $data = $this->db->query("select * from users where email = ?;", "s", $_POST["email"]);
            if ($data === false) {
                $error_msg = "Error checking for user";
            } else if (!empty($data)) {
                // returning user
                if (password_verify($_POST["password"], $data[0]["password"])) {
                    $_SESSION["name"] = $data[0]["name"];
                    $_SESSION["email"] = $data[0]["email"];
                    $_SESSION['userid'] = $data[0]["userid"];
                    $_SESSION["last_refresh"] = $data[0]["last_refresh"];
                    header("Location: ?action=home");
                } else {
                    $error_msg = "Wrong password";
                }
            } else {
                // new user
                $insert = $this->db->query("insert into users (name, email, password, last_refresh) values (?, ?, ?, ?);", "ssss", $_POST["name"], 
                                            $_POST["email"], password_hash($_POST["password"], PASSWORD_DEFAULT), date('Y-m-d', strtotime("-1 week")));
                if ($insert === false) {
                    $error_msg = "Error inserting user";
                } else {
                    $getid = $this->db->query("select userid from users where email = ?", "s", $_POST['email']);
                    if ($getid === false) {
                        $error_msg = "Unable to retrieve userid after inserting new user";
                    }

                    $_SESSION["name"] = $_POST["name"];
                    $_SESSION["email"] = $_POST["email"];
                    $_SESSION['userid'] = $getid[0]['userid'];
                    $_SESSION['last_refresh'] = date('Y-m-d', strtotime("-1 week"));
                    header("Location: ?action=newuser");
                }
            }
        } else if (isset($_POST['email']) || isset($_POST['name'])) {
            $error_msg = "Invalid login. Please provide a valid name and email.";
        }

        include("templates/login.php");
    }

    private function validateLogin($post) {
        if ($this->validateEmail($post['email'])) {
            if ($this->validateName($post['name'])) {
                return true;
            }
        }
        return false;
    }

    private function validateName($name) {
        $regex = "/[A-Za-z\ \']*/";

        if (!empty($name)) {
            if (preg_match($regex, $name)) {
                return true;
            }
        }
        return false;
    }

    private function validateEmail($email) {
        $standard="/^[A-Za-z0-9\-\_\+][A-Za-z0-9\-\_\+\.]*[A-Za-z0-9\-\_\+]@[A-Za-z0-9\-\.]+\.[A-Za-z0-9\-\.]+/";
        
        if (!empty($email)) {
            if (preg_match($standard, $email)) {
                if (!empty($regex)) {
                    if (preg_match($regex, $email)) {
                        return true;
                    } else {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }
    
    public function initializeLibrary() {
        $user = [
            "name" => $_SESSION["name"],
            "email" => $_SESSION["email"],
            "id" => $_SESSION["userid"]
        ];

        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            $library = $this->db->query("select * from songs where userid = ?", "i", $user['id']);

            if ($library === false) {
                $error_msg = "Failed to fetch user's library from user table";
            }

            if (count($library) >= 10) {
                unset($_SESSION['fromNewUser']);
                header("Location: ?action=home");
            }
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $hits = $this->searchGenius($_POST["form-songtitle"], $_POST["form-artist"]);
            
            if ($hits === false) {
                $error_msg = "Failed to make genius request.";
            }
            
            $_SESSION["hits"] = $hits['response']['hits'];
            $_SESSION["title"] = $_POST["form-songtitle"];
            $_SESSION["artist"] = $_POST["form-artist"];
            $_SESSION['fromNewUser'] = true;

            header("Location: ?action=addsong");
        }

        include("templates/newuser.php");
    }

    public function showLibrary() {
        $user = [
            "name" => $_SESSION["name"],
            "email" => $_SESSION["email"],
            "id" => $_SESSION["userid"]
        ];
        // echo $_SESSION['fromNewUser'];
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $hits = $this->searchGenius($_POST["form-songtitle"], $_POST["form-artist"]);
            
            if ($hits === false) {
                $error_msg = "Failed to make genius request.";
            }
            
            $_SESSION["hits"] = $hits['response']['hits'];
            $_SESSION["title"] = $_POST["form-songtitle"];
            $_SESSION["artist"] = $_POST["form-artist"];

            header("Location: ?action=addsong", true, 303);
        } else if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $songs = $this->db->query("select * from songs where userid = ?", "i", $user['id']);

            if ($songs===false) {
                $error_msg = "Failed to select songs from database";
            }
        }

        include('templates/library.php');
    }

    public function addSong() {
        $user = [
            "name" => $_SESSION["name"],
            "email" => $_SESSION["email"],
            "id" => $_SESSION['userid']
        ];

        // echo print_r($user);

        if (!isset($_SESSION["hits"])) {
            $error_msg = "Hits not stored properly.";
            header("Location: ?action=library");
        } else if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $hits = $_SESSION["hits"];
            $title = $_SESSION["title"];
            $artist = $_SESSION["artist"];

            include("templates/addsong.php");
        } else { // POST request
            if (isset($_POST['songinfo'])) {
                $songinfo = json_decode($_POST['songinfo'], true);
                $res = $this->db->query("insert into songs (userid, title, primary_artist, geniusid, image_url) values (?, ?, ?, ?, ?)", "issis", $user['id'], 
                                        $songinfo['title'], $songinfo['artist'], $songinfo['songid'], $songinfo['image_url']);
                if ($res === false) {
                    $error_msg = "Failed to insert into songs table";
                }

                if (isset($_SESSION['fromNewUser']) && $_SESSION['fromNewUser']) {
                    header("Location: ?action=newuser");
                } else {
                    header("Location: ?action=library");
                }
                
            } else {
                $error_msg = "Did not successfully encode song info";
                echo $error_msg;
            }
        }
    }

    private function searchGenius($title, $artist) {
        $search_term = str_replace(" ", "%20", $title . " " . $artist);

        $client_access_token = Config::$access_token;
        $genius_search_url = "http://api.genius.com/search?q={$search_term}&access_token={$client_access_token}";

        $curl = curl_init($genius_search_url);
        curl_setopt($curl, CURLOPT_URL, $genius_search_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        
        $resp = curl_exec($curl);
        curl_close($curl);
        return json_decode($resp, true);
    }

    public function home() {
        $user = [
            "name" => $_SESSION["name"],
            "email" => $_SESSION["email"],
            "id" => $_SESSION['userid'],
            "last_refresh" => $_SESSION['last_refresh']
        ];
        $recommendations = array();
        // generate recommendations
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // user asked to add song to library, add song then redirect back to home
            $songinfo = json_decode($_POST['songinfo'], true);
            $res = $this->db->query("insert into songs (userid, title, primary_artist, geniusid, image_url) values (?, ?, ?, ?, ?)", "issis", $user['id'], 
                                    $songinfo['title'], $songinfo['primary_artist'], $songinfo['songid'], $songinfo['image_url']);
            if ($res === false) {
                $error_msg = "Failed to insert into songs table";
            }

            $recsjson = $this->db->query("select last_recs from users where userid = ?", "i", $user['id']);

            if ($recsjson === false) {
                $error_msg = "Failed to extract previous recommendations from user table.";
            }

            $recommendations = json_decode($recsjson[0]['last_recs'], true);

            if (($key = array_search($songinfo, $recommendations)) !== false) {
                unset($recommendations[$key]);
                $update = $this->db->query("update users set last_recs = ? where userid = ?", "si", json_encode($recommendations), $user['id']);
                if ($update === false) {
                    $error_msg = "Failed to update last recs in user table";
                }
            }

            header("Location: ?action=home", true, 303);
        } else if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $now = strtotime(date('Y-m-d'));
            $last = strtotime($user['last_refresh']);

            // been at least a day since last refreshed the recommendations
            if (abs($now - $last)/(60*60*24) >= 1) {
                // should be an array with 3 elements, containing title, artist, image, and genres
                $recommendations = $this->getRecommendations($user);
                if (gettype($recommendations) != 'array') {
                    $error_msg = $recommendations;
                } else {
                    $_SESSION["last_refresh"] = date('Y-m-d');
                    $insert = $this->db->query("update users set last_recs = ?, last_refresh = ? where userid = ?", "ssi", 
                                                json_encode($recommendations), date('Y-m-d'), $user['id']);
                    if ($insert === false) {
                        $error_msg = "Failed to update recommendations in user table";
                    }
                }
            } else {
                $recsjson = $this->db->query("select last_recs from users where userid = ?", "i", $user['id']);

                if ($recsjson === false) {
                    $error_msg = "Failed to extract previous recommendations from user table.";
                }

                $recommendations = json_decode($recsjson[0]['last_recs'], true);
            }
        }

        include("templates/home.php");
    }

    private function getRecommendations($user) {
        // generate recommendations based on producer, genres, etc.
        // get user library
        $songs = $this->db->query("select * from songs where userid = ?", "i", $user['id']);
        $producers = array();

        if ($songs === false) {
            $error_msg = "Failed to select songs from database.";
            return $error_msg;
        }
        $titles = array();
        foreach ($songs as $song) {
            array_push($titles, $song['title']);
            $songinfo = $this->getGeniusSong($song['geniusid']);

            if ($songinfo['meta']['status'] != 200) {
                $error_msg = "Failed to connect to Genius API with status code " . $songinfo['meta']['status'] . " on song " . $song['geniusid'];
                return $error_msg;
            }

            foreach ($songinfo['response']['song']['producer_artists'] as $producer) {
                $name = $producer['name'];
                if (isset($producers[$name])) {
                    $producers[$name]+=1;
                } else {
                    $producers[$name] = 1;
                }
            }
        }

        arsort($producers);

        // echo "<pre>";
        // print_r($producers);
        // echo "</pre>";

        $recs = array(); // will contain all info the home page will need to display the songs

        // recommend the top result for the top 3 producers in the user's library
        if (count($producers) >= 3) {
            foreach ($producers as $name => $count) {
                if (count($recs) < 3) {
                    $hits = $this->searchGenius("", $name);
                    if ($hits['meta']['status'] != 200) {
                        $error_msg = "Error searching Genius API with status code ".$hits['meta']['status'];
                        return $error_msg;
                    } else {
                        // find a result in hits that is not already in the user's library
                        $i = 0;
                        $results = $hits['response']['hits'];
                        while ($i < count($results)) {
                            // if result not in user's library already
                            if (!in_array($results[$i]['result']['title'], $titles)) {
                                // info: image url, title, artist, producer, where to listen
                                $img_url = $results[$i]['result']['song_art_image_url'];
                                $t = $results[$i]['result']['title'];
                                $a = $results[$i]['result']['primary_artist']['name'];
                                $genius_url = $results[$i]['result']['url']; // links to Genius page
                                array_push($recs, array(
                                    "image_url" => $img_url,
                                    "title" => $t,
                                    "primary_artist" => $a,
                                    "genius_url" => $genius_url,
                                    "producer" => $name,
                                    "songid" => $results[$i]['result']['id']
                                ));
                                break;
                            }
                            $i++;
                        }
                    }
                } else {
                    break;
                }
            }
        } else if (count($producers) == 2) {
            $prod1 = array_key_first($producers);
            $prod2 = array_key_last($producers);

            // add 2 songs from first producer, 1 from the second
            $hits = $this->searchGenius("", $prod1);
            if ($hits['meta']['status'] != 200) {
                $error_msg = "Error searching Genius API with status code ".$hits['meta']['status'];
                return $error_msg;
            } else {
                // find a result in hits that is not already in the user's library
                $i = 0;
                $results = $hits['response']['hits'];
                while ($i < count($results) && count($recs) <= 2) {
                    // if result not in user's library already
                    if (!in_array($results[$i]['result']['title'], $titles)) {
                        // info: image url, title, artist, producer, where to listen
                        $img_url = $results[$i]['result']['song_art_image_url'];
                        $t = $results[$i]['result']['title'];
                        $a = $results[$i]['result']['primary_artist']['name'];
                        $genius_url = $results[$i]['result']['url']; // links to Genius page
                        array_push($recs, array(
                            "image_url" => $img_url,
                            "title" => $t,
                            "primary_artist" => $a,
                            "genius_url" => $genius_url,
                            "producer" => $name,
                            "songid" => $results[$i]['result']['id']
                        ));
                    }
                    $i++;
                }
            }

            $hits = $this->searchGenius("", $prod2);
            if ($hits['meta']['status'] != 200) {
                $error_msg = "Error searching Genius API with status code ".$hits['meta']['status'];
                return $error_msg;
            } else {
                // find a result in hits that is not already in the user's library
                $i = 0;
                $results = $hits['response']['hits'];
                while ($i < count($results)) {
                    // if result not in user's library already
                    if (!in_array($results[$i]['result']['title'], $titles)) {
                        // info: image url, title, artist, producer, where to listen
                        $img_url = $results[$i]['result']['song_art_image_url'];
                        $t = $results[$i]['result']['title'];
                        $a = $results[$i]['result']['primary_artist']['name'];
                        $genius_url = $results[$i]['result']['url']; // links to Genius page
                        array_push($recs, array(
                            "image_url" => $img_url,
                            "title" => $t,
                            "primary_artist" => $a,
                            "genius_url" => $genius_url,
                            "producer" => $name,
                            "songid" => $results[$i]['result']['id']
                        ));
                        break;
                    }
                    $i++;
                }
            }
        } else {
            $prod = array_key_first($producers);

            // add 3 songs from top producer
            $hits = $this->searchGenius("", $prod);
            if ($hits['meta']['status'] != 200) {
                $error_msg = "Error searching Genius API with status code ".$hits['meta']['status'];
                return $error_msg;
            } else {
                // find a result in hits that is not already in the user's library
                $i = 0;
                $results = $hits['response']['hits'];
                while ($i < count($results) && count($recs) <= 3) {
                    // if result not in user's library already
                    if (!in_array($results[$i]['result']['title'], $titles)) {
                        // info: image url, title, artist, producer, where to listen
                        $img_url = $results[$i]['result']['song_art_image_url'];
                        $t = $results[$i]['result']['title'];
                        $a = $results[$i]['result']['primary_artist']['name'];
                        $genius_url = $results[$i]['result']['url']; // links to Genius page
                        array_push($recs, array(
                            "image_url" => $img_url,
                            "title" => $t,
                            "primary_artist" => $a,
                            "genius_url" => $genius_url,
                            "producer" => $name,
                            "songid" => $results[$i]['result']['id']
                        ));
                    }
                    $i++;
                }
            }
        }
        // echo "<pre>";
        // print_r($recs);
        // echo "</pre>";
        return $recs;
    }

    //return artists ranked by # of appearances: 
    public function artistRanking(){
        $userid = $_SESSION['userid'];
        $artists = $this->db->query("select primary_artist, count(*) as count FROM songs where userid = $userid group by primary_artist");

        usort($artists, function ($artist1, $artist2) {
            return $artist2['count'] <=> $artist1['count'];
        });

        // echo print_r($artists);
        return $sortedArtists = $artists;
    }

    //return songs grouped by age: fed the $user_songs array with producer-year pairs
    public function ageGrouping($user_songs){
        $years = array();
        $twenty20s = 0;
        $twenty10s = 0;
        $twenty0s= 0;
        $nineteen90s = 0;
        $nineteen80s= 0;
        $oldies = 0;

        foreach ($user_songs as $started){
            $year = explode("-",$started[0])[0];
            if (intval($year) >= 2020){
                $twenty20s ++;
            }
            else if (intval($year) >= 2010){
                $twenty10s ++;
            }
            else if (intval($year) >= 2000){
                $twenty0s ++;
            }
            else if (intval($year) >= 1990){
                $nineteen90s ++;
            }
            else if (intval($year) >= 1980){
                $nineteen80s ++;
            }
            else if (intval($year) < 1980){
                $oldies++;
            }

        }

        $years["2020s"] = $twenty20s;
        $years["2010s"] = $twenty10s;
        $years["2000s"] = $twenty0s;
        $years["1990s"] = $nineteen90s;
        $years["1980s"] = $nineteen80s;
        $years["oldies"] = $oldies;

        return $years;
    }

    public function ageMessage($years){
        $msg = "";

        // echo array_search(max($years),$years);
        // since you have _ songs
        switch(array_search(max($years),$years)){
            case "2020s":
                $interval = "2020s";
                $count = $years["2020s"];
                $msg = "With <strong>$count</strong> songs from the $interval, we'd call you hip with the times.";
                break;
            case "2010s":
                $interval = "2010s";
                $count = $years["2010s"];
                $msg = "With <strong>$count</strong> songs from the $interval, your vibes are pretty modern.";
                break;
            case "2000s":
                $interval = "2000s";
                $count = $years["2000s"];
                $msg = "With <strong>$count</strong> songs from the $interval, it's clear you love your throwbacks.";
                break;  
            case "1990s":
                $interval = "1990s";
                $count = $years["1990s"];
                $msg = "With <strong>$count</strong> songs from the $interval, you're getting jiggy with it!";
                break;         
            case "1980s":
                $interval = "1980s";
                $count = $years["1980s"];
                $msg = "With <strong>$count</strong> songs from the $interval, your music taste is delightfully retro.";
                break;
            case "oldies":
                $interval = "oldies";
                $count = $years["oldies"];
                $msg = "With <strong>$count</strong> songs from the $interval (pre-1980s), you don't hesitate to give the OG songs their flowers.";
                break;
        }

        return $msg;
    }

    //return songs grouped by age: fed the $user_songs array with producer-year pairs
    public function viewGrouping($user_songs){
        $views = array();
        $zero10k= 0;
        $ten50k = 0;
        $fifty100k= 0;   
        $hund250k = 0;
        $twohund500k= 0;
        $fivehund1mil = 0;
        $overmil = 0;

        foreach ($user_songs as $song){
            $view = $song[2];
            // echo($view);
            if (intval($view) < 10000){
                $zero10k++;
                // echo "happening";
            }
            else if (intval($view) >= 10000 and intval($view)<50000){
                $ten50k ++;
                // echo "happening";

            }
            else if (intval($view) >= 50000 and intval($view)<100000){
                $fifty100k ++;
                // echo "happening";

            }
            else if (intval($view) >= 100000 and intval($view)<250000){
                $hund250k++;
                // echo "happening";

            }
            else if (intval($view) >= 250000 and intval($view)<500000){
                $twohund500k ++;
                // echo "happening";

            }
            else if (intval($view) >= 500000 and intval($view)<1000000){
                $fivehund1mil ++;
                // echo"happening";

            }
            else if (intval($view) >= 1000000){
                $overmil ++;
                // echo "happening";

            }

        }

        $views["lowk"] = $zero10k;
        $views["midk"] = $ten50k;
        $views["upperk"] = $fifty100k;
        $views["lowhundk"] = $hund250k;
        $views["midhundk"] = $twohund500k;
        $views["uphundk"] = $fivehund1mil;
        $views["overmil"] = $overmil;

        return $views;
    }


   function getGeniusSong($songid) {
        $client_access_token = Config::$access_token;
        $genius_search_url = "http://api.genius.com/songs/{$songid}";

        // echo $genius_search_url;

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

    //return array: {producer: {launch year, issampled, viewcount}}
    function userSongs(){
        $userid = $_SESSION['userid'];
        $songs = $this->db->query("select * FROM songs where userid = $userid");
        $songs_info = array(); 
        foreach($songs as $song){
            $result = $this->getGeniusSong($song["geniusid"])['response']['song'];

            //check if sampled
            if ($result["song_relationships"][1]["songs"] !== NULL){
                $isSampled = TRUE;
            }
            else{
                $isSampled = FALSE;
            }

            //check if pageviews = 0
            if (!isset($result["stats"]["pageviews"])){
                // echo $result["title"];
                $result["stats"]["pageviews"] = 0;
            }
            $songs_info[$result["producer_artists"][0]['name']] = [$result["release_date"],$isSampled, $result["stats"]["pageviews"]];
        }

        // echo print_r($songs_info);
        return $songs_info;

    }


    public function getReflection() {
        $data_val = 30;
        $js_out_dval = json_encode($data_val);
        $userSongs = $this->userSongs();
        // echo print_r($userSongs);
    
        #return top artists: 
        $sortedArtists = $this->artistRanking();
        $top_artist = $sortedArtists[0]["primary_artist"];
        $top_count = $sortedArtists[0]["count"];
    
        #return song age groupings: 
        $years = $this->ageGrouping($userSongs);
        $age_msg = $this->ageMessage($years);
        
        #randomly select a producer in the rotation, hype them up: 
        $producer = array_rand($userSongs,1);

        #percentage of songs sampled: 
        $sampleCount = 0;
        $smplmsg = "";
        foreach($userSongs as $song){
            if($song[1] == 1){
                $sampleCount ++; 
            }
        }
        $samplePercent = 100 * round($sampleCount/count($userSongs), 2);
        if ($samplePercent >= 50){
            $smplmsg = "similar";
        }
        else{
            $smplmsg = "somewhat different";
        }
        
        #song popularity breakdown by views
        $views = $this->viewGrouping($userSongs);
        // echo var_dump($views);

    
        include('templates/reflection.php');
       }

}