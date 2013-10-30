/*
	*	Main Javascript file
	* 	MobSci Implementation
	*	Author: Zill Christian
*/


	// Global Long & Lat
	longitude = 0;
	latitude = 0;
	curr_longitude = 0;
	curr_latitude = 0;
	adtFields = 0;

	// Init vars
	prev_infowindow = null;
	tagDifferentLocation = false;
	points = new Array();

	// On document ready
	$(function() {
		// When the main buttons are clicked
		$('.mainBtns .btn').on('click', function() {
			$('.mainBtns .btn').removeClass('currentBtn');
			$(this).addClass('currentBtn');
		});

		// JS Hack to launch the filebrowser when the custom button is clicked
		$('#uploadPreview').click(function() {
			$('#uploadImage').click();
			return false;
		});

		// grab the token from the cookie
		var token = $.cookie('mobsci_token');

		// If the cookie doesn't exist, send back to login to recreate the token
		if (typeof token === 'undefined') {
			window.location.href = "../";
		}

		// Getting all the info on the user
		$.post('http://zillwc.com/mobsci/scripts/getUserInfo.php', {token:token}, function(data) {
			data = jQuery.parseJSON(data);

			if ($.trim(data.status)=='true') {
				var User = data.user;
				$('#userEmail').html(User.email);
				$.cookie('mobsci_user_name', User.name, {expires: 5});
				$.cookie('mobsci_user_email', User.email, {expires: 5});

			} else {
				window.location.href = "../";
			}
		});

		// Getting all of the users plots
		$.post('http://zillwc.com/mobsci/scripts/getData.php', {token:token} , function(data) {
			data = jQuery.parseJSON(data);
			if ($.trim(data.status)=='true') {
				points = data.plots;
				initialize();
				closeSplashScreen();
			} else {
				window.location.href = "../";
			}
		});
	});

	// Function closes the splash screen within 4 seconds after all the data is loaded
	function closeSplashScreen() {
		setTimeout(function(){MetroPreLoader.close();}, 4000);
	}

	/* When the submit button is clicked
	$('#submit').click(function() {
		btnSubmit();
	});*/

	function btnSubmit() {
		var img = $('#uploadPreview').attr('src');
		var name = $('#tagTitle').val();
		var desc = $('#tagDesc').val();

		LatLng = new google.maps.LatLng(curr_latitude, curr_longitude);
	    var marker = new google.maps.Marker({
	        position: LatLng,
	        map: map,
	        animation: google.maps.Animation.DROP
	    });

	    getConditions(curr_latitude, curr_longitude);

	    lat = Math.round(curr_latitude * 1000)/1000;
		lng = Math.round(curr_longitude * 1000)/1000;

	    var infoWindow = new google.maps.InfoWindow();
	    var mContent = '<div class="infoWin"><p class="lead">'+name+'</p><hr /><img src="'+img+'" class="infoImg"><p class="text-info">Description: <span class="muted">'+desc+'</span></p><p class="text-info">Latitude: <span class="muted">'+lat+'</span></p><p class="text-info">Longitude: <span class="muted">'+lng+'</span></p></div>';

	    $('#addForm').modal('hide');

	    google.maps.event.addListener(marker, 'click', function () {
	        var markerContent = mContent;
	        infoWindow.setContent(markerContent);

		    if (prev_infowindow)
		    	prev_infowindow.close();

		    prev_infowindow = infoWindow;
	        infoWindow.open(map, this);
	    });
	}

	function addField() {
		var row = '<div class="row"><div class="col-sm-12"><button type="button" class="close closeFields">&times;</button><div class="form-group adtEntry"><input type="text" class="form-control" id="tagTitle" placeholder="Enter a name for this field.." style="margin-bottom:0px;"></div><div class="form-group adtEntry"><input type="text" class="form-control adtEntry" id="tagTitle" placeholder="Enter a value for this field.." style="margin-bottom:0px;"></div><div class="form-group adtEntry"><textarea rows="3" class="form-control adtEntry" placeholder="Enter any notes about this data.."></textarea></div></div></div>';

		if (adtFields = 0) {
			$('#form').append('<hr /><p>Additional Data</p>'+row);
		} else {
			$('#form').append('<br />'+row);
		}
		adtFields++;
	}

	$('.closeFields').on('click', function() {
		var ele1 = $(this).prev().prev();
		$(ele1).remove();
	});

	/* When the add field button inside the modal is clicked
	$('#addField').on('click', function() {
		alert("boom");
	});*/


	// Function initilizes the map with the users location
	function initialize() {
		var myOptions = {
	    	zoom: 7,
	    	disableDefaultUI: true,
	    	mapTypeId: google.maps.MapTypeId.ROADMAP
	    };
	    map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);

	  	if(navigator.geolocation) {
	    	navigator.geolocation.getCurrentPosition(function(position) {
		    	longitude = position.coords.longitude;
		    	latitude = position.coords.latitude;

		    	curr_longitude = longitude;
		    	curr_latitude = latitude;

		    	initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
		    	map.setCenter(initialLocation);
		    }, function() {
				$('#map-canvas').html('<h1 class="text-error">Geolocation is not supported by this browser.</h1>');
			});
	    }
	  	else {
	    	$('#map-canvas').html('<h1 class="text-error">Geolocation is not supported by this browser.</h1>');
	    }

	    fillMap();
	}

	// Fills the map with the plots
	function fillMap() {
		for (var i=0;i<points.length;i++) {
			var plot = points[i];

			var marker = new google.maps.Marker({
	            position: new google.maps.LatLng($.trim(plot.latitude), $.trim(plot.longitude)),
	            map: map,
	            title: plot.title
	        });

	        var content = makeContent(plot);
	        addInfoWindow(marker, content);

		    // Zoom to marker once it is clicked
		    google.maps.event.addListener(marker, 'click', function() {
			    map.setZoom(8);
			    map.setCenter(marker.getPosition());
			});
		}
	}
	// Function adds content to the info window
	function addInfoWindow(marker, message) {
	    var infoWindow = new google.maps.InfoWindow({
	        content: message
	    });

	    google.maps.event.addListener(marker, 'click', function() {
		    if (prev_infowindow)
		    	prev_infowindow.close();

		    prev_infowindow = infoWindow;
	        infoWindow.open(map, marker);
	    });
	}
	// Function makes and returns contents for info window
	function makeContent(plot) {
	    var title = plot.title;
	    var lat = plot.latitude;
	    var lng = plot.longitude;

	    var html = '<div class="infoWin"><p class="lead">'+$.trim(title)+'</p><hr /><p class="text-info">Latitude: <span class="text-muted">'+$.trim(lat)+'</span></p><p class="text-info">Longitude: <span class="text-muted">'+lng+'</span></p>';

	    var fields = plot.fields;

	    for (var i=0;i<fields.length;i++) {
	    	var field = fields[i].field;
	    	var data = fields[i].data;
	    	var notes = fields[i].notes;

	    	if ($.trim(notes) !== '') {
		        html += '<br /><p class="text-info">'+$.trim(field)+': <span class="text-muted">'+$.trim(data)+'</span><br /><p class="text-info">Notes: <span class="text-muted">'+$.trim(notes)+'</span></p>';
	    	} else {
		    	html += '<br /><p class="text-info">'+$.trim(field)+': <span class="text-muted">'+$.trim(data)+'</span></p>';
	    	}
	    }
	    html += '</div>';
	    return html;
	}

	// Function previews the image onto the specified div after upload
	function PreviewImage() {
		oFReader = new FileReader();
		oFReader.readAsDataURL(document.getElementById("uploadImage").files[0]);
		oFReader.onload = function (oFREvent) {
			$('#uploadPreview').attr('src', oFREvent.target.result);
		    imageUploaded = true;
		};
	};

	// Returns the weather conditions for the lat/lng
	function getConditions(lat, lng) {
		var observation = "false";
		$.ajax({
			url : "http://api.wunderground.com/api/31bd33db42e424b8/geolookup/conditions/q/"+lat+","+lng+".json",
			dataType : "jsonp",
			success : function(data) {
				observation = data['current_observation'];
				console.log(observation);
			},
			error :	function(data) {
				alert(data);
			}
		});
	}


	// Find me function centers on user's location
	function focusOnMe() {
		map.setCenter(initialLocation);
		map.setZoom(8);
	}

	// Updates the current form with the most recent lat/lng
	function updateForm() {
		$('#latlong').html('('+curr_latitude+','+curr_longitude+')');
		$('#latitude').val(curr_latitude);
		$('#longitude').val(curr_longitude);
		$('#token').val($.cookie('mobsci_token'));
	}


	var LocationHandler = "";

	// Enables users to plot on different location
	function enableDifferentLocation() {
		tagDifferentLocation = true;
		$('#addTagText').html("Click a location on map to add tag");

		LocationHandler = google.maps.event.addListener(map, 'click', function(event) {
			curr_latitude = event.latLng.lat();
			curr_longitude = event.latLng.lng();
			updateForm();
		    disableDifferentLocation();
		    $('#addForm').modal('show');
		});
	}
	function disableDifferentLocation() {
		tagDifferentLocation = false;
		google.maps.event.removeListener(LocationHandler);
		$('#addTagText').html("Add new tag");
		$('#addForm').modal('show');
	}

