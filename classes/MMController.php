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
                // $this->getReflection();
                break;
            case "home":
                $this->home();
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
        session_destroy();
    }

    public function login() {
        if (isset($_POST["email"]) && !empty($_POST["email"]) && !empty($_POST["name"])) {
            $data = $this->db->query("select * from users where email = ?;", "s", $_POST["email"]);
            if ($data === false) {
                $error_msg = "Error checking for user";
            } else if (!empty($data)) {
                // returning user
                if (password_verify($_POST["password"], $data[0]["password"])) {
                    $_SESSION["name"] = $data[0]["name"];
                    $_SESSION["email"] = $data[0]["email"];
                    $_SESSION['userid'] = $data[0]["userid"];
                    header("Location: ?action=library");
                } else {
                    $error_msg = "Wrong password";
                }
            } else {
                // new user
                $insert = $this->db->query("insert into users (name, email, password) values (?, ?, ?);", 
                                                                "sss", $_POST["name"], $_POST["email"], password_hash($_POST["password"], PASSWORD_DEFAULT));
                if ($insert === false) {
                    $error_msg = "Error inserting user";
                } else {
                    $_SESSION["name"] = $_POST["name"];
                    $_SESSION["email"] = $_POST["email"];
                    $_SESSION['userid'] = $insert[0]["userid"];
                    header("Location: ?action=library");
                }
            }
        }

        include("templates/login.php");
    }
    

    public function showLibrary() {
        $user = [
            "name" => $_SESSION["name"],
            "email" => $_SESSION["email"],
            "id" => $_SESSION["userid"]
        ];

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

                header("Location: ?action=library");
                
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
            "id" => $_SESSION['userid']
        ];
        $recommendations = array();
        // generate recommendations
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // user asked to add song to library, add song then redirect back to home
            $songinfo = json_decode($_POST['songinfo'], true);
            $res = $this->db->query("insert into songs (userid, title, primary_artist, geniusid, image_url) values (?, ?, ?, ?, ?)", "issis", $user['id'], 
                                    $songinfo['title'], $songinfo['artist'], $songinfo['songid'], $songinfo['image_url']);
            if ($res === false) {
                $error_msg = "Failed to insert into songs table";
            }

            header("Location: ?action=home", true, 303);
        } else if ($_SERVER["REQUEST_METHOD"] == "GET") {
            // should be an array with 3 elements, containing title, artist, image, and genres
            $recommendations = $this->getRecommendations($user);

            if (gettype($recommendations) != 'array') {
                $error_msg = $recommendations;
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
                                    "producer" => $name
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
                            "producer" => $name
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
                            "producer" => $name
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
                            "producer" => $name
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

    private function getGeniusSong($songid) {
        $genius_search_url = "http://api.genius.com/songs/{$songid}";
    
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

}