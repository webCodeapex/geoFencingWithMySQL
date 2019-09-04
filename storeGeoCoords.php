<?php
  // database connection configuration
  $host = 'localhost';
  $user = 'root';
  $password = '';
  $dbName = 'demo';

  $con = mysqli_connect($host, $user, $password, $dbName);
  if(!$con){
    die('Error Occured').mysqli_error($con);
  }
  else{
    if(isset($_POST['overlayName']) && isset($_POST['polygon'])){
        $regionName = $_POST['overlayName'];
        $polygon = $_POST['polygon'];
        // insert query to save the records into db
        // here "testPoly" is our table name and "pol" is our MySQL spatial data type column of type polygon
        // "ST_GeomFromText('POLYGON($polygon)')" function will convert our coordinate sets into MySQL polygon points.
        $qry = "insert INTO testPoly (regionName, pol) VALUES ('$regionName', ST_GeomFromText('POLYGON($polygon)'))";
        //insert INTO testPoly (regionName, pol) VALUES ('$regionName', ST_GeomFromText('POLYGON((lat0 lng0, lat1 lng1,.........,latn lngn, lat0 lng0))'))
        
        $res = mysqli_query($con, $qry);
        if(!$res){
          die('insertion error').mysqli_error($con);
        } else {
          echo 'inserted successfully';
        }
    }
  }
?>