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
            case "reflection":
                $this->getReflection();
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

    public function showLibrary() {
        $user = [
            "name" => $_SESSION["name"],
            "email" => $_SESSION["email"]
        ];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

        } else if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $songs = $this->db->query("select")
        }

        include('templates/library.php');
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
}