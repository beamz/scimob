
var geometric = [
  {
    featureType: "all",
    stylers: [
      { saturation: -80 }
    ]
  },{
    featureType: "road.arterial",
    elementType: "geometry",
    stylers: [
      { hue: "#00ffee" },
      { saturation: 50 }
    ]
  },{
    featureType: "poi.business",
    elementType: "labels",
    stylers: [
      { visibility: "off" }
    ]
  }
];


var simplified = [
  {
    stylers: [
      { hue: "#00ffe6" },
      { saturation: -20 }
    ]
  },{
    featureType: "road",
    elementType: "geometry",
    stylers: [
      { lightness: 100 },
      { visibility: "simplified" }
    ]
  },{
    featureType: "road",
    elementType: "labels",
    stylers: [
      { visibility: "off" }
    ]
  }
];


function changeMap(mapType) {
	switch (mapType) {
		case 'roadmap':
			map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
			break;
		case 'terrain':
			map.setMapTypeId(google.maps.MapTypeId.TERRAIN)
			break;
		case 'hybrid':
			map.setMapTypeId(google.maps.MapTypeId.HYBRID)
			break;
		case 'satellite':
			map.setMapTypeId(google.maps.MapTypeId.SATELLITE)
			break;
		case 'geometric':
			map.setOptions({styles: geometric});
			break;
		case 'simplified':
			map.setOptions({styles: simplified});
			break;
	}
}