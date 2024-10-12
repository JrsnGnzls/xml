<?php 


function executeselect($spname,$params){
   // require 'constants/db_config.php';
   try{

   
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "xml_db";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo $params;
    if($params == ""){
        $sql = "CALL $spname";
    }
    else{
        $sql = "CALL $spname($params)";
       // echo $sql;
        //die();
    }
  //  echo $sql;
    //$sql = "CALL $spname";
    //$statement = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
   // $res = $conn->query()->fetchAll(PDO::FETCH_ASSOC);
    $statement = $conn->prepare($sql);
    $statement->execute();
    $res = $statement->fetchAll(PDO::FETCH_ASSOC);

  

   return $res;
}
catch(PDOException $e){

echo $e;

}

}


?>