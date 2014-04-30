<?php

/*
Plugin Name: Pi Add Onswipe Script
Version: 0.1 beta
Author: Postindustria
*/

class PiAddOnswipeScript
{
	public static function init()
	{
		add_action('admin_menu', array(__CLASS__, 'addAdminMenu'));
		add_action('wp_head', array(__CLASS__, 'addScript'));
	}

	public static function addAdminMenu()
	{
		add_options_page('Pi Add Onswipe Script', 'Pi Add Onswipe Script', 'manage_options', 'pi-add-onswipe-script', array(__CLASS__, 'adminMenu'));
	}

	public static function addScript()
	{
		$params = get_option('pi-add-script');

		if ($params['userName']) {
			echo "<script>window.ONSWIPE_USR = '" . $params['userName'] . "'</script>";
			echo '<script src="http://assets.onswipe.com/synapse/on.js?usr=' . $params['userName'] . '" id="onswipe_synapse"></script>';
		}
	}

	public static function adminMenu()
	{
		wp_enqueue_style("pi-add-script", path_join(WP_PLUGIN_URL, basename(dirname(__FILE__))) . "/pi-add-onswipe-script.css");

		if ($_POST['submit-params']) {
			self::saveData();
		}

		$params = get_option('pi-add-script');

		?>
		<div class="pi-add-script-wrapper">
			<h1 class="pi-add-script-header">Add scripts on site</h1>

			<form class="pi-add-script-body" method="post" method="/wp-admin/options-general.php?page=pi-add-script">
				<label>User name:</label>
				<input type="text" name="userName" placeholder="Enter user name"
					   value='<?php echo $params['userName']; ?>'
					   class="<?php echo (!isset($params['errors']['userName']) ? '' : 'error'); ?>"/>

				<div class="error-message">
					<?php echo (!isset($params['errors']['userName']) ? '' : $params['errors']['userName']); ?>
				</div>
				<div class="successful">
					<?php echo (empty($perams['errors']) && isset($_POST['submit-params'])) ? 'Successful update' : ''; ?>
				</div>
				<input type="submit" value="Save" name='submit-params'>
			</form>
		</div>
	<?php
	}

	public static function saveData()
	{
		if (!empty($_POST['userName'])) {
			update_option('pi-add-script', array(
				'userName' => $_POST['userName'],
				'status' => $_POST['status'],
				'onWipeDomain' => $_POST['onWipeDomain'],
				'errors' => null,
			));
		} else {
			update_option('pi-add-script', array(
				'errors' => array(
					'userName' => 'Please fill user name.'
				),
			));
		}
	}
}

if (defined('ABSPATH') && function_exists('add_action')) {
	PiAddOnswipeScript::init();
}
