<?php
/***********************************************************************************
 *
 * 	SETTINGS PAGE
 *
 ***********************************************************************************/
if (!function_exists('add_action')) exit;

if (isset($_POST['action']) && 'save_settings' === $_POST['action'])
{	// SAVE SETTINGS
	check_admin_referer('fab_settings_'.$this->fab_version);

	$safe_keycode = intval($_POST['fab_keycode']);
	if (!$safe_keycode)
	  $safe_keycode = '';
	if (strlen($safe_keycode) > 3)
	  $safe_keycode = substr($safe_keycode, 0, 3);

	// FIXED IN v1.0.5
	if (isset($_POST['fab_showbutton']))
		$this->fab_options['showbutton'] = 'Y';
	else
		$this->fab_options['showbutton'] = 'N';

	$this->fab_options['shift_ctrl'] = sanitize_text_field($_POST['fab_shift_ctrl']);
	$this->fab_options['keycode']    = $safe_keycode;
	$this->fab_options['position']   = sanitize_text_field($_POST['fab_position']);
	$this->fab_options['spacing']    = sanitize_text_field($_POST['fab_spacing']);
	$this->fab_options['scrolltext'] = sanitize_text_field($_POST['fab_scrolltext']);
		
	update_option('fab_options', $this->fab_options);
	
	echo '<div class="updated"><p><strong>'.__('Floating Admin Button - Settings UPDATED!', 'floating-admin-button').'</strong></p></div>';
} // if (isset($_POST['action']) && 'save_settings' === $_POST['action'])
?>
<?PHP
/***********************************************************************************
 * 	TITLE BAR
 ***********************************************************************************/
?>

<div class="fab-title-bar">
  <h2>
    <?php _e( 'Floating Admin Button - Settings', 'floating-admin-button' ); ?>
  </h2>
</div>
<?php
/***********************************************************************************
 * 	INTRO
 ***********************************************************************************/
?>
<div class="fab-intro">
  <?php _e( 'Plugin version', 'floating-admin-button' ); ?>
  : v<?php echo $this->fab_version?> [<?php echo $this->fab_release_date?>] - <a href="http://cagewebdev.com/index.php/floating-admin-button/" target="_blank">
  <?php _e( 'Plugin page', 'floating-admin-button' ); ?>
  </a> - <a href="https://wordpress.org/plugins/floating-admin-button/" target="_blank">
  <?php _e( 'Download page', 'floating-admin-button' ); ?>
  </a> - <a href="http://cagewebdev.com/index.php/donations-fab/" target="_blank">
  <?php _e( 'Donation page', 'floating-admin-button' ); ?>
  </a> </div>
<?php
/***********************************************************************************
 * 	FORM
 ***********************************************************************************/
$fab_dragged_text = '';
if(isset($_SESSION['fab_bottom']))
{	// BUTTON HAS BEEN DRAGGED DURING THIS SESSION
	$fab_dragged_text = __('Button has been dragged, new Position is', 'floating-admin-button');
	$fab_dragged_text .= ' ';
	$fab_dragged_text .= __('LEFT:', 'floating-admin-button');
	$fab_dragged_text .= ' ';
	$fab_dragged_text .= $_SESSION['fab_left'].'px, ';
	$fab_dragged_text .= __('BOTTOM:', 'floating-admin-button');
	$fab_dragged_text .= ' ';
	$fab_dragged_text .= $_SESSION['fab_bottom'].'px';	
}
$fab_ajax_url = admin_url('admin-ajax.php'); 
?>
<div id="fab-settings-form">
  <form name="fab_settings" id="fab_settings" method="post" action="">
    <?php wp_nonce_field('fab_settings_'.$this->fab_version); ?>
    <input type="hidden" name="action" value="save_settings" />
    <table border="0" cellspacing="0" cellpadding="5">
      <?php
		$fab_showbutton_checked = '';
		if(isset($this->fab_options['showbutton']))
			if ('Y' === $this->fab_options['showbutton'])
				$fab_showbutton_checked = ' checked="checked"';
		?>
      <tr>
        <td valign="top"><?php _e('Initially show the Floating Admin Button<br />instead of the WP Admin Bar', 'floating-admin-button'); ?></td>
        <td valign="top"><input type="checkbox" name="fab_showbutton" id="fab_showbutton" value="Y" <?php echo $fab_showbutton_checked;?> /></td>
      </tr>
      <tr>
        <td valign="top" colspan="2" class="fab_note"><?php _e('Note: select a Key Combination that is not already used by your Browser!', 'floating-admin-button');?></td>
      </tr>
      <tr>
        <td valign="top"><?php _e('Key Combination for toggling<br />between Button and Bar', 'floating-admin-button'); ?></td>
        <td valign="top"><select name="fab_shift_ctrl" id="fab_shift_ctrl">
            <option value="shift">
            <?php _e('Shift', 'floating-admin-button');?>
            </option>
            <option value="ctrl">
            <?php _e('Ctrl', 'floating-admin-button');?>
            </option>
          </select>
          &nbsp;
          <select name="fab_keycode" id="fab_keycode">
            <option value="112">
            <?php _e('F1', 'floating-admin-button');?>
            </option>
            <option value="113">
            <?php _e('F2', 'floating-admin-button');?>
            </option>
            <option value="114">
            <?php _e('F3', 'floating-admin-button');?>
            </option>
            <option value="115">
            <?php _e('F4', 'floating-admin-button');?>
            </option>
            <option value="116">
            <?php _e('F5', 'floating-admin-button');?>
            </option>
            <option value="117">
            <?php _e('F6', 'floating-admin-button');?>
            </option>
            <option value="118">
            <?php _e('F7', 'floating-admin-button');?>
            </option>
            <option value="119">
            <?php _e('F8', 'floating-admin-button');?>
            </option>
            <option value="120">
            <?php _e('F9', 'floating-admin-button');?>
            </option>
            <option value="121">
            <?php _e('F10', 'floating-admin-button');?>
            </option>
            <option value="122">
            <?php _e('F11', 'floating-admin-button');?>
            </option>
            <option value="123">
            <?php _e('F12', 'floating-admin-button');?>
            </option>
          </select></td>
      </tr>
      <script type="text/javascript">
      jQuery('#fab_shift_ctrl').val("<?php echo $this->fab_options['shift_ctrl'];?>");
      jQuery('#fab_keycode').val("<?php echo $this->fab_options['keycode'];?>");	  
      </script>
      <tr>
        <td valign="top" colspan="2" class="fab_note"><?php _e('Note: dragging the Button will (temporary) overwrite the default Position!', 'floating-admin-button');?></td>
      </tr>
      <tr>
        <td valign="top"><?php _e('Default Position of the Button', 'floating-admin-button'); ?></td>
        <td valign="top"><select name="fab_position" id="fab_position">
            <option value="lowerright">
            <?php _e('Lower Right', 'floating-admin-button');?>
            </option>
            <option value="lowerleft">
            <?php _e('Lower Left', 'floating-admin-button');?>
            </option>
            <option value="upperright">
            <?php _e('Upper Right', 'floating-admin-button');?>
            </option>
            <option value="upperleft">
            <?php _e('Upper Left', 'floating-admin-button');?>
            </option>
          </select>
          <?php
		  if($fab_dragged_text)
		  {
		  ?>
          <span class="fab-note" id="fab-note"><br />
          <?php echo $fab_dragged_text; ?><br />
          <input class="button-primary button-large fab-reset-button" type='button' name='fab-reset-button' value='<?php echo __('Back to Default Position', 'floating-admin-button');?>' onclick="fab_reset_position('<?php echo $fab_ajax_url?>')"; />
          </span>
          <?php
		  }
		  ?></td>
      </tr>
      <script type="text/javascript">
      jQuery('#fab_position').val("<?php echo $this->fab_options['position'];?>");
      </script>
      <tr>
        <td valign="top"><?php _e('Spacing from the Edges', 'floating-admin-button'); ?></td>
        <td valign="top"><select name="fab_spacing" id="fab_spacing">
            <option value="15px">
            <?php _e('15px', 'floating-admin-button');?>
            </option>
            <option value="20px">
            <?php _e('20px', 'floating-admin-button');?>
            </option>
            <option value="50px">
            <?php _e('50px', 'floating-admin-button');?>
            </option>
            <option value="75px">
            <?php _e('75px', 'floating-admin-button');?>
            </option>
            <option value="100px">
            <?php _e('100px', 'floating-admin-button');?>
            </option>
          </select></td>
      </tr>
      <script type="text/javascript">
      jQuery('#fab_spacing').val("<?php echo $this->fab_options['spacing'];?>");
      </script>
      <tr>
        <td valign="top"><?php _e('Title for the Button', 'floating-admin-button'); ?></td>
        <td valign="top"><input name="fab_scrolltext" id="fab_scrolltext" type="text" value="<?php echo $this->fab_options['scrolltext'];?>" /></td>
      </tr>
      <tr>
        <td colspan="2"><input class="button-primary button-large fab-save-button" type='submit' name='info_update' value='<?php echo __('Save Settings', 'floating-admin-button');?>' /></td>
      </tr>
    </table>
  </form>
</div>
<?php
include(ABSPATH.'wp-admin/admin-footer.php');
// JUST TO BE SURE
die;
?>
