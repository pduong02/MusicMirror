<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1"> 

        <title>MusicMirror Add Song</title>
        
        <meta name="author" content="Max Kouzel">
        <meta name="description" content="Your personal song library, curated by MusicMirror.">
        <meta name="keywords" content="MusicMirror, library, personalized, music, songs">     
        
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        
        <link rel="stylesheet" type="text/css" href="./styles/main.css"/>
        <link rel="stylesheet/less" type="text/css" href="./styles/styles.less"/>

    </head>
    <body>

        <!-- Welcome to the sites -->
        <div class="jumbotron row">
            <div class="container col-8">
              <h1 class="gradient-text display-3" style="line-height: 1.3">Choose your song</h1>
              <p>Showing results for <?=$title?> by <?=$artist?>.</p>
            </div>
        </div>

        <?php
            if (!empty($error_msg)) {
                echo "<div class='alert alert-danger col-4'>$error_msg</div>";
            }
        ?>

        <!-- User's tracklist -->
        <div class="container-xl" id="tracklist">
            
            <?php

                // echo "<pre>";
                // print_r($hits);
                // echo "</pre>";                

                foreach ($hits as $hit) {
                    $songinfo = [
                        "image_url" => $hit['result']['header_image_url'],
                        "title" => $hit['result']['title'],
                        "artist" => $hit['result']['primary_artist']['name'],
                        "songid" => $hit['result']['id']
                    ];
                    echo "<form action=\"?action=addsong\" method=\"post\">";
                    echo "<div class='row track'>";
                    echo "<div class='col'><img src='".$hit['result']['header_image_url']."' alt='Header image for given song'></div>";
                    echo "<div class=\"col-8\">";
                    echo "<h5 class=\"song\">{$hit['result']['title']}</h5>";
                    echo "<h6 class=\"artist\">{$hit['result']['primary_artist']['name']}</h6>";
                    echo "</div>";
                    echo "<div class=\"col\">";
                    echo "<input type=\"hidden\" id=\"songid\" name=\"songinfo\" value='".json_encode($songinfo)."'>";
                    echo "<button type=\"submit\" class=\"btn btn-primary\" id=\"addsong\">Add Song</button>";
                    echo "</div>";
                    echo "</div>";
                    echo "</form>";
                }
            ?>
            
        </div>


        <!-- Copyright -->
        <footer class = "primary-footer row">

            <small class = "copyright">&#169; Patrick Duong and Max Kouzel.</small>
   
        </footer>

        
        <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        
    </body>
</html>
