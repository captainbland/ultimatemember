<?php
/*
Plugin Name: Ultimate Member
Plugin URI: http://ultimatemember.com/
Description: The easiest way to create powerful online communities and beautiful user profiles with WordPress
Version: 1.3.83
Author: Ultimate Member
Author URI: http://ultimatemember.com/
Text Domain: ultimatemember
Domain Path: /languages
*/
	defined('ABSPATH') || exit;
	require_once(ABSPATH.'wp-admin/includes/plugin.php');

	$plugin_data = get_plugin_data( __FILE__ );

	define('um_url',plugin_dir_url(__FILE__ ));
	define('um_path',plugin_dir_path(__FILE__ ));
	define('um_plugin', plugin_basename( __FILE__ ) );
	define('UM_TEXTDOMAIN', 'ultimatemember');

	define('ultimatemember_version', $plugin_data['Version'] );

	$plugin = um_plugin;

	/***
	***	@Init
	***/
	require_once um_path . 'um-init.php';

	/***
	***	@Display a welcome page
	***/
	function ultimatemember_activation_hook( $plugin ) {

		if( $plugin == um_plugin && get_option('um_version') != ultimatemember_version ) {

			update_option('um_version', ultimatemember_version );

			exit( wp_redirect( admin_url('admin.php?page=ultimatemember-about')  ) );

		}

	}
	add_action( 'activated_plugin', 'ultimatemember_activation_hook' );

	add_action('um_submit_form_errors_hook_', 'um_custom_validate_membership', 999, 1);


		function um_custom_validate_membership($args) {
		  global $ultimatemember;
		  global $wpdb;
		  if(isset($args['membership_number'])) {
		    //mysql> CREATE TABLE available_membership_numbers (membership_id VARCHAR(256), available BOOLEAN);

		    $check_number = $args['membership_number'];
		    $membership_no_hash = base64_encode(hash("sha256", $check_number));
		    $query = $wpdb->prepare("SELECT available FROM available_membership_numbers WHERE membership_id=%s", $membership_no_hash);
		    $available = $wpdb->get_var($query);
		    echo $available;
		    if(is_null($available) || $available == 0) {
		      $ultimatemember->form->add_error('membership_number', "Your membership number appears to be invalid! $membership_no_hash $available");
		    }
		  }
		}

	/***
	***	@Add any custom links to plugin page
	***/
	function ultimatemember_plugin_links( $links ) {

		$more_links[] = '<a href="http://docs.ultimatemember.com/">' . __('Docs','ultimatemember') . '</a>';

		$more_links[] = '<a href="'.admin_url().'admin.php?page=um_options">' . __('Settings','ultimatemember') . '</a>';

		$links = $more_links + $links;

		$links[] = '<a href="'.admin_url().'?um_adm_action=uninstall_ultimatemember" class="um-delete" title="'.__('Remove this plugin','ultimatemember').'">' . __( 'Uninstall','ultimatemember' ) . '</a>';

		return $links;

	}
	$prefix = is_network_admin() ? 'network_admin_' : '';
	add_filter( "{$prefix}plugin_action_links_$plugin", 'ultimatemember_plugin_links' );
