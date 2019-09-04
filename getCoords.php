<!-- this file is to draw a polygon shape on the google mpa and get the coordinates(latitude, longitude) for the shape -->
<!DOCTYPE html>
<html>
  <head>
    <title>Drawing Tools</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
      #showMap{
        display: none;
      }
      #onPolygonComplete{
        position: absolute;
        z-index: -1;
        display: none;
        top:0;
        left:0;
      }
      #getNameOfOverlay{
        position: absolute;
        z-index: -1;
        display: none;
        top:5%;
      }
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      div[title="Stop drawing"] { 
        display: none; 
      }
      div[title="Draw a shape"] { 
        display: none; 
      }
      .class{
        display : block !important;
      }
    </style>
  </head>
  <body>
    <!-- if you wanted to search for your region to draw geoFencing you can search with your area latitude and longitude -->
    <div id="getCoordinates" class="container pt-4">
      <div class="col-6">
        <div class="form-group">
          <label for="lat">Enter Latitude</label>
          <input type="text" id="lat" class="form-control" value="30.660753707548512">
        </div>
        <div class="form-group">
          <label for="lng">Enter Longitude</label>
          <input type="text" id="lng" class="form-control" value="76.72039464383317">
        </div>
        <div class="form-group">
          <input type="submit" id="search" onclick="getValues()" class="btn btn-primary">
        </div>
      </div>
    </div>

    <!-- this div is to show map on the screen -->
    <div class="container text-center pb-2" id="showMap">
      <div class="container" style='height:50px'>
      <!-- this button is to enable drawing mode -->
        <button id="drawOnMap" class="btn btn-primary mt-2" onclick="showDrawingTool()">
          Create Drawing On Map
        </button>
      </div>
      <div id="map" class="container" style="height:90vh"></div>
    </div>

    <!-- this division show when you complete the polygon drawing for confirmation if you wanted to save your shape or wanted to redraw-->
    <div class="container-fluid text-center pb-2" id="onPolygonComplete">
      <!-- if you wanted to save coordinates -->
      <button id="saveDraw" class="btn btn-primary mt-2" onclick="getNameOfOverlay()">
        Save Coordinates
      </button>
      <!-- if you wanted to redraw -->
      <button id="clearAndRedraw" class="btn btn-primary mt-2" onclick="reDraw()">
        Redraw
      </button>
    </div>

    <!-- give your shape a name before saving it to database -->
    <div class="container-fluid py-5 bg-light" id="getNameOfOverlay">
      <div class="row justify-content-center">
        <div class="col-6">
          <div class="form-group text-left">
            <label for="overlayName">Enter Name For This Overlay</label>
            <input type="text" id="overlayName" class="form-control">
          </div>
          <button id="save" class="btn btn-primary mt-2" onclick="saveCoordinates()">
            save
          </button>
        </div>
      </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script>
      // empty array to get a record of drawn shapes over map
      var arrOverlay = [];
      // empty string. this string will get all of the coordinates of drawn shape, in MySQL polygon spatial data type
      var polygonWKT1 = '';

      // to get the latitude and longitude you entered to search your area and reCenter the map in your area
      function getValues(){
          var latitude = $('#lat').val();
          var longitude = $('#lng').val();
          var currentPosition = {
            'lat' : latitude,
            'lng' : longitude
          };
          $('#getCoordinates').hide(100, function(){
            $('#showMap').show(100, initMap(latitude, longitude));                 // our given lat lng passed to initMap function to set center of the map
          })
      }

      // to enable polygon shape on map so you can draw a shape
      function showDrawingTool(){
        $('[title="Draw a shape"]').addClass('class');
        $('[title="Stop drawing"]').addClass('class');
      }

      // when you complete your polygonh this function got triggered
      function drawingComplete(){
        $('#drawOnMap').hide();
        $('#onPolygonComplete').css({'display':'block', 'z-index':'10'});
      }

      // this function is to give a shape name and to store it to db
      function getNameOfOverlay(){
        $('#onPolygonComplete').css({'display':'none', 'z-index':'-1'});
        $('#getNameOfOverlay').css({'display':'block', 'z-index':'10'})
      }

      // ajax request to save all data to db
      function saveCoordinates(){
        var overlayName = $('#overlayName').val();
        // ajax post request to pass data to storeGeoCoords file, where this data will be saved into database
        $.ajax({
          url : 'storeGeoCoords.php',
          type : 'post',
          data : {
            'polygon' : polygonWKT1,
            'overlayName' : overlayName
          },
          success : function(data){
            alert('successfully saved');
            window.location.reload();
          }
        })
      }

      // if you click on reDraw button this function will be triggered. It will delete the drawn shape from the map
      function reDraw(){
        for (var i=0; i < arrOverlay.length; i++)
        {
          arrOverlay[i].overlay.setMap(null);
        }
        arrOverlay = [];
      }

      // map initialization function to show a map, to draw shapes on map
      function initMap(latitude, longitude) {
        var myLatlng = new google.maps.LatLng(latitude, longitude);
        var map = new google.maps.Map(document.getElementById('map'), {
          center: myLatlng,
          zoom: 14
        });

        var drawingManager = new google.maps.drawing.DrawingManager({
          drawingControl: true,
          drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            zIndex: -1,
            drawingModes: ['polygon']
          },
          markerOptions: {icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'},
          circleOptions: {
            fillColor: '#ffff00',
            fillOpacity: 1,
            strokeWeight: 5,
            clickable: false,
            editable: true,
            zIndex: 1
          }
        });
        google.maps.event.addListener(drawingManager, 'overlaycomplete', function(polygon) {
          // push the drawing shape into array so we can keep a record of drawing shapes
            arrOverlay.push(polygon);
            drawingComplete();
            // to save data into MySQL spatial data type polygon, we need in the following format (lat0 lng0, lat1 lng1,.....,latn lngn, lat0 lng0). Thats why we store all coordinates set in a formatted string polygonWKT 
            // lat0 lng0 are repeated at the end to close the polygon shape
            var polygonWKT = '(';
            var lat = [];
            var lng = [];
            for (var i = 0; i < polygon.overlay.getPath().getLength(); i++) {
                lat[i] = polygon.overlay.getPath().getAt(i).lat();
                lng[i] = polygon.overlay.getPath().getAt(i).lng();
            }

            /* to close the polygon shape, at the end of of lat lng, we store the first coordinates again */
            lat[polygon.overlay.getPath().getLength()] = polygon.overlay.getPath().getAt(0).lat();
            lng[polygon.overlay.getPath().getLength()] = polygon.overlay.getPath().getAt(0).lng();
            /* ----------------------- */
            
            for(var i = 0; i < lng.length; i++){
              polygonWKT += lat[i] + ' ' + lng[i]

              if(i < lng.length - 1){
                polygonWKT += ',';
              }
            }
            polygonWKT += ')';
            polygonWKT1 = polygonWKT;
        });
        drawingManager.setMap(map);
      }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=drawing&callback=initMap" async defer></script>
  </body>
</html>