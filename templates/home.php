 <!--CS 4640 Main Login site for project  -->
 <!-- Google Cloud url:  https://storage.googleapis.com/musicmirror/CS-4640/index.html -->
 <!-- Components:  -->
 <!DOCTYPE html>
 <html lang="en">
     <head> <title>MusicMirror</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
      <!-- <link rel="stylesheet" href="bootstrap.css"> -->
      <link rel="stylesheet" href="../home_styles.css">
      <link rel="stylesheet/less" type="text/css" href="../styles.less" />
         <meta charset="utf-8">
         <meta http-equiv="X-UA-Compatible" content="IE=edge">
         <meta name="viewport" content="width=device-width, initial-scale=1"> 

         <meta name="author" content="Patrick Duong">
         <meta name="description" content="Patrick Duong and Max Kouzel's CS 4640 semester project: MoodMirror.">
         <meta name="keywords" content="Patrick Duong and Max Kouzel's CS 4640 semester project: MoodMirror, which is an active music library that generates curated production-based recommendations for users. ">     

    </head>

     <body>
      
<!-- navbar with page title -->

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
                  <li class="nav-item"><a href="home.php" class="nav-link active">Home</a></li>
                  <li class="nav-item"><a href="library.php" class="nav-link">Library</a></li>
                  <li class="nav-item"><a href="reflection.php" class="nav-link">Reflection</a></li>
              </ul>
          </div>
      </div>
  </div>
</nav>
          

          <!-- MusicMirror main login hero/welcome screen  -->
<section class = "container-fluid col-12" style = "padding-top: 60px" >
  <div class="bg-dark text-secondary px-4 py-3 text-center">
      <div class="py-3">
        <h1 class="display-5 fw-bold text-white " style="font-size:4vw;">Welcome Back, <strong class = "gradient-text" style = "font-style: italic;">Username.</strong></h1>
            <div class="col-lg-6 mx-auto">
              <p class="fs-5 mb-4" style = "color: grey">Based on your recent adds, we've curated these three songs as your daily recommendations:</p>
    
            </div>
          </div>
  </div>
</section>

<!-- Song recommendations as thumbnails with supporting text, using bootstrap template-->
<div class="album py-5 bg-light">
  <div class="container">


    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
<!-- ---------------------------------------------------------- -->
<!-- song recommendation #1 -->
      <div class="col">
        <div class="card shadow-sm">
          <img class="card-img-top" src="../images/Channel_ORANGE.jpg" alt="Channel_ORANGE album cover">

          <div class="card-body mx-fixed">
            <h5 class="card-title song-title">Lost</h5>
            <h6 class="card-subtitle mb-2 text-muted">Frank Ocean</h6>
            <p class="card-text" style = "padding-bottom: 10px" >Genres: R&B/Soul, Pop, Alternative/Indie</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary">Listen</button>
                <button type="button" class="btn btn-sm btn-outline-secondary">Add to Library</button>
              </div>   
            </div>
          </div>
        </div>
      </div>

      <!-- song recommendation #2 -->
      <div class="col">
        <div class="card shadow-sm">
          <img class="card-img-top" src="../images/The_Slow_Rush_Tame.jpg" alt="Album cover of 'The Slow Rush' by Tame Impala">

          <div class="card-body mx-fixed">
            <h5 class="card-title song-title">Borderline</h5>
            <h6 class="card-subtitle mb-2 text-muted">Tame Impala</h6>
            <p class="card-text" style = "padding-bottom: 10px" >Genres: R&B/Soul, Pop, Alternative/Indie</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary">Listen</button>
                <button type="button" class="btn btn-sm btn-outline-secondary">Add to Library</button>
              </div>   
            </div>
          </div>
        </div>
      </div>

      <!-- song recommendation #3 -->
      <div class="col">
        <div class="card shadow-sm">
          <img class="card-img-top" src="../images/Flower_Boy.jpg" alt="Album cover of 'Flower Boy' by Tyler, the Creator">

          <div class="card-body mx-fixed">
            <h5 class="card-title song-title">See You Again</h5>
            <h6 class="card-subtitle mb-2 text-muted">Tyler the Creator, Kali Uchis</h6>
            <p class="card-text" style = "padding-bottom: 10px" >Genres: Hip-Hop/Rap</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary">Listen</button>
                <button type="button" class="btn btn-sm btn-outline-secondary">Add to Library</button>
              </div>   
            </div>
          </div>
        </div>
      </div>
      <!-- ----------------------------------------------------------- -->
    </div>
  </div>
</div>

     <footer class = "primary-footer row">

         <small class = "copyright">&#169; Patrick Duong and Max Kouzel.</small>

     </footer>

     <!-- JavaScript CDNs -->
     <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
     <script src="less.js-master/" ></script>

    </body>
 </html>
