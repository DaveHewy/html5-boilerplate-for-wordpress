/*
 * Developed by Elliot Reeve
 */
function PolygonCreator(map) {
    this.map = map;
    this.pen = new Pen(this.map);
    var thisOjb = this;
    this.event = google.maps.event.addListener(thisOjb.map, 'click', function(event) {
        thisOjb.pen.draw(event.latLng);
    });
    this.showData = function() {
        return this.pen.getData();
    }
    this.showColor = function() {
        return this.pen.getColor();
    }
    this.destroy = function() {
        this.pen.deleteMis();
        if (null != this.pen.polygon) {
            this.pen.polygon.remove();
        }
        google.maps.event.removeListener(this.event);
    }
}

function Pen(map) {
    this.map = map;
    this.listOfDots = new Array();
    this.polyline = null;
    this.polygon = null;
    this.currentDot = null;
    this.draw = function(latLng) {
        if (null != this.polygon) {
            alert('Click Reset to draw another');
        } else {
            if (this.currentDot != null && this.listOfDots.length > 1 && this.currentDot == this.listOfDots[0]) {
                this.drawPloygon(this.listOfDots);
            } else {
                if (null != this.polyline) {
                    this.polyline.remove();
                }
                
                //This is the first dot, change the status
                if(this.listOfDots == ''){
					jQuery("#calculatorvisual_1").hide();
					jQuery("#calculatorstep_1").addClass("stepcomplete");
					jQuery("#calculatorstep_2").removeClass("stepawaiting");
					jQuery("#calculatorvisual_2").show();
                }
                
                var dot = new Dot(latLng, this.map, this);
                this.listOfDots.push(dot);
                if (this.listOfDots.length > 1) {
                    this.polyline = new Line(this.listOfDots, this.map);
                    
                }
            }
        }
    }
    this.drawPloygon = function(listOfDots, color, des, id) {
        this.polygon = new Polygon(listOfDots, this.map, this, color, des, id);
        this.deleteMis();
    }
    this.deleteMis = function() {
        jQuery.each(this.listOfDots, function(index, value) {
            value.remove();
        });
        this.listOfDots.length = 0;
        if (null != this.polyline) {
            this.polyline.remove();
            this.polyline = null;
        }
    }
    this.cancel = function() {
        if (null != this.polygon) {
            (this.polygon.remove());
        }
        this.polygon = null;
        this.deleteMis();
    }
    this.setCurrentDot = function(dot) {
        this.currentDot = dot;
    }
    this.getListOfDots = function() {
        return this.listOfDots;
    }
    this.getData = function() {
        if (this.polygon != null) {
            var data = "";
            var paths = this.polygon.getPlots();
            paths.getAt(0).forEach(function(value, index) {
                data += (value.toString());
            });
            return data;
        } else {
            return null;
        }
    }
    this.getColor = function() {
        if (this.polygon != null) {
            var color = this.polygon.getColor();
            return color;
        } else {
            return null;
        }
    }
}

function Dot(latLng, map, pen) {
    this.latLng = latLng;
    this.parent = pen;

	var image = '../wp-content/themes/discoversolar/assets/images/dotflag.png';
      
    this.markerObj = new google.maps.Marker({
        position: this.latLng,
        map: map,
        icon: image
    });
    this.addListener = function() {
        var parent = this.parent;
        var thisMarker = this.markerObj;
        var thisDot = this;
        google.maps.event.addListener(thisMarker, 'click', function() {
            parent.setCurrentDot(thisDot);
            parent.draw(thisMarker.getPosition());
        });
    }
    this.addListener();
    this.getLatLng = function() {
        return this.latLng;
    }
    this.getMarkerObj = function() {
        return this.markerObj;
    }
    this.remove = function() {
        this.markerObj.setMap(null);
    }
}

function Line(listOfDots, map) {
    this.listOfDots = listOfDots;
    this.map = map;
    this.coords = new Array();
    this.polylineObj = null;
    if (this.listOfDots.length > 1) {
        var thisObj = this;
        jQuery.each(this.listOfDots, function(index, value) {
            thisObj.coords.push(value.getLatLng());
        });
        this.polylineObj = new google.maps.Polyline({
            path: this.coords,
            strokeColor: "#FF0000",
            strokeOpacity: 1.0,
            strokeWeight: 2,
            map: this.map
        });
    }
    this.remove = function() {
        this.polylineObj.setMap(null);
    }
}

/**
 * Creates a point on the earth's surface at the supplied latitude / longitude
 *
 * @constructor
 * @param {Number} lat: latitude in numeric degrees
 * @param {Number} lon: longitude in numeric degrees
 * @param {Number} [rad=6371]: radius of earth if different value is required from standard 6,371km
 */
function LatLon(lat, lon, rad) {
  if (typeof(rad) == 'undefined') rad = 6371;  // earth's mean radius in km
  // only accept numbers or valid numeric strings
  this._lat = typeof(lat)=='number' ? lat : typeof(lat)=='string' && lat.trim()!='' ? +lat : NaN;
  this._lon = typeof(lon)=='number' ? lon : typeof(lon)=='string' && lon.trim()!='' ? +lon : NaN;
  this._radius = typeof(rad)=='number' ? rad : typeof(rad)=='string' && trim(lon)!='' ? +rad : NaN;
}

/**
 * Returns final bearing arriving at supplied destination point from this point; the final bearing 
 * will differ from the initial bearing by varying degrees according to distance and latitude
 *
 * @param   {LatLon} point: Latitude/longitude of destination point
 * @returns {Number} Final bearing in degrees from North
 */
LatLon.prototype.finalBearingTo = function(point) {
	// get initial bearing from supplied point back to this point…
	var lat1 = point._lat.toRad(), lat2 = this._lat.toRad();
	var dLon = (this._lon-point._lon).toRad();
	
	var y = Math.sin(dLon) * Math.cos(lat2);
	var x = Math.cos(lat1)*Math.sin(lat2) -
	      Math.sin(lat1)*Math.cos(lat2)*Math.cos(dLon);
	var brng = Math.atan2(y, x);
	      
	// … & reverse it by adding 180°
	return (brng.toDeg()+180) % 360;
}

function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}

function angleFinder(degrees){
	var facing = new Array();
	
	if(degrees >= 337 && degrees <= 360 || degrees >= 0 && degrees < 22) { facing['direction'] = "North"; facing['roofFaces'] = "n";
	} else if(degrees >= 22 && degrees < 67) { facing['direction'] = "North East"; facing['roofFaces'] = "ne";
	} else if(degrees >= 67 && degrees < 112) { facing['direction'] = "East"; facing['roofFaces'] = "e";
	} else if(degrees >= 112 && degrees < 159) { facing['direction'] = "South East"; facing['roofFaces'] = "se";
	} else if(degrees >= 159 && degrees < 202) { facing['direction'] = "South"; facing['roofFaces'] = "s";
	} else if(degrees >= 202 && degrees < 247) { facing['direction'] = "South West"; facing['roofFaces'] = "sw";
	} else if(degrees >= 247 && degrees < 292) { facing['direction'] = "West"; facing['roofFaces'] = "w";
	} else if(degrees >= 292 && degrees < 337) { facing['direction'] = "North West"; facing['roofFaces'] = "nw"; }

	return facing;
}

function Polygon(listOfDots, map, pen, color) {
    this.listOfDots = listOfDots;
    this.map = map;
    this.coords = new Array();
    this.parent = pen;
    this.des = 'Hello';
    var thisObj = this;
    jQuery.each(this.listOfDots, function(index, value) {
        thisObj.coords.push(value.getLatLng());
    });
    this.polygonObj = new google.maps.Polygon({
        paths: this.coords,
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#FF0000",
        fillOpacity: 0.35,
        map: this.map
    });
    
    /* Pass points */
    jQuery("#polygonpoints").val(this.coords);

	jQuery("#calculatorvisual_2").hide();
	jQuery("#calculatorstep_2").addClass("stepcomplete");
	jQuery("#calculatorstep_3").removeClass("stepawaiting");
	jQuery("#calculatorvisual_3").show();

	var string = this.coords;
	var substr = string.toString().split(')');
	var line1 = substr[0].substring(1, substr[0].length).toString().split(',');
	var line2 = substr[1].substring(2, substr[1].length).toString().split(',');
	var line3 = substr[2].substring(2, substr[2].length).toString().split(',');
	var line4 = substr[3].substring(2, substr[3].length).toString().split(',');
		
	/* Work out the Height and Width of Roof */
	var height = distVincenty(Geo.parseDMS(line1[0]), Geo.parseDMS(line1[1]), Geo.parseDMS(line2[0]), Geo.parseDMS(line2[1]));
	var length = distVincenty(Geo.parseDMS(line2[0]), Geo.parseDMS(line2[1]), Geo.parseDMS(line3[0]), Geo.parseDMS(line3[1]));
			
	/* Work out Area of Roof */
	var area = height * length;
	
	/* Format numbers */
	var newheight = roundNumber(height,2);
	var newheightround = roundNumber(height,0);
	var newlength = roundNumber(length,2);
	var newlengthround = roundNumber(length,0);
	var newarea = roundNumber(area,2);

	var bounds = new google.maps.LatLngBounds();
		
	var i;
	var polygonCoords = this.coords;
	for (i = 0; i < polygonCoords.length; i++) {
	  bounds.extend(polygonCoords[i]);
	}
	
		
	var middlelatlong = bounds.getCenter();
	
	var middlelat = middlelatlong.lat();
	var middlelng = middlelatlong.lng();
	
	var southlong = middlelat-0.00012;
			     
	var marker = new google.maps.Marker({
		map: this.map,
		position: new google.maps.LatLng(middlelat,middlelng),
		draggable: false,
		clickable: false,
		visible: false,
		raiseOnDrag: false
	});
	
	var marker2 = new google.maps.Marker({
		map: this.map,
		position: new google.maps.LatLng(southlong,middlelng),
		draggable: true,
		raiseOnDrag: false
	});
	
	var line = new google.maps.Polyline({
		path: [marker.getPosition(), marker2.getPosition()],
		map: this.map,
		strokeColor: "#66FF66"
	});
	
	var starting = new LatLon(middlelat,middlelng);
	var ending = new LatLon(southlong,middlelng);
	var brngFinal = roundNumber(starting.finalBearingTo(ending,0),0);
	var facing = angleFinder(brngFinal);
	
	jQuery("#roofFaces").val(facing['roofFaces']);
	jQuery("#roofangle").text(facing['direction']+" ("+brngFinal+"°)");
		
	/* Update display */
	if(newheight > newlength){
		newlength_final = newheight;
		newheight_final = newlength;
		
		newlength_final_rounded = roundNumber(newlength_final,0);
		newheight_final_rounded = roundNumber(newheight_final,0);
	} else {
		newlength_final = newlength;
		newheight_final = newheight;
		
		newlength_final_rounded = roundNumber(newlength_final,0);
		newheight_final_rounded = roundNumber(newheight_final,0);
	}

	jQuery("#roofheight").text(newlength_final+"m");
	jQuery("#rooflength").text(newheight_final+"m");
	jQuery("#roofarea").text("("+newarea+"m²)");
	jQuery("#buttons").append('<input type="hidden" name="height" value="'+newheight_final_rounded+'" id="hiddenheight"><input type="hidden" name="width" value="'+newlength_final_rounded+'" id="hiddenlength"><input type="hidden" name="roofFaces" value="'+facing['roofFaces']+'" id="hiddenfaces"><input type ="hidden" name="givenlat" value="'+middlelat+'" id="givenlat"><input type ="hidden" name="givenlong" value="'+middlelng+'" id="givenlong">');

	jQuery(".estimatesubmit").attr("disabled", false);
	
	var update_line_and_display = function(line, display, position1, position2){
		// update the line		
		line.getPath().setAt(0, position1);
		line.getPath().setAt(1, position2);
	}
	
	marker.position_changed = function() {
		update_line_and_display(line, '#distance-display', this.get('position'), marker2.getPosition());
	}
	
	marker2.position_changed = function() {
		update_line_and_display(line, '#distance-display', marker.getPosition(), this.get('position'));
		
		var bearing = this.get('position');
		
		var bearinglng = bearing.lng();
		var bearinglat = bearing.lat();
		
		var starting = new LatLon(middlelat,middlelng);
		var ending = new LatLon(bearinglat,bearinglng);
		var brngFinal = roundNumber(starting.finalBearingTo(ending,0),0);
		var facing = angleFinder(brngFinal);	

		jQuery("#roofFaces").val(facing['roofFaces']);
		jQuery("#roofangle").text(facing['direction']+" ("+brngFinal+"°)");
		jQuery('input[name=roofFaces]').val(facing['roofFaces']);
	}    
   
    this.remove = function() {
        this.info.remove();
        marker2.setMap(null);
        marker.setMap(null);
        line.setMap(null);
        this.polygonObj.setMap(null);
    }
    this.getContent = function() {
        return this.des;
    }
    this.getPolygonObj = function() {
        return this.polygonObj;
    }
    this.getListOfDots = function() {
        return this.listOfDots;
    }
    this.getPlots = function() {
        return this.polygonObj.getPaths();
    }
    this.getColor = function() {
        return this.getPolygonObj().fillColor;
    }
    this.setColor = function(color) {
        return this.getPolygonObj().setOptions({
            fillColor: color,
            strokeColor: color,
            strokeWeight: 2
        });
    }
    this.info = new Info(this, this.map);
    this.addListener = function() {
        var info = this.info;
        var thisPolygon = this.polygonObj;
        google.maps.event.addListener(thisPolygon, 'rightclick', function(event) {
            info.show(event.latLng);
        });
    }
    this.addListener();
}

function Info(polygon, map) {
    this.parent = polygon;
    this.map = map;
    this.color = document.createElement('input');
    this.button = document.createElement('input');
    jQuery(this.button).attr('type', 'button');
    jQuery(this.button).val("Change Color");
    var thisOjb = this;
    this.changeColor = function() {
        thisOjb.parent.setColor(jQuery(thisOjb.color).val());
    }
    this.getContent = function() {
        var content = document.createElement('div');
        jQuery(this.color).val(this.parent.getColor());
        jQuery(this.button).click(function() {
            thisObj.changeColor();
        });
        jQuery(content).append(this.color);
        jQuery(content).append(this.button);
        return content;
    }
    thisObj = this;
    this.infoWidObj = new google.maps.InfoWindow({
        content: thisObj.getContent()
    });
    this.show = function(latLng) {
        this.infoWidObj.setPosition(latLng);
        this.infoWidObj.open(this.map);
    }
    this.remove = function() {
        this.infoWidObj.close();
    }
}