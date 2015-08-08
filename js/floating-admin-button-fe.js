/* ---------------------------------------------------
 *
 *	FRONTEND JQUERY
 *
 * -------------------------------------------------*/
var fab_current_bottom = -1;
var fab_current_left   = -1;
var fab_max_bottom     = -1;
var fab_max_left       = -1;
var fab_button_width   = 38;
var fab_button_height  = 38;

jQuery(document).ready(function(){
	
	/* INITIALLY SHOW THE BUTTON OR THE BAR */
	if(fab_showbutton == 'Y') // v1.0.5
	{	/* HIDE THE WORDPRESS ADMIN BAR */
		jQuery("#wpadminbar").hide();
		/* v1.0.1 - REMOVE THE SPACE FOR THE ADMIN BAR */
		jQuery("html").attr('style', 'margin-top: 0px !important');
		/* SHOW BUTTON */
		jQuery("#adminButton").show();
	}
	else
	{	/* SHOW THE WORDPRESS ADMIN BAR */
		jQuery("#wpadminbar").show();
		/* v1.0.1 - MAKE ROOM FOR THE ADMIN BAR */
		jQuery("html").attr('style', 'margin-top: 32px !important');
		/* HIDE BUTTON */
		jQuery("#adminButton").hide();
	}

	/* ADD A NEW 'adminButton' DIV TO THE END OF THE BODY */
	var $div = jQuery('<div />').appendTo('body');
	$div.attr('id', 'adminButton');
	
	/* SET THE TITLE (=TOOLTIP) */
	jQuery("#adminButton").attr('title', fab_scrolltext);
	
	/* SET THE A BACKGROUND IMAGE FOR THE BUTTON */
	jQuery("#adminButton").css({"background-image":"url("+fab_imgurl+")"});
	if(fab_showbutton == 'N') jQuery("#adminButton").hide(); // v1.0.5

	/* ADD MOUSEOVER EFFECT */	
	jQuery("#adminButton").mouseover(function() {
		fab_setOpacity("99");
	});
	jQuery("#adminButton").mouseout(function() {
		fab_setOpacity("50");
	});

	/* MAKE THE BUTTON DRAGGABLE */
	jQuery("#adminButton").draggable({
		/* BUTTON DROPPED */
		stop: function(event, ui) {
			/* CALCULATE THE NEW BOTTOM */
			fab_calc_bottom();
			/* CHECK THE CONTRAINTS */	
			fab_constraints();
			/* STORE POSITION IN SESSION VARIABLES */
			fab_update_position();
		}
	});
	
	/* SET THE LINK FOR THE BUTTON */
	jQuery("#adminButton").click(function(event) {
		self.location = fab_adminurl;
	});
	
	if(fab_bottom != '-1')
	{	/* BUTTON HAS BEEN DRAGGED DURING THIS SESSION: SET THE CURRENT POSITION */
		jQuery("#adminButton").css('bottom', fab_bottom+'px');
		jQuery("#adminButton").css('left', fab_left+'px');
	}
	else
	{	/* USER SELECTED DEFAULT POSITION */
		var wh  = jQuery(window).height();
		var ww  = jQuery(window).width();
		
		if(fab_position == 'lowerleft')
		{	jQuery("#adminButton").css('left', fab_spacing);
			jQuery("#adminButton").css('bottom', fab_spacing);		
		}
		else if(fab_position == 'lowerright')
		{	jQuery("#adminButton").css('left', (ww - fab_button_width - fab_spacing_int)+'px');
			jQuery("#adminButton").css('bottom', fab_spacing);			
		}
		else if(fab_position == 'upperleft')
		{	jQuery("#adminButton").css('left', fab_spacing);
			jQuery("#adminButton").css('bottom', (wh - fab_button_height - fab_spacing_int)+'px');
		}
		else if(fab_position == 'upperright')
		{	jQuery("#adminButton").css('left', (ww - fab_button_width - fab_spacing_int)+'px');
			jQuery("#adminButton").css('bottom', (wh - fab_button_height - fab_spacing_int)+'px');		
		}	
	}
	
	/* TOGGLE BUTTON AND BAR WHEN THE CUSTOM HOTKEY IS PRESSED (DEFAULT: CTRL-F8) */
	jQuery(document).keydown(function(event) {
		if(((fab_shift_ctrl == 'shift' && event.shiftKey) || (fab_shift_ctrl == 'ctrl' && event.ctrlKey)) && event.which == fab_keycode)
		{	/* HOTKEY PRESSED: TOGGLE BETWEEN BUTTON AND BAR */
			jQuery("#adminButton").toggle();			
			jQuery("#wpadminbar").toggle();
			if(jQuery("#adminButton").is(":visible"))
			{	fab_showbutton = "Y";
			}
			else
			{	fab_showbutton = "N";
			}
			// v1.0.6
			fab_update_showbutton();
			// v1.0.1
			if (jQuery("#adminButton").is(":visible"))
				/* IN BUTTON MODE: RECLAIM ADMIN BAR SPACE */
				jQuery("html").attr('style', 'margin-top: 0px !important');
			else
				/* IN ADMIN BAR MODE: MAKE ROOM FOR THE BAR */
				jQuery("html").attr('style', 'margin-top: 32px !important');
		}
	});
	
	/* WINDOW HAS BEEN RESIZED */
	jQuery(window).resize(function() {
		/* CHECK CONTRAINTS */
		fab_constraints();
	});	
});		


/* ---------------------------------------------------
 *
 *	CHECK CONSTRAINTS
 *
 * -------------------------------------------------*/
function fab_constraints()
{	var wh  = jQuery(window).height();
	var ww  = jQuery(window).width();
	
	fab_max_bottom = wh - fab_button_height - fab_spacing_int;
	fab_max_left   = ww - fab_button_width  - fab_spacing_int;	
	
	if(fab_current_bottom > fab_max_bottom)
	{	fab_current_bottom = fab_max_bottom;
		jQuery("#adminButton").css('bottom', fab_current_bottom);
	}
	if(fab_current_bottom < fab_spacing_int)
	{	fab_current_bottom = fab_spacing_int;
		jQuery("#adminButton").css('bottom', fab_current_bottom);
	}
	if(fab_current_left > fab_max_left)
	{	fab_current_left = fab_max_left;
		jQuery("#adminButton").css('left', fab_current_left);
	}
	if(fab_current_left < fab_spacing_int)
	{	fab_current_left = fab_spacing_int;
		jQuery("#adminButton").css('left', fab_current_left);
	}	
} // fab_constraints()


/* ---------------------------------------------------
 *
 *	UPDATE THE CURRENT BUTTON POSITION
 *
 * -------------------------------------------------*/
function fab_calc_bottom()
{
	var pos = jQuery("#adminButton").position();
	var wh  = jQuery(window).height();
	var ww  = jQuery(window).width();

	fab_current_bottom = wh - pos.top - fab_button_height;
	fab_current_left   = pos.left;
	
	jQuery("#adminButton").css('bottom', fab_current_bottom);
	/* IMPORTANT: SET THE TOP TO 'AUTO' */
	jQuery("#adminButton").css('top', 'auto');
} // function fab_calc_bottom()


/* ---------------------------------------------------
 *
 *	UPDATE THE CURRENT BUTTON POSITION
 *
 * -------------------------------------------------*/
function fab_update_position()
{
	/* PARAMETERS FOR THE AJAX CALL */
	var data = {
		'action': 'fab_action',	// v1.0.4
		'fab_action': 'set_position',
		'fab_left': fab_current_left,
		'fab_bottom': fab_current_bottom,
		'fab_showbutton': fab_showbutton
	};

	/* ---------------------------------------------------
	 *	CALL THE AJAX SERVER:
	 *	THIS WILL CREATE / UPDATE THE SESSION VARIABLES
	 *	THAT KEEP THE TEMPORARY POSITION
	 * -------------------------------------------------*/
	jQuery.post(fab_ajaxurl, data, function(response){
	});
} // fab_update_position()


/* ---------------------------------------------------
 *
 *	UPDATE THE CURRENT SHOW/HIDE BUTTON VALUE
 *
 *	Since v1.0.6
 *
 * -------------------------------------------------*/
function fab_update_showbutton()
{
	/* PARAMETERS FOR THE AJAX CALL */
	var data = {
		'action': 'fab_action',
		'fab_action': 'set_showbutton',
		'fab_showbutton': fab_showbutton
	};

	/* ---------------------------------------------------
	 *	CALL THE AJAX SERVER:
	 *	THIS WILL CREATE / UPDATE THE SHOW / HIDE BUTTON
	 *	STATUS
	 * -------------------------------------------------*/
	jQuery.post(fab_ajaxurl, data, function(response){
	});
} // fab_update_showbutton()


/* ---------------------------------------------------
 *
 *	SET THE OPACITY OF THE BUTTON
 *
 * -------------------------------------------------*/
function fab_setOpacity(opac)
{
	jQuery("#adminButton").css({"-khtml-opacity":"."+opac});
	jQuery("#adminButton").css({"-moz-opacity":"."+opac});
	jQuery("#adminButton").css({"-ms-filter":'"alpha(opacity='+opac+')"'});
	jQuery("#adminButton").css({"filter":"alpha(opacity="+opac+")"});
	jQuery("#adminButton").css({"filter":"progid:DXImageTransform.Microsoft.Alpha(opacity=0."+opac+")"});
	jQuery("#adminButton").css({"opacity":"."+opac});		
} // fab_setOpacity()

