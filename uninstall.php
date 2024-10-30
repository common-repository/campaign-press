<?php

	if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
		exit();

	function campaignpress_delete_plugin() {
		
			delete_option( 'campaignpress_settings' );
			
			global $wpdb;
						
			$table_name = $wpdb->prefix . 'campaignpress1_client_field_value';
			$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			
			$table_name = $wpdb->prefix . 'campaignpress1_list_field';
			$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );

			$table_name = $wpdb->prefix . 'campaignpress1_field';
			$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			
			$table_name = $wpdb->prefix . 'campaignpress1_list';
			$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			
			$table_name = $wpdb->prefix . 'campaignpress1_activity';
			$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			
			$table_name = $wpdb->prefix . 'campaignpress1_client';
			$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			
			$table_name = $wpdb->prefix . 'campaignpress1_group';
			$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			
			$table_name = $wpdb->prefix . 'campaignpress1_addon';
			$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			
	}

	campaignpress_delete_plugin();

?>