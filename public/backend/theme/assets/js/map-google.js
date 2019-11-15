'use strict';
(function($) {
  /**
    * Function to load simple google map
  **/
  if ($(".gmap_basic").length > 0){
    new GMaps({
    		div: '.gmap_basic',
    		lat: -12.043333,
    		lng: -77.028333
  	});
  }
  /**
    * Function to load events google map
  **/
  if ($(".map_event").length > 0){
    var map = new GMaps({
      div: '.map_event',
      zoom: 16,
      lat: -12.043333,
      lng: -77.028333,
      click: function(e) {
        alert('click');
      },
      dragend: function(e) {
        alert('dragend');
      }
    });
  }

  /**
    * Function to load markers google map
  **/
  if ($(".map_marker").length > 0){
    	var mapmarker = new GMaps({
    		div: '.map_marker',
    	  	zoom: 16,
    	  	lat: -12.043333,
    	  	lng: -77.028333,
    	});
    	mapmarker.addMarker({
    	 	lat: -12.043333,
    	  	lng: -77.028333,
    	  	title: 'Lima',
    	  	click: function(e) {
    	    	alert('You clicked in this marker');
    	  	},
    	});
  }

  /**
    * Function to load geolocation google map
  **/
  if ($(".map_gl").length > 0){
    	var mapgl = new GMaps({
    		div: '.map_gl',
    	  	zoom: 16,
    	  	lat: -12.043333,
    	  	lng: -77.028333,
    	});
  }

  /**
    * Function to load geocoding google map
  **/
  if ($(".map_geo").length > 0){
    	var mapgeo = new GMaps({
            div: '.map_geo',
            lat: -12.043333,
            lng: -77.028333
        });
		$('#geocoding_form').submit(function(e){
		e.preventDefault();
			GMaps.geocode({
				address: $('#address').val().trim(),
				callback: function(results, status){
				 if(status=='OK'){
					var latlng = results[0].geometry.location;
					mapgeo.setCenter(latlng.lat(), latlng.lng());
					mapgeo.addMarker({
					  lat: latlng.lat(),
					  lng: latlng.lng()
					});
				 }
				}
			});
		});
	}

	/**
	  * Function to load polylines google map
	**/
	if ($(".map_pol").length > 0){
		var path = [[-12.044012922866312, -77.02470665341184], [-12.05449279282314, -77.03024273281858], [-12.055122327623378, -77.03039293652341], [-12.075917129727586, -77.02764635449216], [-12.07635776902266, -77.02792530422971], [-12.076819390363665, -77.02893381481931], [-12.088527520066453, -77.0241058385925], [-12.090814532191756, -77.02271108990476]];
		var mappol = new GMaps({
			 div: '.map_pol',
			 lat: -12.043333,
			 lng: -77.028333
		});
		mappol.drawPolyline({
			path: path,
			strokeColor: '#131540',
			strokeOpacity: 0.6,
			strokeWeight: 6
		});
	}

	/**
	  * Function to load overlays google map
	**/
	if ($(".map_over").length > 0){
		var mapover = new GMaps({
				 div: '.map_over',
				 lat: -12.043333,
				 lng: -77.028333
			});
		 mapover.drawOverlay({
				 lat: mapover.getCenter().lat(),
				 lng: mapover.getCenter().lng(),
				 content: '<div class="overlay">Lima<div class="overlay_arrow above"></div></div>',
				 verticalAlign: 'top',
				 horizontalAlign: 'center'
		 });
	}

	/**
	  * Function to load polygons google map
	**/
	if ($(".map_poly").length > 0){
		 var mappoly = new GMaps({
			  div: '.map_poly',
			  lat: -12.043333,
			  lng: -77.028333
		 });
	
		  var path =  [[-12.040397656836609,-77.03373871559225],
					[-12.040248585302038,-77.03993927003302],
					[-12.050047116528843,-77.02448169303511],
					[-12.044804866577001,-77.02154422636042]
					];
		 var polygon = mappoly.drawPolygon({
					paths: path,
					strokeColor: '#BBD8E9',
					strokeOpacity: 1,
					strokeWeight: 3,
					fillColor: '#BBD8E9',
					fillOpacity: 0.6
			});
	}

	/**
	  * Function to load geojson polygons google map
	**/
	if ($(".map_geojson").length > 0){
		 var mapgeojson = new GMaps({
			  div: '.map_geojson',
			  lat: 39.743296277167325,
			  lng: -105.00517845153809
		 });
	
		 var paths = [
					[
					  [
						 [-105.00432014465332, 39.74732195489861],
						 [-105.00715255737305, 39.74620006835170],
						 [-105.00921249389647, 39.74468219277038],
						 [-105.01067161560059, 39.74362625960105],
						 [-105.01195907592773, 39.74290029616054],
						 [-105.00989913940431, 39.74078835902781],
						 [-105.00758171081543, 39.74059036160317],
						 [-105.00346183776855, 39.74059036160317],
						 [-105.00097274780272, 39.74059036160317],
						 [-105.00062942504881, 39.74072235994946],
						 [-105.00020027160645, 39.74191033368865],
						 [-105.00071525573731, 39.74276830198601],
						 [-105.00097274780272, 39.74369225589818],
						 [-105.00097274780272, 39.74461619742136],
						 [-105.00123023986816, 39.74534214278395],
						 [-105.00183105468751, 39.74613407445653],
						 [-105.00432014465332, 39.74732195489861]
					  ],[
						 [-105.00361204147337, 39.74354376414072],
						 [-105.00301122665405, 39.74278480127163],
						 [-105.00221729278564, 39.74316428375108],
						 [-105.00283956527711, 39.74390674342741],
						 [-105.00361204147337, 39.74354376414072]
					  ]
					],[
					  [
						 [-105.00942707061768, 39.73989736613708],
						 [-105.00942707061768, 39.73910536278566],
						 [-105.00685214996338, 39.73923736397631],
						 [-105.00384807586671, 39.73910536278566],
						 [-105.00174522399902, 39.73903936209552],
						 [-105.00041484832764, 39.73910536278566],
						 [-105.00041484832764, 39.73979836621592],
						 [-105.00535011291504, 39.73986436617916],
						 [-105.00942707061768, 39.73989736613708]
					  ]
					]
				 ];
	
			var polygon = mapgeojson.drawPolygon({
				 paths: paths,
				 useGeoJSON: true,
				 strokeColor: '#BBD8E9',
				 strokeOpacity: 1,
				 strokeWeight: 3,
				 fillColor: '#BBD8E9',
				 fillOpacity: 0.6
			});
	}
	
	/**
	  * Function to load routes google map
	**/
	if ($(".map_routes").length > 0){
		 var mapRoutes = new GMaps({
			  div: '.map_routes',
			  lat: -12.043333,
			  lng: -77.028333
		 });
		 mapRoutes.drawRoute({
			  origin: [-12.044012922866312, -77.02470665341184],
			  destination: [-12.090814532191756, -77.02271108990476],
			  travelMode: 'driving',
			  strokeColor: '#131540',
			  strokeOpacity: 0.6,
			  strokeWeight: 6
		 });
	}
	
	/**
	  * Function to load routes(advanced) google map
	**/
	if ($(".map_routesad").length > 0){
		 var mapRoutesAd = new GMaps({
			  div: '.map_routesad',
			  lat: -12.043333,
			  lng: -77.028333
		 });
		 $('#start_travel').on('click',function(e){
			  e.preventDefault();
			  mapRoutesAd.travelRoute({
				 origin: [-12.044012922866312, -77.02470665341184],
				 destination: [-12.090814532191756, -77.02271108990476],
				 travelMode: 'driving',
				 step: function(e){
					$('#instructions').append('<li>'+e.instructions+'</li>');
					$('#instructions li:eq('+e.step_number+')').delay(450*e.step_number).fadeIn(200, function(){
					  mapRoutesAd.setCenter(e.end_location.lat(), e.end_location.lng());
					  mapRoutesAd.drawPolyline({
						 path: e.path,
						 strokeColor: '#131540',
						 strokeOpacity: 0.6,
						 strokeWeight: 6
					  });
					});
				 }
			  });
		 });
	}
	
	/**
	  * Function to load context menu google map
	**/
	if ($(".map_context").length > 0){
		 var mapContext = new GMaps({
			  div: '.map_context',
			  lat: -12.043333,
			  lng: -77.028333
		 });
		 mapContext.setContextMenu({
			  control: 'map',
			  options: [{
				 title: 'Add marker',
				 name: 'add_marker',
				 action: function(e){
					this.addMarker({
					  lat: e.latLng.lat(),
					  lng: e.latLng.lng(),
					  title: 'New marker'
					});
					this.hideContextMenu();
				 }
			  }, {
				 title: 'Center here',
				 name: 'center_here',
				 action: function(e){
					this.setCenter(e.latLng.lat(), e.latLng.lng());
				 }
			  }]
		 });
	}
	
	/**
	  * Function to load geofences google map
	**/
	if ($(".map_geofences").length > 0){
		 var mapGeofences = new GMaps({
			  div: '.map_geofences',
			  lat: -12.043333,
			  lng: -77.028333
		 });
		 var pathGeofences=[];
		 var p = [[-12.040397656836609,-77.03373871559225],[-12.040248585302038,-77.03993927003302],[-12.050047116528843,-77.02448169303511],[-12.044804866577001,-77.02154422636042]];
		 for(var i in p){
			  var latlng = new google.maps.LatLng(p[i][0], p[i][1]);
			  pathGeofences.push(latlng);
		 }
		 var polygon = mapGeofences.drawPolygon({
			  paths: pathGeofences,
			  strokeColor: '#BBD8E9',
			  strokeOpacity: 1,
			  strokeWeight: 3,
			  fillColor: '#BBD8E9',
			  fillOpacity: 0.6
			});
			mapGeofences.addMarker({
			  lat: -12.043333,
			  lng: -77.028333,
			  draggable: true,
			  fences: [polygon],
			  outside: function(m, f){
				 alert('This marker has been moved outside of its fence');
			  }
		 });
	}
	
	/**
	  * Function to load custom control google map
	**/
	if ($(".map_cctrl").length > 0){
		 var mapCustomctrl = new GMaps({
			  div: '.map_cctrl',
			  zoom: 16,
			  lat: -12.043333,
			  lng: -77.028333
		 });
		 mapCustomctrl.addControl({
			  position: 'top_right',
			  content: 'Geolocate',
			  style: {
				 margin: '5px',
				 padding: '1px 6px',
				 border: 'solid 1px #717B87',
				 background: '#fff'
			  },
			  events: {
				 click: function(){
					GMaps.geolocate({
					  success: function(position){
						 mapCustomctrl.setCenter(position.coords.latitude, position.coords.longitude);
					  },
					  error: function(error){
						 alert('Geolocation failed: ' + error.message);
					  },
					  not_supported: function(){
						 alert("Your browser does not support geolocation");
					  }
					});
				 }
			  }
		 });
	}
	
	/**
	  * Function to load fusion table layers google map
	**/
	if ($(".map_ftable").length > 0){
		 var infoWindow = new google.maps.InfoWindow({});
		 var mapFtable = new GMaps({
			  div: '.map_ftable',
			  zoom: 11,
			  lat: 41.850033,
			  lng: -87.6500523
		 });
		 mapFtable.loadFromFusionTables({
			  query: {
				 select: '\'Geocodable address\'',
				 from: '1mZ53Z70NsChnBMm-qEYmSDOvLXgrreLTkQUvvg'
			  },
			  suppressInfoWindows: true,
			  events: {
				 click: function(point){
					infoWindow.setContent('You clicked here!');
					infoWindow.setPosition(point.latLng);
					infoWindow.open(mapFtable.map);
				 }
			  }
		 });
	}
	
	/**
	  * Function to load kml layers google map
	**/
	if ($(".map_kml").length > 0){
		 var infoWindow2 = new google.maps.InfoWindow({});
		 var mapKlayers = new GMaps({
			  div: '.map_kml',
			  zoom: 12,
			  lat: 40.65,
			  lng: -73.95
		 });
		 mapKlayers.loadFromKML({
			  url: 'http://api.flickr.com/services/feeds/geo/?g=322338@N20&lang=en-us&format=feed-georss',
			  suppressInfoWindows: true,
			  events: {
				 click: function(point){
					infoWindow2.setContent(point.featureData.infoWindowHtml);
					infoWindow2.setPosition(point.latLng);
					infoWindow2.open(mapKlayers.map);
				 }
			  }
		 });
	}
	
	/**
	  * Function to load map types google map
	**/
	if ($(".map_mtypes").length > 0){
		var mapTypes = new GMaps({
			  div: '.map_mtypes',
			  lat: -12.043333,
			  lng: -77.028333,
			  mapTypeControlOptions: {
				 mapTypeIds : ["hybrid", "roadmap", "satellite", "terrain", "osm"]
			  }
		 });
		 mapTypes.addMapType("osm", {
			  getTileUrl: function(coord, zoom) {
				 return "https://a.tile.openstreetmap.org/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
			  },
			  tileSize: new google.maps.Size(256, 256),
			  name: "OpenStreetMap",
			  maxZoom: 18
		 });
		 mapTypes.setMapTypeId("osm");
	}
	
	/**
	  * Function to load overlay map types google map
	**/
	if ($(".map_omtypes").length > 0){
		 var getTile = function(coord, zoom, ownerDocument) {
			 var div = ownerDocument.createElement('div');
			 div.innerHTML = coord;
			 div.style.width = this.tileSize.width + 'px';
			 div.style.height = this.tileSize.height + 'px';
			 div.style.background = 'rgba(250, 250, 250, 0.55)';
			  div.style.fontFamily = 'Monaco, Andale Mono, Courier New, monospace';
			  div.style.fontSize = '10';
			  div.style.fontWeight = 'bolder';
			  div.style.border = 'dotted 1px #aaa';
			  div.style.textAlign = 'center';
			  div.style.lineHeight = this.tileSize.height + 'px';
			  return div;
		 };
	
		 var mapOverlayTypes = new GMaps({
			  el: '.map_omtypes',
			  lat: -12.043333,
			  lng: -77.028333
		 });
		 mapOverlayTypes.addOverlayMapType({
			  index: 0,
			  tileSize: new google.maps.Size(256, 256),
			  getTile: getTile
		 });
	}
	
	/**
	  * Function to load street view google map
	**/
	if ($("#map_street").length > 0){
		 var panorama = GMaps.createPanorama({
			  el: '#map_street',
			  lat : 42.3455,
			  lng : -71.0983
		 });
	}
		 
	/**
	  * Function to load interacting with ui google map
	**/
	if ($(".map_ui").length > 0){
		 var mapUi = new GMaps({
			  div: '.map_ui',
			  lat: -12.043333,
			  lng: -77.028333
		 });
	
			GMaps.on('marker_added', mapUi, function(marker) {
			  $('#markers-with-index').append('<li><a href="#" class="pan-to-marker" data-marker-index="' + mapUi.markers.indexOf(marker) + '">' + marker.title + '</a></li>');
	
			  $('#markers-with-coordinates').append('<li><a href="#" class="pan-to-marker" data-marker-lat="' + marker.getPosition().lat() + '" data-marker-lng="' + marker.getPosition().lng() + '">' + marker.title + '</a></li>');
			});
	
			GMaps.on('click', mapUi.map, function(event) {
			  var index = mapUi.markers.length;
			  var lat = event.latLng.lat();
			  var lng = event.latLng.lng();
	
			  var template = $('#edit_marker_template').text();
	
			  var content = template.replace(/{{index}}/g, index).replace(/{{lat}}/g, lat).replace(/{{lng}}/g, lng);
	
			  mapUi.addMarker({
				 lat: lat,
				 lng: lng,
				 title: 'Marker #' + index,
				 infoWindow: {
					content : content
				 }
			  });
			});
	}
	
	$(document).on('submit', '.edit_marker', function(e) {
      e.preventDefault();
      var $index = $(this).data('marker-index');
      $lat = $('#marker_' + $index + '_lat').val();
      $lng = $('#marker_' + $index + '_lng').val();
      var template = $('#edit_marker_template').text();
      // Update form values
      var content = template.replace(/{{index}}/g, $index).replace(/{{lat}}/g, $lat).replace(/{{lng}}/g, $lng);
      mapUi.markers[$index].setPosition(new google.maps.LatLng($lat, $lng));
      mapUi.markers[$index].infoWindow.setContent(content);
      $marker = $('#markers-with-coordinates').find('li').eq(0).find('a');
      $marker.data('marker-lat', $lat);
      $marker.data('marker-lng', $lng);
	});
	
	// Update center
    $(document).on('click', '.pan-to-marker', function(e) {
      e.preventDefault();

      var lat, lng;

      var $index = $(this).data('marker-index');
      var $lat = $(this).data('marker-lat');
      var $lng = $(this).data('marker-lng');

      if ($index != undefined) {
        // using indices
        var position = mapUi.markers[$index].getPosition();
        lat = position.lat();
        lng = position.lng();
      }
      else {
        // using coordinates
        lat = $lat;
        lng = $lng;
      }

      mapUi.setCenter(lat, lng);
    });
})(jQuery); 