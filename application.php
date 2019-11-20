<?php
  require("config.php");

  $query = $pdo->prepare("SELECT * FROM $updateTable");
  $query->execute();
  $updateTableArray = $query->fetchAll(PDO::FETCH_ASSOC);

  if(isset($_POST['unload'])){
    unset($_FILES);
  }
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Aglorithm Update Visualization</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">


    <!-- Custom styles for this template -->
    <style>
      html, .row{
        height: 100%;
      }
      .container-fluid{
        height:90%;
      }
      body {
        font-size: .875rem;
        padding-top: 4.5rem;
        height: 100%;
      }

      .settings{
        position: relative;
        left: 0.5rem;
      }
      .unload{
        position: relative;
        float: right;
        right: 0.5rem;
      }

      #collapseExample {
        position: absolute;
        z-index: 10000;
      }

      .collapsing {
        -webkit-transition: none;
        transition: none;
        display: none;
      }

      /* Bootstrap Switches */
      
      /* The switch - the box around the slider */
      .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
        float:left;
        margin-left: 20px;
        margin-right: 20px;
      }

      /* Hide default HTML checkbox */
      .switch input {display:none;}

      /* The slider */
      .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
      }

      .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
      }

      input.default:checked + .slider {
        background-color: #444;
      }
      input.primary:checked + .slider {
        background-color: #2196F3;
      }
      input.success:checked + .slider {
        background-color: #8bc34a;
      }
      input.info:checked + .slider {
        background-color: #3de0f5;
      }
      input.warning:checked + .slider {
        background-color: #FFC107;
      }
      input.danger:checked + .slider {
        background-color: #f44336;
      }

      input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
      }

      input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
      }

      /* Rounded sliders */
      .slider.round {
        border-radius: 34px;
      }

      .slider.round:before {
        border-radius: 50%;
      }
            

    </style>

    <!-- Google Charts -->
    <style> 
      .chartWithMarkerOverlay {
        position: relative;
        width: 100%;
      }

      .overlay-text {
        width: 200px;
        height: 200px;
        position: absolute;
        top: 50px;   /* chartArea top */
        left: 200px; /* chartArea left */
      }

      .overlay-marker {
        width: 50px;
        height: 50px;
        position: absolute;
        top: 53px;   /* chartArea top */
        left: 528px; /* chartArea left */
      }

      .collapse{
        padding-bottom: 10px;
      }

      label img {
        opacity : 0.3;
      }
      input[type=checkbox]:checked + label img {
        opacity : 1;
      }

    </style>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">

      google.charts.load("current", {packages:['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = new google.visualization.arrayToDataTable([
          ['Date', 'Traffic'],

          <?php
            if(isset($_FILES['file'])){ //if there's a csv in the temp storage
              $file = fopen($_FILES['file']['tmp_name'], 'r');

              $i=0;
              $j=0;
              while (($line = fgetcsv($file)) !== FALSE) { //make the csv into an array
                if($i>=20){
                  $csv[$j] = $line;
                  $data[$j]['date'] = date("Y-m-d", strtotime($csv[$j][0])); //turn the format to be readable in Google Charts
                  $data[$j]['traffic'] = str_replace(",", "", $csv[$j][1]); //take out the comma in numbers 
                  $data[$j]['pos'] = $j;
                  $j++;
                }
                $i++;
              }

              array_pop($data); //remove last element in csv as it's the total 
              array_pop($data); //remove last element in csv as it's the total AGAIN
              fclose($file);

              foreach($data as $point){
                $dataPoint = "[new Date(" . substr($point['date'], 0, 4) . "," . (substr($point['date'], 5, 2)-1) . "," . substr($point['date'], 8, 2) . ")," . $point['traffic'] . "],";
                echo $dataPoint;
              }
            }
          ?>

        ]);

        var options = {
          hAxis: {
            viewWindow: {
              //min: new Date(2016, 11, 31),
              //max: new Date(2018, 0, 3)
            },
          },
          chartArea: {
            // leave room for y-axis labels
            width: '90%',
            height: '90%'
          },
          legend: 'none',
          colors: ['#003f5c'],
          lineWidth: 2,
        };

        //function to place update markers according to their date

        function placeMarker(dataTable) {
          var cli = this.getChartLayoutInterface();
          var chartArea = cli.getChartAreaBoundingBox();
          var heightAdded;

          function heightAdjust(pos){ //function to get average line and max height //
              var i,difference;
              var n = 0;
              var location = 0;
              var avgLocation = 0;
              var shortest = Math.floor(cli.getYLocation(dataTable.getValue(pos,1)));

              for(i=pos-10;i<pos+10;i++){
                location = Math.floor(cli.getYLocation(dataTable.getValue(i,1)));
                avgLocation += location;

                if(location < shortest){ 
                  shortest = location;
                }
                n++;
              }

              //console.log(avgLocation,n);
              avgLocation /= n;
              difference = avgLocation - shortest;

              return (avgLocation - difference - 35);
          }  

          <?php
            if(isset($_FILES['file'])){
              foreach($updateTableArray as $update){
                foreach($data as $point){
                 
                  if($point['date'] == $update['date']){ //if they share the same date, it must be that position
                    $inRangeUpdate[] = $update; //make a new array of updates within the range for later

                    echo "heightAdded = heightAdjust(".$point['pos'].");";
                  
                    //echo "console.log(heightAdded);";

                    echo "document.querySelector('#img_overlay_".$update['id']."').style.top = heightAdded + 'px';";
                    echo "
                    ";
                    echo "document.querySelector('#img_overlay_".$update['id']."').style.left = Math.floor(cli.getXLocation(new Date('".$update['date']."'))) - 12 + 'px';";
                  }
                }
              }
            }
          ?>
        };

        var chart = new google.visualization.LineChart(document.getElementById('line-chart-marker'));
        google.visualization.events.addListener(chart, 'ready',placeMarker.bind(chart, data));

        <?php
          if(isset($_FILES['file'])){
              echo "chart.draw(data, options);"; //draw the actual chart if the csv is uploaded
          }
        ?>

      }

    </script>
  </head>

  <body>

    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
      <a class="navbar-brand" href=# target="_blank">AUV</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Main Menu <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="application.php">Main Application</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="editor.php">Update Editor</a>
          </li>
        </ul>
      </div>
    </nav>

    <div>
      <?php
        if(isset($_FILES['file'])){
          echo " 
          <div class='container-fluid row'>
            <a class='btn btn-primary settings' data-toggle='collapse' href='#collapseExample' role='button' aria-expanded='false' aria-controls='collapseExample'>
              Settings
            </a>

            <div>
              <a class='btn btn-danger' href='#' role='button' onclick='unset()' style='margin-left: 100px'>
                Unload Data
              </a>
            </div>
          </div>
          ";
        }
      ?>
      <div class="collapse container-fluid row col-sm-6" id="collapseExample">

        <div class= "col-sm-6" style="max-width: 350px; flex: 0 0 100%">
          <div class="list-group-item">
            <label class="switch">
              <input type="checkbox" class="default" checked="checked" id="Minor">
              <span class="slider round" id="minor-check"></span>
            </label>
            <h4>Minor</h4>
          </div>

          <div class="list-group-item">
            <label class="switch">
              <input type="checkbox" class="primary" checked="checked" id="Moderate">
              <span class="slider round"></span>
            </label>
            <h4>Moderate</h4>
          </div>

          <div class="list-group-item">
            <label class="switch">
              <input type="checkbox" class="danger" checked="checked" id="Major">
              <span class="slider round"></span>
            </label>
            <h4>Major</h4>
          </div>

          <div class="list-group-item">
            <label class="switch">
              <input type="checkbox" class="success" checked="checked" id="Link">
              <span class="slider round"></span>
            </label>
            <h4>Link Related</h4>
          </div>

          <div class="list-group-item">
            <label class="switch">
              <input type="checkbox" class="warning" checked="checked" id="Quality">
              <span class="slider round"></span>
            </label>
            <h4>Quality</h4>
          
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid">
        <main role="main" style="height: 100%">

          <!--<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2">Dashboard</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
              <button class="btn btn-sm btn-outline-secondary dropdown-toggle">
                <span data-feather="calendar"></span>
                  This week
              </button>
            </div>
          </div> -->

          <!-- Chart Elements -->

          <?php
          if(!isset($_FILES['file'])){      
            echo "
              <div class='card' style='width:50%'>

                <div class='card-header'>
                  <h3>Upload your Google Analytics CSV to continue</h3>
                </div>

                <div class='card-body'>
                  <form action='' method='post' name='uploadCSV'
                    enctype='multipart/form-data'>
                      <div>
                          <input type='file' name='file' id='file' accept='.csv'>
                          <br/><br/>
                          <button type='submit' id='submit' name='import'
                              class='btn-primary btn'>
                              Upload
                          </button>

                      </div>
                  </form>
                </div>

              </div>
            ";
      }

        ?>

          <div class="chartWithMarkerOverlay" style="height: 100%">
           <div id="line-chart-marker" style="width: 100%; height: 100%;"></div>

           <!-- <div class="overlay-text">
            <div style="font-family:'Arial Black'; font-size: 128px;">88</div>
            <div style="color: #b44; font-family:'Arial Black'; font-size: 32px; letter-spacing: .21em; margin-top:50px; margin-left:5px;">zombie</div>
            <div style="color: #444; font-family:'Arial Black'; font-size: 32px; letter-spacing: .15em; margin-top:15px; margin-left:5px;">attacks</div>
          </div> -->

          <?php
            if(isset($_FILES["file"])){
            //images
            foreach($inRangeUpdate as $update){
              if($update['class'] == "Minor"){
                $png = "marker-black.png";
              }
              else if($update['class'] == "Moderate"){
                $png = "marker-blue.png";
              }
              else if($update['class'] == "Major"){
                $png = "marker-red.png";
              }
              else if($update['class'] == "Link"){
                $png = "marker-green.png";
              }
              else if($update['class'] == "Quality"){
                $png = "marker-yellow.png";
              }
              
              echo "
                <div class='overlay-marker ".$update['class']."' id='img_overlay_".$update['id']."'>
                    <input class='marker_check'type='checkbox' id='checkbox_".$update['id']."' name='' style='display: none;'/>

                    <label for='checkbox_".$update['id']."'>
                      <img src='".$png."' height='35' title='".date("M jS, Y", strtotime($update['date']))."' data-toggle='popover' data-placement='top' data-content='".$update['description']."'>
                    </label>
                </div>
              ";
            }

          

          echo "</div>";

          echo '<!--Expand elements for report purpose-->
          <h4 class="display-4">Selected Updates</h4>
          <form action="export.php" method="POST">';
  
            $classes = ["Minor","Moderate","Major","Link","Quality"];

            foreach($classes as $class){ //look at each class type
              echo "<h5>".$class."<h5>";

              foreach($inRangeUpdate as $update){ //look at each update and place it under its class
                if($update['class']===$class){
                  echo "
                    <div class='collapse col-md-8' id='collapse_".$update['id']."'>
                      <div class='card'>
                        <div class='card-header container-fluid'>
                          <h4>".date("M jS, Y", strtotime($update['date'])). " Update - <small>". $update['description']. "</small></h4>
                        </div>
                      </div>
                    </div>
                  ";
                }
              }
              echo "<hr>";
            }
            $i=0;
            foreach($data as $point){
              echo "<input type='hidden' name='data[".$i."][date]' value='".$point['date']."'>";
              echo "
              ";
              echo "<input type='hidden' name='data[".$i."][traffic]' value='".$point['traffic']."'>";
              echo "
              ";
              $i++;
            }

            $i=0;
            foreach($inRangeUpdate as $update){
              echo "<input type='hidden' name='update[".$i."][id]' value='".$update['id']."'>";
              echo "
              ";
              echo "<input type='hidden' name='update[".$i."][date]' value='".$update['date']."'>";
              echo "
              ";
              echo "<input type='hidden' name='update[".$i."][description]' value='".$update['description']."'>";
              echo "
              ";
              $i++;
            }

          echo "<input type='hidden' id='checkedUpdates' name='checkedUpdates' value = ''>";  
          
          echo '<button type="submit" class="btn btn-primary mb-2" name="export">Submit and Export &rarr;</button>';
        echo '</form>';
        }
        ?>

        </main>
      </div>
    </div>

    <form id="fake_form" method="POST" action="">
      <input type="hidden" value="" name="unload" id="unload"/>
    </form>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
    </script>

    <script>

      $(function () { //popovers for images
        $('[data-toggle="popover"]').popover({ trigger: "hover" }); //popover shows on hover
      })

      <?php
        foreach($classes as $class){
          //annoying way to check if class is selected then hide it

          echo '
            $("#'.$class.'").change(function(){

              if(this.checked){
                $(".'.$class.'").each(function(i, obj) {
                  $(this).show();
                });
              }
              else{
                $(".'.$class.'").each(function(i, obj) {
                  $(this).hide();
                });
              }
            });
          ';
        }
      ?>

      //
      var fieldDates = [
      <?php
        $i = 0;
        foreach($inRangeUpdate as $update){
          if($i>0){
            echo ",";
          }
          echo $update['id'];
          $i++;
        }
      ?>
      ];

      $('.marker_check').change(function(){
        checkedUpdates = [];
        j=0;
        for (i=0;i<fieldDates.length;i++) {
            input = document.getElementById("checkbox_" + fieldDates[i]);

            if(input.checked === true) {
                checkedUpdates[j] = fieldDates[i];
                $("#collapse_"+fieldDates[i]).show();
                j++;
            }
            else{
                $("#collapse_"+fieldDates[i]).hide();
            }
        }
        cUpdates = document.getElementById("checkedUpdates");
        cUpdates.value = checkedUpdates;
        console.log("Adding",cUpdates.value);
      });

      function unset(){
        document.getElementById("unload").value = "true";
        document.getElementById("fake_form").submit();
      }

      /*function export(){
        for (i=0;i<fieldDates.length;i++) {
            input = document.getElementById("checkbox_" + fieldDates[i]);

            if(input.checked === true) {
                $("#collapse_"+fieldDates[i])
            }
      }*/


    </script>

  </body>
</html>
