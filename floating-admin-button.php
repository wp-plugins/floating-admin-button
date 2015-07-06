<?php
/*
Plugin Name: Floating Admin Button
Plugin URI: http://cagewebdev.com/floating-admin-button
Description: On frontend pages this plugin shows a floating admin button instead of the admin bar
Version: 1.0.1
Date: 07/07/2015
Author: Rolf van Gelder
Author URI: http://cagewebdev.com
License: GPLv2 or later
*/
?>
<?php
if (!session_id()) session_start();
?>
<?php
/***********************************************************************************
 * 	MAIN CLASS
 ***********************************************************************************/	 
class Fab
{
	var $fab_version = '1.0.1';
	var $fab_release_date = '07/07/2015';

	
	/*******************************************************************************
	 * 	CONSTRUCTOR
	 *******************************************************************************/
	function __construct()
	{
		// INITIALIZE PLUGIN
		add_action('init', array(&$this, 'fab_init'));
				
		// GET OPTIONS FROM DB (JSON FORMAT)
		$this->fab_options = get_option('fab_options');

		// FIRST RUN: SET DEFAULT SETTINGS
		if(!$this->fab_options) $this->fab_init_settings();

		// BASE NAME OF THE PLUGIN
		$this->plugin_basename = plugin_basename(__FILE__);
		$this->plugin_basename = substr($this->plugin_basename, 0, strpos( $this->plugin_basename, '/'));
		
		// IMAGE LOCATION
		$this->imgurl = plugins_url().'/'.$this->plugin_basename.'/images/';
		$this->imgdir = plugin_dir_path( __FILE__ ).'images/';
		
		// LOCALIZATION
		add_action('init', array(&$this, 'fab_i18n'));		
		
		// ADD STYLE SHEET(S)
		add_action('init', array(&$this, 'fab_styles'));

	} // __construct()


	/*******************************************************************************
	 * 	INITIALIZE PLUGIN
	 *******************************************************************************/
	function fab_init()
	{
		if($this->fab_is_frontend_page() && is_user_logged_in())
		{	// FRONTEND PAGE AND USER IS LOGGED IN
			add_action('wp_footer', array(&$this, 'fab_javascript_vars'));
			add_action('wp_footer', array(&$this, 'fab_fe_scripts'));			
		}
		else if (is_user_logged_in())
		{	// BACKEND PAGE
			add_action('admin_menu', array(&$this, 'fab_admin_menu'));
			add_filter('plugin_action_links_'.plugin_basename(__FILE__), array(&$this, 'fab_settings_link'));	
		} // if($this->fab_is_frontend_page() && is_user_logged_in())
	} // fab_init()
	
	
	/*******************************************************************************
	 * 	INITIALIZE SETTINGS (FIRST RUN)
	 *******************************************************************************/
	function fab_settings()
	{	// INITIALIZE SETTINGS (FIRST RUN)
		include_once(trailingslashit(dirname( __FILE__ )).'/admin/settings.php');
	} // fab_settings()	
	

	/*******************************************************************************
	 * 	INITIALIZE SETTINGS
	 *******************************************************************************/
	function fab_init_settings()
	{	if (false === $this->fab_options)
		{	// NO SETTINGS YET: SET DEFAULTS
			$this->fab_options['showbutton'] = 'Y';
			$this->fab_options['shift_ctrl'] = 'ctrl';
			$this->fab_options['keycode']    = '119'; // F8
			$this->fab_options['position']   = 'lowerleft';
			$this->fab_options['spacing']    = '20px';
			$this->fab_options['scrolltext'] = 'Open Admin Screen';
		} // if ( false === $this->fab_options )

		// SAVE OPTIONS ARRAY
		update_option('fab_options', $this->fab_options);
	} // fab_init_settings()
	

	/*******************************************************************************
	 * 	DEFINE TEXT DOMAIN (FOR LOCALIZATION)
	 *******************************************************************************/
	function fab_i18n()
	{	load_plugin_textdomain('floating-admin-button', false, dirname(plugin_basename( __FILE__ )).'/language/');
	} // fab_action_init()	
	
	
	/*******************************************************************************
	 * 	LOAD STYLESHEET(S)
	 *******************************************************************************/
	function fab_styles()
	{	wp_register_style('fab-style', plugins_url('css/floating-admin-button.css', __FILE__));
		wp_enqueue_style('fab-style');
	} // fab_styles()


	/*******************************************************************************
	 * 	IS THIS A FRONTEND PAGE?
	 *******************************************************************************/
	function fab_is_frontend_page()
	{	if (isset($GLOBALS['pagenow']))
			return !is_admin() && !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
		else
			return !is_admin();
	} // fab_is_frontend_page()
	

	/*******************************************************************************
	 * 	ADD PAGE TO THE SETTINGS MENU
	 *******************************************************************************/
	function fab_admin_menu()
	{	if (function_exists('add_options_page'))
		{	global $fab_options;
			$fab_options = add_options_page(__('Floating Admin Button', 'floating-admin-button'), __( 'Floating Admin Button', 'floating-admin-button' ), 'manage_options', 'fab_settings', array( &$this, 'fab_settings'));
		}
	} // fab_admin_menu()
	
	
	/*******************************************************************************
	 * 	ADD 'SETTINGS' LINK TO THE MAIN PLUGIN PAGE
	 *******************************************************************************/
	function fab_settings_link($links)
	{	array_unshift($links, '<a href="options-general.php?page=fab_settings">Settings</a>');
		return $links;
	} // fab_settings_link()
	
	
	/*******************************************************************************
	 * 	LOAD FRONTEND JAVASCRIPT
	 *******************************************************************************/
	function fab_fe_scripts()
	{	wp_register_script('fab-active', plugins_url('js/floating-admin-button.js', __FILE__), array('jquery', 'jquery-ui-draggable'), '1.0', true);
		wp_enqueue_script('fab-active');
	} // fab_fe_scripts()

	
	/*******************************************************************************
	 * 	PASS OPTIONS TO JAVASCRIPT
	 *******************************************************************************/
	function fab_javascript_vars()
	{
		// URL FOR THE AJAX SERVER (NEEDED FOR FRONTEND OPERATION, ajaxurl DOESN'T WORK ON THE FRONTEND)
		$fab_ajax_url = admin_url('admin-ajax.php');
		$fab_admin_url = admin_url();	
		
		// BUTTON HAS BEEN DRAGGED DURING THIS SESSION?
		$fab_top = -1;
		if(isset($_SESSION['fab_top']))  $fab_top  = $_SESSION['fab_top'];
		$fab_left = -1;
		if(isset($_SESSION['fab_left'])) $fab_left = $_SESSION['fab_left'];
		
		echo '
<!-- START Floating Admin Button v'.$this->fab_version.' ['.$this->fab_release_date.'] | http://cagewebdev.com/floating-admin-button | CAGE Web Design | Rolf van Gelder -->
<script type="text/javascript">
var fab_showbutton = "'.$this->fab_options['showbutton'].'";
var fab_shift_ctrl = "'.$this->fab_options['shift_ctrl'].'";
var fab_keycode    = "'.$this->fab_options['keycode'].'";
var fab_position   = "'.$this->fab_options['position'].'";
var fab_top        = "'.$fab_top.'";
var fab_left       = "'.$fab_left.'";
var fab_spacing    = "'.$this->fab_options['spacing'].'";
var fab_scrolltext = "'.$this->fab_options['scrolltext'].'";
var fab_imgurl     = "'.$this->imgurl.'wp_logo.png";
var fab_ajaxurl    = "'.$fab_ajax_url.'";
var fab_adminurl   = "'.$fab_admin_url.'";
</script>
<!-- END Floating Admin Button -->
';
	} // fab_javascript_vars()
} // Fab

// CREATE INSTANCE
global $fab_class;
$fab_class = new Fab;


/*******************************************************************************
 * 	AJAX SERVER FOR UPDATING THE CURRENT BUTTON POSITION
 *******************************************************************************/
function fab_action_callback()
{
	// SAVE THE CURRENT BUTTON POSITION TO SESSION VARIABLES
	$_SESSION['fab_top']  = sanitize_text_field($_POST['fab_top']);
	$_SESSION['fab_left'] = sanitize_text_field($_POST['fab_left']);
	
	echo 'OK';
	
	// NEEDED FOR AN AJAX SERVER
	die();
} // fab_action_callback()

// ADD AJAX SERVER
add_action('wp_ajax_my_action', 'fab_action_callback');	
?>