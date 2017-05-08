<?php
 // just so we know it is broken
 error_reporting(E_ALL ^ E_DEPRECATED);
 // some basic sanity checks
 
 if(ISSET($_GET["Ibtn"]))
 {
 	$lat=$_GET["Latitude"];
 	$lng=$_GET["Longitude"];
 	$place=$_GET["Place"];
     //connect to the db
    $con=mysql_connect('us-cdbr-iron-east-03.cleardb.net','b1265721bda1fd','8b57cdc1')  or die ("Con Error".mysql_error());
    mysql_select_db('ad_0e67c22e1e4d6ec',$con);
     $sql = "Insert into places values($lat,$lng,'$place')";
     echo $sql;
     // the result of the query
     $result = mysql_query($sql,$con) or die("Invalid query: " . mysql_error());
   
     if($result){
     echo "Saved";
     echo '<script language="javascript">';
	echo 'alert("Location Successfully Stored!")';
	echo '</script>';
     if(set_time_limit(10))
     
     header("Location:/index.php");
     echo '<script type="text/javascript">';
     echo 'alert("Data Successfully Inserted into DataBase")';
     echo '</script>';

  		
  }
else
	 echo "Error".mysql_error();
     mysql_close($con);
 }
?>
