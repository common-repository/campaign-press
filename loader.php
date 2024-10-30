<?php

	/**
	* This file is the beginning - it sets some constants then loads everything else.
	*/
	
		function campaignpress() {
	
		global $wpdb, $campaignpress;

		if ( is_object( $campaignpress ) )
			return;

		$campaignpress = (object) array	(
														'addon_table_name' => 					$wpdb->prefix . 'campaignpress1_addon',
														'client_table_name' => 					$wpdb->prefix . 'campaignpress1_client',
														'group_table_name' => 					$wpdb->prefix . 'campaignpress1_group',
														'activity_table_name' => 				$wpdb->prefix . 'campaignpress1_activity',
														'list_table_name' => 					$wpdb->prefix . 'campaignpress1_list',
														'field_table_name' => 					$wpdb->prefix . 'campaignpress1_field',
														'list_field_table_name' => 			$wpdb->prefix . 'campaignpress1_list_field',
														'client_field_value_table_name' => 	$wpdb->prefix . 'campaignpress1_client_field_value'
													);
	}

	// create our object
	campaignpress();
	
	// grab our settings
	$campaignpress_settings = get_option( 'campaignpress_settings' );
	
	// load our classes
	require_once CAMPAIGNPRESS_PLUGIN_DIR . '/lib/campaignpress-classes.php';

	// load our admin functions
	if ( is_admin() )
		require_once CAMPAIGNPRESS_PLUGIN_DIR . '/admin/admin.php';
	else
		require_once CAMPAIGNPRESS_PLUGIN_DIR . '/frontend/frontend.php';
		
	// load our common functions
	require_once CAMPAIGNPRESS_PLUGIN_DIR . '/lib/campaignpress-common.php';

?>