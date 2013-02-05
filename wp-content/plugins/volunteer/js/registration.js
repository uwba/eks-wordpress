jQuery(document).ready(function($){
	$('#volunteer_dialog').dialog({autoOpen:false, width: 980, height: 600});
	$('#facebook_dialog').dialog({autoOpen:false, width: 500, height: 400});
	
	
	$('#volunteer_register').click(function(event){
		event.preventDefault();
		
		$('.back').click(function(){
			gotoStep(--Volunteer.step);
		});
		
		
		$('#volunteer_dialog').dialog('open');
		gotoStep(Volunteer.step);
	
		// populate form
		$('#step1 input').each(function(){
			var name = $(this).attr('name');
			if (name in Volunteer) {
				$('#step1 input[name='+name+']').val(Volunteer[name]);
			}
		});
		$('#step1').ajaxForm({
			data: Volunteer,
			dataType: 'json',
			success : function(response, statusText, xhr, $form) {
//				alert(response + ":::" + response.success + ":::" + statusText + ":::" + response.errors);
				if (response.success) {
//					for (i in response.data ){
//						Volunteer[i] = response.data[i];
//					}
					
					gotoStep(2);
					//populateForm('#step2', response.data);
				} else {
					$('#step1 .errors').html(implode('<br/>', response.errors));
//					console.log(response);
//					alert(implode("\n", response.errors));
				}
			}
		});

		$('#step2').ajaxForm({
			data: Volunteer,
			dataType: 'json',
			success : function(response, statusText, xhr, $form) {
				if (response.success) {
					gotoStep(3);
				} else {
					$('#step2 .errors').html(implode('<br/>', response.errors));
//					console.log(response);
//					alert(implode("\n", response.errors));
				}
				
			}
		});

		$('#step31').ajaxForm({
			data: TaxSearch,
			dataType: 'html',
			success : function(response, statusText, xhr, $form) {
				//for (i=0; i<response.length; i++) {
				//console.log(responseText[i].ID);

				$('#step32 #results').html(toHTML(response));
				$('#volunteer_dialog').dialog("option", "title", "Step Three: Choose a Tax Site");
				//}
			}
		});

		
		$('#step32').ajaxForm({
			data: Volunteer,
			dataType: 'json',
			success : function(response, statusText, xhr, $form) {
//				for (i in response.data ){
//						Volunteer[i] = response.data[i];
//					}
				$('#step4 .trainings').html(response.html);
				gotoStep(4);
			}
		});
		
		$('#step4').ajaxForm({
			data: Volunteer,
			dataType: 'json',
			success : function(response, statusText, xhr, $form) {
//				for (i in response.data ){
//						Volunteer[i] = response.data[i];
//					}
				gotoStep(5);
			}
		});
		
		$('#step5').click(function(event){
			event.preventDefault();
			window.location.href = Volunteer.success_url; //"http://eks.hondosite.com:8181/thank-you";
			//location.reload();
			$('#volunteer_dialog').dialog( "close" );
		});
	});
	
//	$('#facebook_register').click(function(event){
//		event.preventDefault();
//		
//		$('#facebook_dialog').dialog('open');
//		$('#facebook_dialog').dialog("option", "title", "Social Login");
//	
//	});
	
	function gotoStep(step) {
		if (step <= 0) step = 1;
		Volunteer.step = step;
		$('.step .errors').empty();
		$('.step').hide();
		$('#step' + step).show();
		var titles = {
			1: "Step One: Register your account",
			2: "Step Two: Choose a Position",
			3: "Step Three: Choose a Tax Site",
			4: "Step Four: Training",
			5: "Registration is done"
		};
		$('#volunteer_dialog').dialog("option", "title", titles[step]);
	}
	
	$.validator.addMethod("phoneUS", function(phone_number, element) {
		phone_number = phone_number.replace(/\s+/g, ""); 
		return this.optional(element) || phone_number.length > 9 &&
			phone_number.match(/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
	}, "Please specify a valid phone number");

	$("#phone").mask("(999) 999-9999");
	
	$("#step1").validate({
		rules: {
			name: {
				required: true,
				minlength: 3
			},
			username: {
				required: true,
				minlength: 3
			},
			phone: {
				required: true,
//				phoneUS: true,
				minlength: 3
			},
			password: {
				required: true,
				minlength: 3
			},
			password_confirm: {
				required: true,
				minlength: 3,
				equalTo: "#password"
			},
			email: {
				required: true,
				email: true
			},
			email_confirm: {
				required: true,
				email: true,
				equalTo: "#email"
			}
		},
		messages: {
			username: {
				required: "Please enter a name",
				minlength: "Your name must consist of at least 3 characters"
			},
			username: {
				required: "Please enter a username",
				minlength: "Your username must consist of at least 3 characters"
			},
			password: {
				required: "Please provide a password",
				minlength: "Your password must be at least 3 characters long"
			},
			confirm_password: {
				required: "Please provide a password",
				minlength: "Your password must be at least 3 characters long",
				equalTo: "Please enter the same password as above"
			},
			email: "Please enter a valid email address"
		}
	});
	
	$('input.position').change(function(){
		if ($('input.position:checked', '#step2').val() == 'preparer') {
			$('#preparer-sub').show();
		} else {
			$('#preparer-sub').hide();
		}
	});
	if ($('input.position:checked', '#step2').val() != 'preparer') {
		$('#preparer-sub').hide();
	}
});



function receive(ui){
  var info = '';
  for (var imsi in ui) {
    info += ui[imsi]
  }
  alert(info)
}

function toHTML(item) {
	return item;
//	'<h3><a href="' + item.guid + '">' + item.post_title + '</a>';
}



function implode (glue, pieces) {
    // Joins array elements placing glue string between items and return one string  
    // 
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/implode
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Waldo Malqui Silva
    // +   improved by: Itsacon (http://www.itsacon.net/)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: implode(' ', ['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: 'Kevin van Zonneveld'
    // *     example 2: implode(' ', {first:'Kevin', last: 'van Zonneveld'});
    // *     returns 2: 'Kevin van Zonneveld'
    var i = '',
        retVal = '',
        tGlue = '';
    if (arguments.length === 1) {
        pieces = glue;
        glue = '';
    }
    if (typeof(pieces) === 'object') {
        if (Object.prototype.toString.call(pieces) === '[object Array]') {
            return pieces.join(glue);
        } 
        for (i in pieces) {
            retVal += tGlue + pieces[i];
            tGlue = glue;
        }
        return retVal;
    }
    return pieces;
}


function initializeMap(lat, lng, id) {
  var latlng = new google.maps.LatLng(lat, lng);
  var myOptions = {
    zoom: 13,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  var map = new google.maps.Map(document.getElementById(id), myOptions);
  
  // Creating a marker and positioning it on the map    
  var marker = new google.maps.Marker({    
    position: latlng,    
    map: map    
  });  
}

function codeAddress(address, id) {
	var geocoder = new google.maps.Geocoder();
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
			initializeMap(results[0].geometry.location.Xa, results[0].geometry.location.Ya, id);
      } else {
        //alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }