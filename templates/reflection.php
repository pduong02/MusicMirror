 <!-- The components I added and their tabs: [Index] - navigation bar at the top, [Index] - buttons in the navbar, [About] - card with image (the banner), [Experiences]- accordion with highlights, [Index] - carousel with photos that represent me -->
 
 <!DOCTYPE html>
 <html lang="en">
     <head> <title>MusicMirror</title>
     <link rel="stylesheet" href="./styles/home_styles.css">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/charts.css/dist/charts.min.css">
      <!-- <link rel="stylesheet" href="bootstrap.css"> -->
      <!-- <link rel="stylesheet" href="./styles/home_styles.css"> -->
      <link rel="stylesheet" href="./styles/reflection_styles.css">

      <link rel="stylesheet/less" type="text/css" href="./styles.less" />
         <meta charset="utf-8">
         <meta http-equiv="X-UA-Compatible" content="IE=edge">
         <meta name="viewport" content="width=device-width, initial-scale=1"> 

         <meta name="author" content="Patrick Duong and Max Kouzel">
         <meta name="description" content="Patrick Duong and Max Kouzel's CS 4640 semester project: MoodMirror.">
         <meta name="keywords" content="Patrick Duong and Max Kouzel's CS 4640 semester project: MoodMirror, which is an active music library that generates curated production-based recommendations for users. ">     

    </head>

     <body>
      
<!-- navbar with page title -->

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="home.php" style="font-weight: bolder">MusicMirror.</a>
        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample05" aria-controls="navbarsExample05" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse me-auto" id="navbarsExample05" style="float:right;">
            <div class="ms-auto">
                <ul class="navbar-nav">
                  <li class="nav-item"><a href="?action=home" class="nav-link">Home</a></li>
                  <li class="nav-item"><a href="?action=library" class="nav-link ">Library</a></li>
                  <li class="nav-item"><a href="?action=reflection" class="nav-link active">Reflection</a></li>
                                  <li class="nav-item"><a href="?action=logout" class="nav-link">Logout</a></li>

                </ul>
            </div>
        </div>
    </div>
  </nav>

  <!-- first section with a paragraph element and the pie chart next to it  -->
<div class = "row p-5" style = "padding-top: 50px; padding-bottom: 50px">
    <h1 id = "reflection-intro">
        This is your <strong class = "gradient-text">Reflection</strong>, <?=$_SESSION["name"]?>.</strong>
  </h1>
    <p style = "font-size: 1.5vw; padding-left: 10px; padding-top: 20px">
        We've created this tool to give you a closer look at your musical personality and find the insights that you've been looking for. 
        As you explore, try to learn more about your top genres, artists, and listening patterns. Cheers! 
    </p>
    <div style = "margin:auto">
      <h5 style = "font-size: 1.5vw; padding-left: 10px; padding-top: 20px"> -> One thing's clear: you've got <strong style = "height:25% "><?=$top_artist?></strong> on your mind with <strong><?=$top_count?></strong> songs from them in your library.</h5>
      <h5 style = "font-size: 1.5vw; padding-left: 10px; padding-top: 20px"> -> Let's show some love to the engineers. You've got <strong><?=$producer?>'s</strong> productions in your rotation!</h5>
      <h5 style = "font-size: 1.5vw; padding-left: 10px; padding-top: 20px"> -> <?=$age_msg?></h5>
      <h5 style = "font-size: 1.5vw; padding-left: 10px; padding-top: 20px"> -> With <?=$samplePercent?>% of your songs being sampled, we'd say your music taste is <?=$smplmsg?> to the producers out there.</h5>
    </div>

    <!-- <h5 style = "font-size: 1.5vw; padding-left: 10px; padding-top: 20px"> Let's give some love to the engineers. You've got <strong><?=$producer?>'s productions in your rotation. That's some great taste right there.</strong>.</h5> -->


</div>

<!-- next section with more advanced insights: 
<div class = "col-10 p-5">
</div> -->


<!-- beginning of the graphs  -->
<section class = "container-fluid col-12 row-cols-3" style = "padding-top: 50px">
<!-- chart canvas instantiation (from Chart.js) -->
    <div class = "chart-container col-4">
        <canvas id="donut" aria-label="Donut graph of genres" role="img"></canvas>
    </div>
     <!-- <div class = "chart-container col-4"> 
        <canvas id="radar" aria-label="Radar graph of genres" role="canvas"></canvas>
    </div> -->

    <div class = "chart-container col-4" >
        <canvas id="polar" aria-label="Radar graph of genres" role="canvas"></canvas>
    </div> 
    
</section>


  <!-- chart cdn  -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  
<!-- donut graph of musical activities -->
<section class = "container">
    <script style = "height: 200px">
        //initialize data vals: get years from getReflection(), from ageGrouping()
        var twenty20s = <?= $years["2020s"]; ?>;
        var twenty10s = <?= $years["2010s"]; ?>;
        var twenty0s = <?= $years["2000s"]; ?>;
        var nineteen90s = <?= $years["1990s"]; ?>;
        var nineteen80s = <?= $years["1980s"]; ?>;
        var oldies = <?= $years["oldies"]; ?>;



        // set up the chart
        const donut_data = {
        labels: [
          '2020s',
          '2010s',
          '2000s',
          '1990s',
          '1980s',
          'Oldies',

        ],
        datasets: [{
          label: 'My First Dataset',
          data: [twenty20s, twenty10s, twenty0s, nineteen90s,nineteen80s,oldies ],
          backgroundColor: [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(259, 105, 186)',
            'rgb(209, 205,26)',
            'rgb(102, 205, 44)',
            'rgb(235, 15, 209)'

          ],
          hoverOffset: 4
        }]
      };
    
      //render the data 
    const donut_config = {
        type: 'doughnut',
        data: donut_data,

        options: {
        plugins: {
            title: {
                display: true,
                text: 'Age Breakdown'
            }
        }
    }

      };


    </script>
  </section>

  <!-- rendering for donut chart -->
  <script>      
    const donut = new Chart(
    document.getElementById('donut'),
    donut_config
  );</script>









<!-- //polar area chart of musical activity -->
<section class = "container">
    <script>
        var zero10k = <?= $views["lowk"]; ?>;
        var ten50k = <?= $views["midk"]; ?>;
        var fifty100k = <?= $views["upperk"]; ?>;
        var hund250k = <?= $views["lowhundk"]; ?>;
        var twohund500k = <?= $views["midhundk"]; ?>;
        var fivehund1mil = <?= $views["uphundk"]; ?>;
        var overmil = <?= $views["overmil"]; ?>;

        const data = {
        labels: [
          '0-10K',
          '10-50K',
          '50-100K',
          '100-250K',
          '250-500K',
          '500K-1mil',
          'Above 1mil'

        ],
        datasets: [{
          label: 'My First Dataset',
          data: [zero10k, ten50k, fifty100k, hund250k, twohund500k, fivehund1mil, overmil],
          backgroundColor: [
            'rgb(52, 158, 235)',
            'rgb(75, 192, 192)',
            'rgb(255, 205, 86)',
            'rgb(201, 203, 207)',
            'rgb(68, 94, 143)',
            'rgb(126, 94, 103)',
            'rgb(84, 23, 95)'

          ]
        }]
      };

const polar_config = {
  type: 'polarArea',
  data: data,
  options: {
        plugins: {
            title: {
                display: true,
                text: 'Genius Views Breakdown'
            }
        }
    }
};
    </script>
</section>

  <!-- rendering for Polar chart -->
  <script>
    const polar = new Chart(
      document.getElementById('polar'),
      polar_config
    );</script>


<!-- radar graph of genre breakdown -->
<section class = "container">
    <script style = "height: 200px">
        // set up the chart
        const radar_data = {
  labels: [
    '12AM',
    '3AM',
    '6AM',
    '9AM',
    '12PM',
    '3PM',
    '6PM',
    '9PM',
  ],
  datasets: [{
    label: 'Folk Music',
    data: [65, 59, 90, 81, 56, 55, 40, 35],
    fill: true,
    backgroundColor: 'rgba(255, 99, 132, 0.2)',
    borderColor: 'rgb(255, 99, 132)',
    pointBackgroundColor: 'rgb(255, 99, 132)',
    pointBorderColor: '#fff',
    pointHoverBackgroundColor: '#fff',
    pointHoverBorderColor: 'rgb(255, 99, 132)'
  }, {
    label: 'Indie',
    data: [28, 48, 40, 19, 96, 27, 100, 20],
    fill: true,
    backgroundColor: 'rgba(54, 162, 235, 0.2)',
    borderColor: 'rgb(54, 162, 235)',
    pointBackgroundColor: 'rgb(54, 162, 235)',
    pointBorderColor: '#fff',
    pointHoverBackgroundColor: '#fff',
    pointHoverBorderColor: 'rgb(54, 162, 235)'
  }]
};
    
const radar_config = {
  type: 'radar',
  data: radar_data,
  options: {
    elements: {
      line: {
        borderWidth: 3
      }
    }
  },
};

    </script>
  </section>

  <!-- rendering for Radar chart -->
  <script>
  const radar = new Chart(
    document.getElementById('radar'),
    radar_config
  );</script>


<footer class = "primary-footer row">
  <small class = "copyright">&#169; Patrick Duong and Max Kouzel.</small>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="less.js-master/" ></script>


    </body>
 </html>

