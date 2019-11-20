 <?php
 require_once('config.php');

 $result = 0;

 $id = intval($_POST['id']);

if(intval($id)){

  $query = $pdo->prepare("DELETE FROM $updateTable WHERE id = :id");
  $query->bindParam(':id', $id, PDO::PARAM_INT);

  if($query->execute()){

    $result = 1;
  }

}

echo $result;
?>