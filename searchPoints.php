<?php
  $host = 'localhost';
  $user = 'root';
  $password = '';
  $dbName = 'demo';

  $con = mysqli_connect($host, $user, $password, $dbName);

  if(!$con){
    die('Error Occured').mysqli_error($con);
  }
  else{
    if(isset($_POST['lat']) && isset($_POST['lng'])){
        $lat = $_POST['lat'];
        $lng = $_POST['lng'];
        // "GeomFromText('POINT($lat $lng)')" function will convert our coordinate sets into MySQL points.
        $qry = "select regionName FROM testPoly where ST_Contains(pol, GeomFromText('POINT($lat $lng)'))";

        $res = mysqli_query($con, $qry);
        $row = mysqli_fetch_array($res);
        $regionName = $row['regionName'];

        if(!empty($regionName)){
            echo "This area belongs to $regionName region";
        }
        else {
            echo "This area belongs to no region";
        }
    }
  }
?>