function fab_reset_position(fab_ajaxurl)
{
	/* PARAMETERS FOR THE AJAX CALL */
	var data = {
		'action': 'fab_action',	// v1.0.4
		'fab_action': 'reset_position'
	};
	
	/* CALL THE AJAX SERVER:
	   THIS WILL CREATE / UPDATE THE SESSION VARIABLES THAT KEEP THE TEMPORARY POSITION */
	jQuery.post(fab_ajaxurl, data, function(response){
		jQuery("#fab-note").html('');	
	});	
}
