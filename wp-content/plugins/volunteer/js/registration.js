jQuery(document).ready(function($) {
    $('#volunteer_dialog').dialog({
        autoOpen: false,
        width: 700,
        position: {my: "top", at: "top", of: "#breadcrumbs"}
    });
    $('#facebook_dialog').dialog({autoOpen: false, width: 500, height: 400});

    $('#volunteer_register').click(function(event) {
        event.preventDefault();

        $('.back').click(function() {
            gotoStep(--Volunteer.step);
        });

        $('#volunteer_dialog').dialog('open');
        gotoStep(Volunteer.step);

        // populate form
        $('#step1 input').each(function() {
            var name = $(this).attr('name');
            if (name in Volunteer) {
                $('#step1 input[name=' + name + ']').val(Volunteer[name]);
            }
        });
        $('#step1').ajaxForm({
            data: Volunteer,
            dataType: 'json',
            beforeSubmit: function() {
            },
            success: function(response, statusText, xhr, $form) {
                if (response.success) {
                    gotoStep(2);
                } else {
                    $('#step1 .error').html(implode('<br/>', response.errors));
                }
            }
        });

        $('#step2').ajaxForm({
            data: Volunteer,
            dataType: 'json',
            beforeSubmit: function() {
            },
            success: function(response, statusText, xhr, $form) {
                if (response.success) {
                    $('#step3 form div').html(response.data.terms);
                    gotoStep(3);
                } else {
                    $('#step2 .error').html(implode('<br/>', response.errors));
                }

            }
        });
        
        $('#step3 form').ajaxForm({
            data: Volunteer,
            dataType: 'json',
            beforeSubmit: function() {
            },
            success: function(response, statusText, xhr, $form) {
        
                if (response.success) {
                    // Wipe out any existing site search results
                    $('#search-text').val('');
                    $('#step42 #results').html('');
                    gotoStep(4);
                } else {
                    $('#step3 .error').html(implode('<br/>', response.errors));
                }

            }
        });

        $('#step41').ajaxForm({
            data: TaxSearch,
            dataType: 'html',
            beforeSubmit: function(arr, $form, options) {
            },
            success: function(response, statusText, xhr, $form) {
                $('#step42 #results').html(toHTML(response));
                $('#volunteer_dialog').dialog("option", "title", "Step Three: Choose a Tax Site");
            }
        });
        
        // Tax site selection submitted
        $('#step42').ajaxForm({
            data: Volunteer,
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
            },
            success: function(response, statusText, xhr, $form) {
                if (response.success) {
                    $('#step5 .results').html(toHTML(response.html));
                    // Select the first one in the list by default
                    $('#step5 input:first').attr('checked', 'checked');
                    
                    // Adjust the dialog header
                    var position = response.data.position[0];
                    if (position == 'preparer')
                        position = 'tax ' + position;
                    $('#step5 h3 span').text(position);
                    
                    gotoStep(5);
                } else {
                    $('#step42 .error').html(implode('<br/>', response.errors));
                }
            }
        });
        
        // Training selection submitted
        $('#step5 form').ajaxForm({
            data: Volunteer,
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
            },
            success: function(response, statusText, xhr, $form) {
                if (response.success)
                {
                    $('#step6 .results').html(toHTML(response.html));
                    $('#step6 h3').text(response.header);
                    $('#step6 .footer').text(response.footer);
                    gotoStep(6);
                }
            }
        });
        
        $('#step6 form').ajaxForm({
            data: Volunteer,
            dataType: 'json',
            beforeSubmit: function(arr, $form, options) {
                //return confirm("Are you sure you want to volunteer at this Tax Site?  By clicking OK you are committing to volunteer at this location and the Coordinator for this site will be notified.");
            },
            success: function(response, statusText, xhr, $form) {
                if (response.success) {             
                    $('#login_username').val(response.data.username);
                    $('#login_password').val(response.data.password);
                    gotoStep(7);
                }
                else
                {
                    // Shouldn't ever happen!
                    alert('There was a problem setting up your account.  Please try again later.');
                    //document.location.href = '/';
                }
            }
        });
        
        $('#step7 input').click(function() {
            // If you cannot submit the login form (i.e., an update), navigate to the dashboard
            if ($('#login').length)
                $('#login').click();
            else
                document.location.href = '/dashboard';
                
        });
    });

    function gotoStep(step) {
        if (step <= 0)
            step = 1;
        Volunteer.step = step;
        $('.step .errors').empty();
        $('.step').hide();
        $('#step' + step).show();
        var titles = {
            1: "Step 1: Create an Account",
            2: "Step 2: Choose a Position",
            3: "Step 3: Confirm the Terms of Use",
            4: "Step 4: Choose a Tax Site",
            5: "Step 5: Choose a Training",
            6: "Step 6: Confirm Your Information",
            7: "Step 7: Registration Complete"
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

    $('input.position').change(function() {
        if ($('input.position:checked', '#step2').val() == 'preparer') {
            $('#preparer-sub').show();
        } else {
            $('#preparer-sub').hide();
        }
    });
    if ($('input.position:checked', '#step2').val() != 'preparer') {
        $('#preparer-sub').hide();
    }
    
    if (document.location.hash == '#complete')
        $('#volunteer_register').trigger('click');
});



function receive(ui) {
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



function implode(glue, pieces) {
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
    geocoder.geocode({'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            initializeMap(results[0].geometry.location.Xa, results[0].geometry.location.Ya, id);
        } else {
            //alert("Geocode was not successful for the following reason: " + status);
        }
    });
}