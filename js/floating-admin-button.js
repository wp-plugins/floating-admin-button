/* ---------------------------------------------------
 *
 *	INITIALIZE THE JQUERY STUFF
 *
 * -------------------------------------------------*/	
jQuery(document).ready(function(){

	/* INITIALLY SHOW THE BUTTON OR THE BAR */
	if(fab_showbutton)
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

	/* ADD A NEW 'adminButton' DIV TO THE AND OF THE BODY */
	var $div = jQuery('<div />').appendTo('body');
	$div.attr('id', 'adminButton');
	
	/* SET THE TITLE (=TOOLTIP) */
	jQuery("#adminButton").attr('title', fab_scrolltext);
	
	/* SET THE A BACKGROUND IMAGE FOR THE BUTTON */
	jQuery("#adminButton").css({"background-image":"url("+fab_imgurl+")"});

	/* ADD MOUSEOVER EFFECT */	
	jQuery("#adminButton").mouseover(function() {
		fab_setOpacity("99");
	});
	jQuery("#adminButton").mouseout(function() {
		fab_setOpacity("50");
	});

	/* MAKE THE BUTTON DRAGGABLE */
	jQuery("#adminButton").draggable({
		stop: function(event, ui) {
			var pos = jQuery("#adminButton").position();
			
			/* PARAMETERS FOR THE AJAX CALL */
			var data = {
				'action': 'my_action',
				'fab_top': pos.top,
				'fab_left': pos.left
			};
		
			/* CALL THE AJAX SERVER:
			   THIS WILL CREATE / UPDATE THE SESSION VARIABLES THAT KEEP THE TEMPORARY POSITION */
			jQuery.post(fab_ajaxurl, data, function(response){});
		}
	});
	
	/* SET THE LINK FOR THE BUTTON */
	jQuery("#adminButton").click(function(event) {
		self.location = fab_adminurl;
	});
	
	if(fab_top != '-1')
	{	/* BUTTON HAS BEEN DRAGGED DURING THIS SESSION: SET THE CURRENT POSITION */
		jQuery("#adminButton").css('top', fab_top+'px');
		jQuery("#adminButton").css('left', fab_left+'px');
	}
	else
	{	/* USER SELECTED DEFAULT POSITION */
		if(fab_position == 'lowerleft')
		{
			jQuery("#adminButton").css('left', fab_spacing);
			jQuery("#adminButton").css('bottom', fab_spacing);		
		}
		else if(fab_position == 'lowerright')
		{
			jQuery("#adminButton").css('right', fab_spacing);
			jQuery("#adminButton").css('bottom', fab_spacing);			
		}
		else if(fab_position == 'upperleft')
		{
			jQuery("#adminButton").css('left', fab_spacing);
			jQuery("#adminButton").css('top', fab_spacing);
		}
		else if(fab_position == 'upperright')
		{
			jQuery("#adminButton").css('right', fab_spacing);
			jQuery("#adminButton").css('top', fab_spacing);		
		}	
	}
	
	/* TOGGLE BUTTON AND BAR WHEN THE CUSTOM HOTKEY IS PRESSED */
	jQuery(document).keydown(function(event) {
		if(((fab_shift_ctrl == 'shift' && event.shiftKey) || (fab_shift_ctrl == 'ctrl' && event.ctrlKey)) && event.which == fab_keycode)
		{	/* HOTKEY PRESSED: TOGGLE BETWEEN BUTTON AND BAR */
			jQuery("#adminButton").toggle();			
			jQuery("#wpadminbar").toggle();
			// v1.0.1
			if (jQuery("#adminButton").is(":visible"))
				// IN BUTTON MODE: RECLAIM ADMIN BAR SPACE
				jQuery("html").attr('style', 'margin-top: 0px !important');
			else
				// IN ADMIN BAR MODE: MAKE ROOM FOR THE BAR
				jQuery("html").attr('style', 'margin-top: 32px !important');
		}
	});
});		

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
} // setOpacity()
