<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Polygon Arrays</title>
    <style>
    #map {
        height: 100%;
    }
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
    </style>
</head>
<body>
    <div id="map"></div>
    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script>
        
        var mainArray = [];
        $(document).ready(function(){
            $.ajax({
                url : 'getGeoFencesData.php',
                type : 'get',
                success : function(data){
                    
                    var arrCoords = JSON.parse(data);
                    
                    for(var i = 0; i < arrCoords.length; i++){
                        var latLng = [];
                        var innerArray = arrCoords[i];
                        for(var j = 0; j < innerArray.length; j++){
                            var tmp = JSON.parse(innerArray[j]);
                            // console.log(tmp);
                            latLng.push(new google.maps.LatLng( parseFloat(tmp.lat) , parseFloat(tmp.lng)));
                        }
                        mainArray.push(latLng);
                    }
                    // console.log(JSON.stringify(mainArray));
                    initMap(mainArray);
                }
            })
        })
        var map;
        var infoWindow;

        function initMap(data) {
            var myLatlng = new google.maps.LatLng(30.660753707548512, 76.72039464383317);
            var arrColors = ['#FF0000','#FF5733','#39148C','#E447EB','#EEAF08','#0F010C','#54F60E','#37EEF3','#A5A6F0'];
            map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,
            center: myLatlng,
            mapTypeId: 'terrain'
            });

            // Define the LatLng coordinates for the polygon.
            var triangleCoords = data;
            for(var i = 0;i < triangleCoords.length;i++) {
                var selectedColor = arrColors[Math.floor(Math.random() * arrColors.length)];
                // Construct the polygon.
                var bermudaTriangle = new google.maps.Polygon({
                paths: triangleCoords[i],
                strokeColor: selectedColor,
                strokeOpacity: 0.8,
                strokeWeight: 3,
                fillColor: selectedColor,
                fillOpacity: 0.35
                });
                bermudaTriangle.setMap(map);

                // Add a listener for the click event.
                bermudaTriangle.addListener('click', function(event) {
                    searchPlace(event.latLng);
                });
            }
            google.maps.event.addListener(map, 'click', function(event) {
                searchPlace(event.latLng);
            });
            function searchPlace(location) {
                var latitude = location.lat();
                var longitude = location.lng();
                $.ajax({
                    url : 'searchPoints.php',
                    type : 'post',
                    data : {
                    'lat' : latitude,
                    'lng' : longitude
                    },
                    success : function(data){
                    alert(data);
                    }
                })
            }
            infoWindow = new google.maps.InfoWindow;
        }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js">
    </script>
</body>
</html>