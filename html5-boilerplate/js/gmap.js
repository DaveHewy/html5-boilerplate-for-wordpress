var map;
var localSearch = new GlocalSearch();

var icon = new GIcon();
icon.image = "http://www.google.com/mapfiles/marker.png";
icon.shadow = "http://www.google.com/mapfiles/shadow50.png";
icon.iconSize = new GSize(20, 34);
icon.shadowSize = new GSize(37, 34);
icon.iconAnchor = new GPoint(10, 34);


function usePointFromPostcode(postcode, mylat, mylong, callbackFunction) {
	
	localSearch.setSearchCompleteCallback(null, 
		function() {
						
			if (localSearch.results[0])
			{	
				if(mylat != '' && mylong != ''){
					var resultLat = mylat;
					var resultLng = mylong;
				} else {
					var resultLat = localSearch.results[0].lat;
					var resultLng = localSearch.results[0].lng;
				}
				var point = new GLatLng(resultLat,resultLng);
				callbackFunction(point,postcode,mylat,mylong);
			}
		});	
		
	localSearch.execute(postcode + ", UK");
}

function placeMarkerAtPoint(point,postcode, mylat, mylong)
{
	var marker = new GMarker(point,icon);
	map.addOverlay(marker);
	
	usePointFromPostcode(postcode, mylat, mylong, setCenterToPoint);

}

function setCenterToPoint(point,postcode, mylat, mylong)
{
	if(mylat == "" && mylong == ""){
		map.setCenter(point, 17);
	} else {
		map.setCenter(point, 19);
	}
	
	usePointFromPostcode(postcode, mylat, mylong, showPointLatLng);
}


function showPointLatLng(point,mylat,mylong)
{
	if(mylat == "" && mylong == ""){
		document.getElementById("longitude").value = point.lng();
		document.getElementById("latitude").value = point.lat();
	} else {
		document.getElementById("longitude").value = mylong;
		document.getElementById("latitude").value = mylat;
	}
}


function doAll(postcode,mylat,mylong){
	usePointFromPostcode(postcode, mylat, mylong, placeMarkerAtPoint);
}

function mapLoad() {
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("map"));
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		map.setCenter(new GLatLng(54.622978,-2.592773), 5, G_HYBRID_MAP);
		setTimeout('initiateMap()',500);
	}
}

function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      func();
    }
  }
}

function addUnLoadEvent(func) {
	var oldonunload = window.onunload;
	if (typeof window.onunload != 'function') {
	  window.onunload = func;
	} else {
	  window.onunload = function() {
	    func();
	  }
	}
}