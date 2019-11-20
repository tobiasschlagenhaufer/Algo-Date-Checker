<?php
  require("config.php");

  $query = $pdo->prepare("SELECT * FROM $updateTable");
  $query->execute();
  $updateTableArray = $query->fetchAll(PDO::FETCH_ASSOC);

  $data = $_POST['data'];
  $temp_updates = $_POST['checkedUpdates'];
  $update = $_POST['update'];

  $updateId = explode(",",$temp_updates);
  $selected_dates = [];
  foreach ($update as $val){
  	foreach($updateId as $id){
		if($val['id'] == $id){
  			array_push($selected_dates,$val);
  		}
		}
  }

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <style>
    	html, body{
    		height: 100%;
    	}
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

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">

      google.charts.load("current", {packages:['corechart']});
      google.charts.setOnLoadCallback(drawChart);

	    function drawChart() {
	        var data = new google.visualization.arrayToDataTable([
	          ['Date', 'Traffic'],

	          <?php

	              foreach($data as $point){
	                $dataPoint = "[new Date(" . substr($point['date'], 0, 4) . "," . (substr($point['date'], 5, 2)-1) . "," . substr($point['date'], 8, 2) . ")," . $point['traffic'] . "],";
	                echo $dataPoint;
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
	          'legend' : 'right',
	          colors: ['#003f5c'],
	          lineWidth: 2,
	        };

	        //function to place update markers according to their date

	        function placeMarker(dataTable) {
	          var cli = this.getChartLayoutInterface();
	          var chartArea = cli.getChartAreaBoundingBox();
	          var heightAdded;

	          function heightAdjust(pos){ //function to get average line and max height // (useless until I find a way to get pixel coordinates)
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
	              console.log(i,location);
	              n++;
	            }

	            //console.log(avgLocation,n);
	            avgLocation /= n;
	            difference = avgLocation - shortest;

	            return (avgLocation - difference - 50);
	          } 

	          <?php
	            
	              foreach($selected_dates as $date){
	                foreach($data as $point){
	                 
	                  if($point['date'] == $date['date']){ //if they share the same date, it must be that position
	                  	$pos = array_search($point['date'], array_column($data,'date'));

	                    //echo "heightAdded = heightAdjust(".$point['pos'].");";
	                    echo "heightAdded = heightAdjust(".$pos.");";
	                    //echo "console.log(heightAdded);";

	                    echo "document.querySelector('#img_overlay_".$date['id']."').style.top =  heightAdded + 'px';";
	                    echo "
	                    ";
	                    echo "document.querySelector('#img_overlay_".$date['id']."').style.left = Math.floor(cli.getXLocation(new Date('".$date['date']."'))) - 53 + 'px';";
	                  }
	                }
	              }
	            
	          ?>
	        };

	        var chart = new google.visualization.LineChart(document.getElementById('line-chart-marker'));
	        google.visualization.events.addListener(chart, 'ready',placeMarker.bind(chart, data));

	        chart.draw(data, options);

	    }

    </script>

  </head>
  <body>

  	<div class="chartWithMarkerOverlay" style="height: 100%">
           <div id="line-chart-marker" style="width: 100%; height: 100%;"></div>

           <?php
           	$png = "http://weclipart.com/gimg/3516F6FA1C1D29A3/red-right-down-arrow-hi.png";

           	foreach ($selected_dates as $date){
           		echo "
           		<div class='overlay-marker' id='img_overlay_".$date['id']."'>
           			<img src='".$png."' height='50'>
           		</div>";
           	}
           ?>
    </div>

		<div style="text-align: left; padding-left: 20px">
			<ul>
    	<?php
				foreach ($selected_dates as $update){
					echo "<li><h4>".date("M jS, Y", strtotime($update['date'])). " Update - <small>". $update['description']. "</small></h4></li>";
				}
    	?>
			</ul>
    </div>

  </body>
 </html>