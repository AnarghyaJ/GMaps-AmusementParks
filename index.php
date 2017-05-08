<?php
// just so we know it is broken
 error_reporting(E_ALL ^ E_DEPRECATED);
 // some basic sanity checks
 

	$con=mysql_connect('us-cdbr-iron-east-03.cleardb.net','b1265721bda1fd','8b57cdc1')  or die ("Con Error".mysql_error());
    mysql_select_db('ad_0e67c22e1e4d6ec',$con);
      $latitude=array();
      $longitude=array();
      $places=array();
      
    $sql="Select * from places";
    
    $ret_val=mysql_query($sql, $con) or die ("Error".mysql_error());
    
    if($ret_val)
    {
    	if(mysql_num_rows($ret_val)>0)
    	{
    	//echo "<html><center>";
    	while($row=mysql_fetch_array($ret_val, MYSQL_NUM)){
			array_push($latitude,$row[0]);
			array_push($longitude,$row[1]);
			array_push($places,$row[2]);
		//echo ($latitude[0] . " and " . $longitude[0]." and " .$places[0] ."</br>");}
		//header("Location: \index.html");
		//echo "</center></html>";
    	}}
	else
		echo ("No Record Found");	
    } 
    else
	echo (mysql_error()."Error");
	
   
  ?>






<!DOCTYPE html>
<html>
  <head>
    <title>Amusement Parks</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css" type="text/css">
    
    <script>
    
	
	//Dummy Value 
	var CurrPosition={lat:15.120429, lng:75.088836};
      var map;
      var map2;
      var infowindow;
      var p;
      var CM;
      var Radius=10000;
      var markers = [];
	  var dest;
      var flag;
        
    

function error(err) {
  console.warn("ERROR"+err.code+err.message);
}


      function initMap( ) {
			
     		map = new google.maps.Map(document.getElementById('map'), {
          center:CurrPosition ,
          zoom: 5,
          mapTypeId: 'roadmap'
        });
		directionsService = new google.maps.DirectionsService;
         directionsDisplay = new google.maps.DirectionsRenderer;
		
         directionsDisplay.setMap(map);
        if (navigator.geolocation){
 	 navigator.geolocation.getCurrentPosition(function(position){
 	 var CurrPosition = {lat: position.coords.latitude, lng: position.coords.longitude};
     	window.CurrPosition=CurrPosition;
     	map.setCenter(CurrPosition);
     	map.setZoom(12);
     	var CM = new google.maps.Marker({
          map: map,
          position: CurrPosition,
          icon: pinSymbol("#f20000"),
          title: 'My Position'});
          }
 	 ,error)
 	}
 	else
 	{console.log("Browser not Supported");
 	}
        
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
          searchBox.setBounds(map.getBounds());
        });
        
        
        
        searchBox.addListener('places_changed', function() {
          var places = searchBox.getPlaces();

          if (places.length == 0) {
            return;
          }
          
        
          
          
          // For each place, get the icon, name and location.
          var bounds = new google.maps.LatLngBounds();
          places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }
            var icon = {
              url: place.icon,
              size: new google.maps.Size(71, 71),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(17, 34),
              scaledSize: new google.maps.Size(25, 25)
            };

            // Create a marker for each place.
            markers.push(new google.maps.Marker({
              map: map,
              icon: pinSymbol('#2c76f7'),
              title: place.name,
              position: place.geometry.location
            }));
			
            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
            dest=place.geometry.location;
            calculateDistance(dest);
            calculateAndDisplayRoute(directionsService, directionsDisplay);
           // NearBySearch(place.geometry.location);
          });
          map.fitBounds(bounds);
          
          
          
         });
      
    
          
        
      }

      function callback(results, status) {
        if (status === google.maps.places.PlacesServiceStatus.OK) {
          for (var i = 0; i < results.length; i++) {
            createMarker(results[i]);
          }
        }
      }
       
      function nbSearch(){
      //clearMarkers();
      NearBySearch(CurrPosition);
      }
      function NearBySearch(loc)
      {
      infowindow = new google.maps.InfoWindow();
        var service = new google.maps.places.PlacesService(map);
        service.nearbySearch({
          location: loc,
          radius: Radius,
          type: ['amusement_park']
        }, callback);
      }
      
     function pinSymbol(color) {
    return {
        path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z M -2,-30 a 2,2 0 1,1 4,0 2,2 0 1,1 -4,0',
        fillColor: color,
        fillOpacity: 1,
        strokeColor: '#000',
        strokeWeight: 2,
        scale: 1,
   };
}

      function createMarker(place) {
        var placeLoc = place.geometry.location;
        var marker = new google.maps.Marker({
          map: map,
          icon: pinSymbol('#2c76f7'),
          position: place.geometry.location
        });
        markers.push(marker);
		

        google.maps.event.addListener(marker, 'click', function() {
    
     
       dest=placeLoc;   
        calculateDistance(placeLoc);
		calculateAndDisplayRoute(directionsService, directionsDisplay);
       		
          
        }
        );
        marker.addListener('mouseover',function(){
        infowindow.setContent(place.name);
        infowindow.open(map, this);});
        marker.addListener('mouseout', function() {
    		infowindow.close();
			});
        
        }
        function setFlag(){
        	window.flag=43;
        }
        
        
         function clearMarkers( ){
        setMapOnAll(null);}
        
    function setMapOnAll(map) {
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(map);
  }
}
       function setRadius(){
       var t=document.getElementById("radius").value;
       clearMarkers();
       markers = [];
		setMapOnAll(map);
		Radius=t*1000;
		NearBySearch(CurrPosition);
		
        
       }
      
         function displayMarkers(){
      var latitude = <?php echo json_encode($latitude, JSON_HEX_TAG); ?>; 
    var longitude= <?php echo json_encode($longitude,JSON_HEX_TAG); ?>;
    var places= <?php echo json_encode($places,JSON_HEX_TAG); ?>;
    infowindow = new google.maps.InfoWindow(); 	   
    for( var i=0; i< latitude.length;i++){
    	console.log(latitude[i]+","+longitude[i]);
    	MarkPlace(latitude[i],longitude[i],places[i]);	
    }}
    
    function MarkPlace(latitude,longitude,name){
    	
    	var l1=parseFloat(latitude);
    	var l2=parseFloat(longitude);
     	var loc={lat:l1,lng:l2};
     	var marker = new google.maps.Marker({
          map: map,
          icon: pinSymbol('#e2ba36'),
          position:loc,
          title:name
        });
        markers.push(marker);
        
         marker.addListener('mouseover',function(){
         	var content=String(name);
        infowindow.setContent(content);
        infowindow.open(map, this);});
        marker.addListener('mouseout', function() {
    		infowindow.close();
			});	
        google.maps.event.addListener(marker, 'click', function() { 
        calculateDisplayRoute(l1,l2);});
     	}
       
        function  calculateDisplayRoute(latitude,longitude){
        	
        	var location={lat:latitude,lng:longitude};
        	dest=location;
        	var service = new google.maps.DistanceMatrixService();
       		var source=CurrPosition;
     	  
    service.getDistanceMatrix({
        origins: [source],
        destinations: [location],
        travelMode: google.maps.TravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.METRIC,
        avoidHighways: false,
        avoidTolls: false
    }, function (response, status) {
        if (status == google.maps.DistanceMatrixStatus.OK && response.rows[0].elements[0].status != "ZERO_RESULTS") {
            var distance = response.rows[0].elements[0].distance.text;
            var duration = response.rows[0].elements[0].duration.text;
            var p=document.getElementById("distance");
            var q=document.getElementById("duration");
            p.innerHTML=distance;
            q.innerHTML=duration;
			//alert("The Distance is "+distance+" The Duration is "+duration);
 
        } else {
            alert("Unable to find the distance via road.");
        }
    });
		clearMarkers(null);
	   var marker = new google.maps.Marker({
          map: map,
          icon: pinSymbol('#e2ba36'),
          position: dest
        });
        markers.push(marker);
	   
	    directionsService.route({
          origin: CurrPosition,
          destination:dest,
          travelMode: 'DRIVING'
        }, function(response, status) {
          if (status === 'OK') {
            directionsDisplay.setDirections(response);
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
        	
        	
        }
        function calculateDistance(place) {
	
		
         var service = new google.maps.DistanceMatrixService();
       var source=CurrPosition;
       var destination={lat:place.lat(),lng:place.lng()};
    service.getDistanceMatrix({
        origins: [source],
        destinations: [destination],
        travelMode: google.maps.TravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.METRIC,
        avoidHighways: false,
        avoidTolls: false
    }, function (response, status) {
        if (status == google.maps.DistanceMatrixStatus.OK && response.rows[0].elements[0].status != "ZERO_RESULTS") {
            var distance = response.rows[0].elements[0].distance.text;
            var duration = response.rows[0].elements[0].duration.text;
            var p=document.getElementById("distance");
            var q=document.getElementById("duration");
            p.innerHTML=distance;
            q.innerHTML=duration;
			//alert("The Distance is "+distance+" The Duration is "+duration);
 
        } else {
            alert("Unable to find the distance via road.");
        }
    });
		 
       }
	   
	   function setcenter(){
	    map.setZoom(13);
	   }
	   function calculateAndDisplayRoute(directionsService, directionsDisplay){
	   clearMarkers(null);
	   var marker = new google.maps.Marker({
          map: map,
          icon: pinSymbol('#2c76f7'),
          position: dest
        });
        markers.push(marker);
	   
	    directionsService.route({
          origin: CurrPosition,
          destination:dest,
          travelMode: 'DRIVING'
        }, function(response, status) {
          if (status === 'OK') {
            directionsDisplay.setDirections(response);
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
	   
	   
	   }
          
    
          
    </script>
  </head>
  <body>
  <script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '741169406052450',
      cookie     : true,
      xfbml      : true,
      version    : 'v2.8'
    });
    FB.AppEvents.logPageView();   
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>

  
  <div class="radiusBox">
  Enter The Coverage Radius:<input type="number" min="1" max="50" id="radius" placeholder="10 Km" >
  <input type="button" value="Set" id="setbtn" onclick="setRadius();">
  <input id="nbsearch" type="button" value="Near By Search" onclick="nbSearch();">
   Distance Between Two places is:<br /><div id="distance"></div>
   
   <input type="button" id="sbutton" name="retrieve" onclick="displayMarkers();" value="Fetch records from Database" ><br/>
  Duration is:<br /><div id="duration" ></div>
  <a class="button" href="#insertData">Insert new Places into Database</a>
  </div>
<div class="fb-like" data-href="https://developers.facebook.com/docs/plugins/" data-width="60" data-layout="button_count" data-action="like" data-size="small" data-show-faces="true" data-share="true"></div>

<div class="container" id="insertData">
<div class="popup">
<a class="close" href="#">&times;</a>
<h4>ENTER DATA INTO DATABASE:</h4><br />
<form action="/insert.php" method="get">
Latitude:<input type="text" min="-180" max="180" id="lat" name="Latitude" required><br />
Longitude:<input type="text" min="-180" max="180"id="lng" name="Longitude" required><br />
Place Name:<input type="text"id="place" name="Place" required><br />
<input type="submit" value="Insert" name="Ibtn">
</form>
</div>
 </div> 
  
  <input id="pac-input" class="controls" type="text" placeholder="Search Places">
  

    <div id="map"></div>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBst_jfYDILuznhnBVQYGogDZdYK9xTlGU&libraries=places&callback=initMap" async defer></script>
    
    
   
   
  </body>
</html>
