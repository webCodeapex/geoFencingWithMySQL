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
        $qry = "select asText(pol) as coordinate FROM testPoly";

        $res = mysqli_query($con, $qry);
        // $row = mysqli_fetch_array($res);
        $arrDemo = [];
        while($row = mysqli_fetch_assoc($res)){
          $arrTemp = [];
          $blob = str_replace("))", "", str_replace("POLYGON((", "", $row['coordinate']));
          $coords = explode(",", $blob);
          for($i = 0; $i < count($coords); $i++){
            $coord_split = explode(" ", $coords[$i]);
            $coordinates["lat"]=$coord_split[0];
            $coordinates["lng"]=$coord_split[1];
            array_push($arrTemp, json_encode($coordinates));    
        }
          array_push($arrDemo, $arrTemp);    
        
      }
      print_r(json_encode($arrDemo));
          
          //   $coords = explode(",", $blob);
          //   $coordinates = array();
          //   foreach($coords as $coord)
          //   {
          //       $coord_split = explode(" ", $coord);
          //       $coordinates[]=array("lat"=>$coord_split[0], "lng"=>$coord_split[1]);
          //   }
          //   print_r(json_encode($coordinates));
        // }
        // function sql_to_coordinates($blob)
        // {
        //     $blob = str_replace("))", "", str_replace("POLYGON((", "", $blob));
        //     $coords = explode(",", $blob);
        //     $coordinates = array();
        //     foreach($coords as $coord)
        //     {
        //         $coord_split = explode(" ", $coord);
        //         $coordinates[]=array("lat"=>$coord_split[0], "lng"=>$coord_split[1]);
        //     }
        //     print_r($coordinates);
        // }
  }
?>