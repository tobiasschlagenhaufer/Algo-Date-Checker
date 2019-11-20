<?php
  require("config.php");

  if ($_SERVER['REQUEST_METHOD'] == 'POST'){ //is page reload from a form submit
    $array = array(
      "date"=>$_POST['date'],
      "description"=>$_POST['description'],
      "class"=>$_POST['class']
    );


    $query = $pdo->prepare("INSERT INTO updates (`date`,`description`,`class`) VALUES(:date, :description, :class)");
    echo $_POST['date'];
    $query->execute($array);
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

    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
    </script>

    <!-- DataTables -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.material.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-lite/1.1.0/material.min.css
    "/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.material.min.css"/>

    <!-- Custom styles for this template -->
    <style>
    html,body{
		  height: 100%;
		}
		body {
		  /* Margin bottom by footer height */
		  margin-bottom: 60px;
		  padding-top: 3.5rem;
      
		}

		.footer {
		  position: absolute;
		  bottom: 0;
		  width: 100%;
		  /* Set the fixed height of the footer here */
		  height: 60px;
		  line-height: 60px; /* Vertically center the text there */
		  background-color: #f5f5f5;
		}


    #updateTable tbody tr.selected {
        background-color: #a6b1c1 !important; 
    }
    #updateTable tbody tr:hover {
        background-color: #dee6f2;
    }

    </style>
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
            <a class="nav-link" href="index.php">Main Menu </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="application.php">Main Application</a>
          </li>
          <li class="nav-item  active">
            <a class="nav-link" href="editor.php">Update Editor <span class="sr-only">(current)</span></a>
          </li>
        </ul>
      </div>
    </nav>

    <main role="main">
      <div class="container-fluid row">
      <!-- Data Table card-->
      <div class="col-md-6" style="top: 3rem">
        <div class="card">
          <div class="card-header">
            List of Updates
          </div>

          <div class="card-body">

        <table id='updateTable' class='mdl-data-table hover display' style='width: 100%;'>
          <thead>
            <tr>
              <th>Date</th>
              <th>Description</th>                       
              <th>Class</th>
            </tr>
          </thead>

          <tbody>

          <?php
              
            //get clients array from database
            $query = $pdo->prepare("SELECT * FROM $updateTable");
            $query->execute();
            $updateTableArray = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach($updateTableArray as $current){ //for loop to make the table data
              echo "<tr id='".$current['id']."'>
                    <td>".$current['date']."</td>";

                    if(strlen($current['description']) > 55){
                      echo "<td>".substr($current['description'], 0,55). "..." . "</td>";
                    }
                    else{
                      echo "<td>".$current['description']. "</td>";
                    }
                                         
                    echo "<td>".$current['class']."</td>
                  </tr>";
              }
          ?>

          </tbody>
         </table>

         <button id="deleteButton" class="btn btn-primary">Delete</button>

        </div>

      </div>

    </div>

    <!-- Add Update card-->
    <div class="col-md-6" style="top: 3rem; float: right;">
        <div class="card">
          <div class="card-header">
            Add an Update
          </div>

          <div class="card-body">
            <form action="" method="POST">

              <div class="form-group">
                <label for="date">Date</label>
                <input type="text" class="form-control" id="date" name="date" placeholder="YYYY-MM-DD">
              </div>

              <div class="form-group">
                <label for="description">Description / Name</label>
                <textarea class="form-control" id="description" rows="3" name="description"></textarea>
              </div>

              <div class="form-group">
                <label for="class">Class</label>
                <select class="form-control" id="class" name="class">
                  <option>Minor</option>
                  <option>Moderate</option>
                  <option>Major</option>
                  <option>Quality</option>
                  <option>Link</option>
                </select>
                
                <!--<br/>
                <input id="quality" type="radio" name="related" value="quality">
                <label for="quality">Quality Related</label><br/>
                
                <input id="link" type="radio" name="related" value="link">
                <label for="link">Link Related</label>-->

              </div>

              <button type="submit" class="btn btn-primary">Submit</button>
            </form>
          </div>

        </div>
      </div>
    </div>
    </main>

    <script type="text/javascript">
      var id; //id of the selected row


        //set up the dataTable
      $(document).ready( function() {

          var table = $('#updateTable').DataTable(); 
       
          $('#updateTable tbody').on( 'click', 'tr', function () {
            id = table.row(this).id(); //set the id to this row for ajax

              if ( $(this).hasClass('selected') ) { //unselect if it's selected
                  $(this).removeClass('selected');
              }
              else {
                  table.$('tr.selected').removeClass('selected'); //select if it's not
                  $(this).addClass('selected');
              }
          } );
       
        //if the delete button is clicked
          $('#deleteButton').click( function (){

              console.log('id: ',id);
              //call ajax page
              $.post('ajax_process.php', {id:id})
                  .done(function(data) {

            console.log('data: ',data)
                    if(data > 0){//if the row is deleted from the database
                      $('.success').show(3000).html("Record deleted successfully.").delay(2000).fadeOut(1000);//no error or success message yet                  
                      table.row('.selected').remove().draw( false ); //remove the selected row from DOM

                    }
                    else{
                       $('.error').show(3000).html("Record could not be deleted. Please try again.").delay(2000).fadeOut(1000);
                    }
                });
          });
      });


    </script>

  </body>
</html>
