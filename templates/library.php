<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1"> 

        <title>MusicMirror Library</title>
        
        <meta name="author" content="Max Kouzel">
        <meta name="description" content="Your personal song library, curated by MusicMirror.">
        <meta name="keywords" content="MusicMirror, library, personalized, music, songs">     
        
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        
        <link rel="stylesheet" type="text/css" href="./styles/main.css"/>
        <link rel="stylesheet/less" type="text/css" href="./styles/styles.less"/>

    </head>
    <body>

        <!--- Navigation between sites -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="home.php" style="font-weight: bolder">MusicMirror.</a>
                <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample05" aria-controls="navbarsExample05" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
        
                <div class="collapse navbar-collapse me-auto" id="navbarsExample05" style="float:right;">
                    <div class="ms-auto">
                        <ul class="navbar-nav">
                            <li class="nav-item"><a href="home.php" class="nav-link">Home</a></li>
                            <li class="nav-item"><a href="library.php" class="nav-link active">Library</a></li>
                            <li class="nav-item"><a href="reflection.php" class="nav-link">Reflection</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Welcome to the sites -->
        <div class="jumbotron row">
            <div class="container col-8">
              <h1 class="gradient-text display-3" style="line-height: 1.3">Your Library</h1>
              <p>Welcome to your unique music playlist, curated just for you.</p>
            </div>
        </div>

        <?php
            if (!empty($error_msg)) {
                echo "<div class='alert alert-danger col-4'>$error_msg</div>";
            }

        ?>

        <!-- Button to add a song to the playlist; clicking brings up a offcanvas element with a search form -->
        <div class="container" id="addSongContainer">
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTop" aria-controls="offcanvasTop">Add a song</button>

            <div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasTop" aria-labelledby="offcanvasTopLabel">
                <div class="offcanvas-header">
                    <h5 id="offcanvasTopLabel">Search for a song</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <form action="?action=library" method="post">
                        <div class="form-group">
                            <label for="form-songtitle">Title</label>
                            <input type="text" class="form-control" id="form-songtitle" name="form-songtitle" placeholder="Enter song title">
                        </div>
                        <div class="form-group">
                            <label for="form-artist">Artist</label>
                            <input type="text" class="form-control" id="form-artist" name="form-artist" placeholder="Enter artist">
                        </div>
                        <button type="submit" class="btn btn-primary" id="search-submit">Search</button>
                    </form>
                </div>
            </div>  
        </div>

        <!-- User's tracklist -->
        <div class="container-xl" id="tracklist">
            <?php
                foreach ($songs as $song) {
                    echo "<div class='row track'>";
                    echo "<div class='col'><img src='".$song['image_url']."' alt='Header image for given song'></div>";
                    echo "<div class=\"col-8\">";
                    echo "<h5 class=\"song\">{$song['title']}</h5>";
                    echo "<h6 class=\"artist\">{$song['primary_artist']}</h6>";
                    echo "</div>";
                    echo "<div class=\"col\">";
                    echo "<h5 class=\"runtime\">{$song['geniusid']}</h5>";
                    echo "</div>";
                    echo "</div>";
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
