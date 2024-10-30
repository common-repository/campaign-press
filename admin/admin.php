<?php

	/**
	* These functions create the administrative menu structure for CampaignPress.
	* The presentation (HTML) code is stored in separate files in the admin folder (named page-<pagename>.php)
	*/
		
		
	/**
	* Settings & validation
	*/
		// hooks
		add_action('admin_init', 'campaignpress_admin_init');
		
		function campaignpress_admin_init() {
		
			// no longer sanitising callback
			register_setting( 'campaignpress_settings', 'campaignpress_settings', 'campaignpress_settings_validate' );
		}
		
		// validate through register_setting
		function campaignpress_settings_validate( $input ) {
		
			global $campaignpress_settings;
		
			// encrypt the email password
			$cp_key = $campaignpress_settings['cp_key'];
			if( strlen( $cp_key ) < 1 ) {
				$cp_key = $input['cp_key'];
			}
			if( strlen( $input['override_mail_password'] ) > 0 ) {
				$input['override_mail_password'] = campaignpress_encrypt( $input['override_mail_password'], $campaignpress_settings['cp_key'] );
			}
			
			$errors = campaignpress_check_settings( $input );
			
			if( count( $errors ) < 1 ) {
				add_settings_error('campaignpress_settings', 'updated', __('Settings saved.', 'campaignpress'), 'updated');
			}				

			return $input;
		}
		
	
	/** 
	* Defining extra hooks 
	*/
	
		function campaignpress_settings_main() {
			do_action( 'campaignpress_settings_main' );
		}
		
		function campaignpress_settings_signup() {
			do_action( 'campaignpress_settings_signup' );
		}
		
		function campaignpress_settings_notifications() {
			do_action( 'campaignpress_settings_notifications' );
		}
	
		function campaignpress_settings_signup_hidden_fields() {
			do_action( 'campaignpress_settings_signup_hidden_fields' );
		}
		
		function campaignpress_hook_after_client_details() {
			do_action( 'campaignpress_hook_after_client_details' );
		}
	
	/**
	* Menu structure, pages & help
	*/
		// hooks
		add_action( 'admin_menu', 'campaignpress_admin_menu' );
		add_action( 'contextual_help', 'campaignpress_help', 10, 3);
		
		function campaignpress_admin_menu() {
		
			global $plugin_page, $campaignpress_dashboard_hook, $campaignpress_clients_hook, $campaignpress_groups_hook, $campaignpress_settings_hook, $campaignpress_addons_hook;
	
			// Javascript goodies
			wp_enqueue_script( 'campaignpress-admin', CAMPAIGNPRESS_PLUGIN_URL . '/js/admin.js', array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-sortable', 'jquery-ui-dialog' ) );
			
			// build our array of script vars
			$js_vars['ajaxurl'] = admin_url( 'admin-ajax.php' );
			$js_vars['adminurl'] = admin_url( 'admin.php' );
			$js_vars['errormsg'] = __( 'Error! Please fix the following and try again', 'campaignpress' );
			$js_vars['nonce'] = wp_create_nonce( 'campaignpress-nonce' );
			
			// group specific stuff
			if ( $plugin_page == 'campaignpress-groups' ) {
				$currencies = campaignpress_get_billing_currencies();
				foreach( $currencies as &$currency ) {
					$currency['ClientPaysAtStandardRate_desc'] = __( $currency['name'] , 'campaignpress' ) . ' (' . $currency['major'] . $currency['delivery'] . ' ' . __( 'plus', 'campaignpress' ). ' ' . $currency['recipient'] . ' ' . $currency['minor'] . '/email)';
					$currency['ClientPaysWithMarkup_desc'] = __( $currency['name'] , 'campaignpress' ) . ' (' . __( 'base cost of', 'campaignpress' ) . ' ' . $currency['major'] . $currency['delivery'] . ' ' . __( 'plus', 'campaignpress' ) . ' ' . $currency['recipient'] . ' ' . $currency['minor'] . '/email)';
				}
				$currency_list = array();
				foreach( $currencies as $code => $values ) {
					$currency_list[] = $code;
				}
				$js_vars['currencies'] = implode( ',', $currency_list );
				foreach( $currencies as $code => $values ) {
					$js_vars["currency_$code"] = implode( ',', $values );
				}
			}

			// give our script some vars
			wp_localize_script( 'campaignpress-admin', 'CampaignPress', $js_vars );		
			
			//  add our main menu link (also give it a handle for contextual help)
			$title = __( 'Campaign P.', 'campaignpress' );
			$campaignpress_dashboard_hook = add_menu_page( $title, $title, CAMPAIGNPRESS_ADMIN_READ_CAPABILITY, 'campaignpress', 'campaignpress_admin_dashboard', CAMPAIGNPRESS_PLUGIN_URL . '/images/icon.png' );
			
			// sub menu (also main menu) for dashboard
			$title_sub = __( 'Dashboard', 'campaignpress' );
			add_submenu_page( 'campaignpress', $title, $title_sub, CAMPAIGNPRESS_ADMIN_READ_CAPABILITY, 'campaignpress', 'campaignpress_admin_dashboard' );
		
			// sub menu for clients
			$title_sub = __( 'Clients', 'campaignpress' );
			$campaignpress_clients_hook = add_submenu_page( 'campaignpress', $title, $title_sub, CAMPAIGNPRESS_ADMIN_READ_CAPABILITY, 'campaignpress-clients', 'campaignpress_admin_clients' );
			
			// sub menu for groups
			$title_sub = __( 'Groups', 'campaignpress' );
			$campaignpress_groups_hook = add_submenu_page( 'campaignpress', $title, $title_sub, CAMPAIGNPRESS_ADMIN_READ_CAPABILITY, 'campaignpress-groups', 'campaignpress_admin_groups' );
			
			// sub menu for global settings
			$title_sub = __( 'Settings', 'campaignpress' );
			$campaignpress_settings_hook = add_submenu_page( 'campaignpress', $title, $title_sub, CAMPAIGNPRESS_ADMIN_READ_CAPABILITY, 'campaignpress-settings', 'campaignpress_admin_settings' );
			
			// sub menu for add-ons
			$title_sub = __( 'Add-Ons', 'campaignpress' );
			$campaignpress_addons_hook = add_submenu_page( 'campaignpress', $title, $title_sub, CAMPAIGNPRESS_ADMIN_READ_CAPABILITY, 'campaignpressaddons', 'campaignpress_admin_addons' );
		}
				
		// Add-Ons page
		function campaignpress_admin_addons() {
		
			global $wpdb, $campaignpress, $plugin_page;
					
			// make sure we've got access to this page
			campaignpress_admin_has_read_cap();
			
			// and check if we can edit stuff
			$can_edit = campaignpress_admin_has_edit_cap();
			
			// get a list of our addons
			$all_addons = campaignpress_get_addons();
			
			// determine which status we are on
			if ( isset( $_GET['status'] ) ) {
				$current_status = trim( $_GET['status'] );
			} else {
				// must be All
				$current_status = 'All';
			}
			
			$addons = array();
			$states = array();
			$states['All'] = count( $all_addons );
			foreach ($all_addons as $addon) {
				// increment the count
				if( true === $addon->addon_is_installed() ) {
					if( isset( $states['Installed'] ) ) {
						$states['Installed'] ++;
					} else {
						$states['Installed'] = 1;
					}
				}
				if ( 'Installed' == $current_status ) {
					// check if we need to add this bad boy
					if ( true === $addon->addon_is_installed() ) {
						$addons[] = $addon;
					}
				} else {
					// just add then all
					$addons[] = $addon;
				}
			}
		
			$style = '';
			$style_second = '';

			// load our settings page
			require_once CAMPAIGNPRESS_PLUGIN_DIR . '/admin/page-addons.php';
		}		
				
		// Dashboard page
		function campaignpress_admin_dashboard() {
		
			global $wpdb, $campaignpress;
		
			// make sure we've got access to this page
			campaignpress_admin_has_read_cap();
			
			// and check if we can edit stuff
			$can_edit = campaignpress_admin_has_edit_cap();
			
			$query = "SELECT id, description, occurred, item_type, item_id FROM $campaignpress->activity_table_name ORDER BY id DESC LIMIT 10;";
			$all_activity = $wpdb->get_results( $query );
			$all_activity_count = count( $all_activity );
			
			$query = $wpdb->prepare( "SELECT id, company, contact_name, email, created FROM $campaignpress->client_table_name WHERE status = %s ORDER BY created ASC;", 'Awaiting Approval' );
			$all_clients = $wpdb->get_results( $query );
			$all_clients_count = count( $all_clients );

			$style = '';
			
			// use magpie rss to grab our RSS feed from our website
      if( ! function_exists( 'fetch_rss' ) ) {
        require_once CAMPAIGNPRESS_PLUGIN_DIR . '/lib/magpierss/rss_fetch.inc.php';
      }
			$rss = array();
			$rss_result = fetch_rss( 'http://floatingmonk.co.nz/campaignpress/blog/feed/' );
			if( $rss_result ) {
				$rss = $rss_result;
			}

			// load our dashboard page
			require_once CAMPAIGNPRESS_PLUGIN_DIR . '/admin/page-dashboard.php';
		}
		
		// Clients page
		function campaignpress_admin_clients() {
		
			global $campaignpress, $campaignpress_settings, $wpdb, $plugin_page, $client_id;
			
			// make sure we've got access to this page
			campaignpress_admin_has_read_cap();
			
			// and check if we can edit stuff
			$can_edit = campaignpress_admin_has_edit_cap();
			
			$client_id = '';
			
			if( isset( $_GET['client'] ) ) {
				$client_id = $_GET['client'];
				if( 'new' != $client_id ) {
					$client_id = (int) trim( $_GET['client'] );
					$query = $wpdb->prepare( "SELECT * FROM $campaignpress->client_table_name WHERE id = %d;", $client_id );
					$client_db = $wpdb->get_row( $query );
				}
			}
			
			$client = new CampaignPressClient();
			$current_group = new CampaignPressGroup();
			
			if( strlen( $client_id ) > 0 ) {
				
				$all_groups = array();
				$all_activity = array();
				
				if( 'new' != $client_id ) { 
				
					$client->id = $client_id;
					$client->api_id = $client_db->api_id;
					$client->status = $client_db->status;
					$client->group_id = $client_db->group_id;
					$client->company = $client_db->company;
					$client->contact_name = $client_db->contact_name;
					$client->email = $client_db->email;
					$client->country = $client_db->country;
					$client->timezone = $client_db->timezone;
					$client->username = $client_db->username;
					$client->temp_password = $client_db->temp_password;
					$client->additional_info = $client_db->additional_info;
				
					// grab the activity for this client
					$act_desc_len = 40;
					$query = $wpdb->prepare( "SELECT id, description, occurred FROM $campaignpress->activity_table_name WHERE item_id = %d AND item_type = %s ORDER BY id DESC LIMIT 5;", $client_id, 'client' );
					$all_activity = $wpdb->get_results( $query );
					
					// see if we need to allow an approval email to be sent out
					$sent_welcome_email = '';
					if( 'Active' == $client->status ) {
						$query = $wpdb->prepare( "SELECT id FROM $campaignpress->activity_table_name WHERE item_id = %d AND item_type = %s AND ( description = %s OR description = %s OR description = %s ) LIMIT 1;", $client_id, 'client', 'Sent welcome email to client.', 'Created via sync.', 'Created via interface.' );
						$sent_welcome_email = $wpdb->get_var( $query );
					} else {
						$sent_welcome_email = 'Wrong Status';
					}
					
					// and the name for the current group
					$query = $wpdb->prepare( "SELECT * FROM $campaignpress->group_table_name WHERE id = %d;", $client_db->group_id );
					$current_group_db = $wpdb->get_row( $query );
					$current_group->id = $current_group_db->id;
					$current_group->name = $current_group_db->name;
					
					// we display a list of groups to select from
					$query = "SELECT id, name FROM $campaignpress->group_table_name ORDER BY name ASC;";
					$all_groups = $wpdb->get_results( $query );
					
					// get a list of timezones and countries
					$timezones = campaignpress_get_timezones();
					$countries = campaignpress_get_countries();
					
					$all_groups_count = count( $all_groups );
					$all_activity_count = count( $all_activity );
          
					// display the page
					require_once CAMPAIGNPRESS_PLUGIN_DIR . '/admin/page-client.php';
					
				} else {
				
					// new (actually existing in CM) client
					require_once CAMPAIGNPRESS_PLUGIN_DIR . '/admin/page-client-add-existing.php';
				}
				
			} else {
				
				// determine which status we are on
				if ( isset( $_GET['status'] ) ) {
					$current_status = trim( $_GET['status'] );
				} else {
					// must be All
					$current_status = 'All';
				}

				// grab our list of clients from the database
				$query = "SELECT id, company, contact_name, email, status FROM $campaignpress->client_table_name ORDER BY status ASC, company ASC;";
				$all_clients = $wpdb->get_results( $query );
				
				// determine the various statuses we have & build up our client list
				$clients = array();
				$states = array();
				$states['All'] = count( $all_clients );
				foreach ($all_clients as $client) {
					// increment the count
					if( isset( $states[$client->status] ) ) {
						$states[$client->status] ++;
					} else {
						$states[$client->status] = 1;
					}
					if ( 'All' != $current_status ) {
						// check if we need to add this bad boy
						if ( $client->status == $current_status ) {
							$clients[] = $client;
						}
					} else {
						// just add then all
						$clients[] = $client;
					}
				}
				
				// display the page
				require_once CAMPAIGNPRESS_PLUGIN_DIR . '/admin/page-clients.php';
			}
		}
		
		// Groups page
		function campaignpress_admin_groups() {
		
			global $campaignpress, $campaignpress_settings, $wpdb, $plugin_page;
	
			// make sure we've got access to this page
			campaignpress_admin_has_read_cap();
			
			// and check if we can edit stuff
			$can_edit = campaignpress_admin_has_edit_cap();
			
			// where we cut the description off at 
			$desc_len = 140;
			
			$group_id = '';
			
			if( isset( $_GET['group'] ) ) {
				$group_id = $_GET['group'];
				if( 'new' != $group_id ) {
					$group_id = (int) trim( $_GET['group'] );
					$query = $wpdb->prepare( "SELECT * FROM $campaignpress->group_table_name WHERE id = %d;", $group_id );
					$group_db = $wpdb->get_row( $query );
				}
			}
			
			if( strlen( $group_id ) > 0 ) {
			
				// create a new group for later...
				$group = new CampaignPressGroup();
				
				// list of currencies we're going to need for the page	
				$currencies = campaignpress_get_billing_currencies();

				// set this so we get no errors when counting
				$all_activity = array();
				$all_clients = array();
			
				if( 'new' != $group_id ) { 

					// populate our group
					$group->name = $group_db->name;
					$group->desc = $group_db->description;
					$group->access_level = $group_db->access_level;		// populates all the other access variables when we do this
					$group->billing_type = $group_db->billing_type;
					$group->billing_currency = $group_db->currency;
					$group->billing_delivery_fee = $group_db->delivery_fee;
					$group->billing_recipient_fee = $group_db->recipient_fee;
					$group->billing_spamtest_fee = $group_db->test_fee;
					$group->updated = $group_db->updated;
					
					// grab all the clients for this group
					$query = $wpdb->prepare( "SELECT id, company FROM $campaignpress->client_table_name WHERE group_id = %d;", $group_id );
					$all_clients = $wpdb->get_results( $query );
					
					// grab the activity for this group
					$act_desc_len = 40;
					$query = $wpdb->prepare( "SELECT id, description, occurred FROM $campaignpress->activity_table_name WHERE item_id = %d AND item_type = %s ORDER BY id DESC LIMIT 5;", $group_id, 'group' );
					$all_activity = $wpdb->get_results( $query );
				}
				
				$all_activity_count = count( $all_activity );		
				$all_clients_count = count( $all_clients );

				// load our group page
				require_once CAMPAIGNPRESS_PLUGIN_DIR . '/admin/page-group.php';
			
			} else {
			
				// grab our list of groups from the database
				$query = "SELECT id, name, description, access_level FROM $campaignpress->group_table_name ORDER BY id DESC;";
				$all_groups = $wpdb->get_results( $query );
				$all_groups_count = count( $all_groups );

				// load our groups page
				require_once CAMPAIGNPRESS_PLUGIN_DIR . '/admin/page-groups.php';
			}
			
		}
		
		// Settings page
		function campaignpress_admin_settings() {
		
			global $campaignpress, $wpdb, $plugin_page, $campaignpress_settings, $both_fields;
		
			// make sure we've got access to this page
			campaignpress_admin_has_read_cap();
			
			// and check if we can edit stuff
			$can_edit = campaignpress_admin_has_edit_cap();
			
			// we display a list of groups to select from
			$query = "SELECT id, name FROM $campaignpress->group_table_name ORDER BY name ASC;";
			$all_groups = $wpdb->get_results( $query );
			$all_groups_count = count( $all_groups );
			
			// we also need to grab a list of our fields (the ones that we care about)
			$query = "	SELECT * FROM $campaignpress->field_table_name 
								WHERE $campaignpress->field_table_name.id  
									NOT IN ( 
										SELECT campaignpress_field_id FROM $campaignpress->list_field_table_name
									)
								ORDER BY position ASC;";
			$all_fields = $wpdb->get_results( $query );
			$all_fields_count = count( $all_fields );
			
			// form our hidden & displayed fields
			$displayed_fields = array();
			$hidden_fields = array();
			$both_fields = array();
			foreach( $all_fields as $field ) {
				$cf = new CampaignPressField();
				$cf->id = $field->id;
				$cf->name = $field->name;
				$cf->datatype = $field->datatype;
				$cf->position = $field->position;
				$cf->required = $field->required;
				$cf->system_field = $field->system_field;
				$options = explode( '||', $field->value_options );
				foreach( $options as $option ) {
					$option = trim( $option );
					if( strlen( $option ) > 0 ) {
						$cf->add_option( $option );
					}
				}
				if( $field->displayed ) {
					$displayed_fields[] = $cf;
				} else {
					$hidden_fields[] = $cf;
				}
				$both_fields[] = $cf;
			}

			$timezones = campaignpress_get_timezones();
			$countries = campaignpress_get_countries();
			
			// make sure we've got a key to encrypt our passwords
			$cp_key = $campaignpress_settings['cp_key'];
			if( strlen( $cp_key ) < 1 ) {
				// we need to generate one!!!
				$cp_key = campaignpress_generate_key();
			}
			
			// now unencrypt the override mail password if we've got one
			$override_mail_password_unenc = $campaignpress_settings['override_mail_password'];
			if( strlen( $override_mail_password_unenc ) > 0 ) {
				$override_mail_password_unenc = campaignpress_decrypt( $override_mail_password_unenc, $campaignpress_settings['cp_key'] );
			}
			
			// load our settings page
			require_once CAMPAIGNPRESS_PLUGIN_DIR . '/admin/page-settings.php';
		}
		
		// Footer version info etc.
		function campaignpress_footer() {
			
			echo '<div class="footer-link">';
			printf( __('%1$s version %2$s', 'campaignpress'), '<a href="http://floatingmonk.co.nz/campaignpress/">Campaign Press</a>', CAMPAIGNPRESS_VERSION );
			echo '</div>';
		}
		
		// Contextual help
		function campaignpress_help( $contextual_help, $screen_id, $screen ) {

			// our hooks so we know what page we're on
			global $plugin_page, $campaignpress_dashboard_hook, $campaignpress_clients_hook, $campaignpress_groups_hook, $campaignpress_settings_hook, $campaignpress_addons_hook;

			switch( $screen_id ) 
			{
				case $campaignpress_dashboard_hook:
					require_once CAMPAIGNPRESS_PLUGIN_DIR . '/admin/help-dashboard.php';
					return '';
					break;
					
				case $campaignpress_clients_hook:
					require_once CAMPAIGNPRESS_PLUGIN_DIR . '/admin/help-clients.php';
					return '';
					break;
					
				case $campaignpress_groups_hook:
					require_once CAMPAIGNPRESS_PLUGIN_DIR . '/admin/help-groups.php';
					return '';
					break;
					
				case $campaignpress_settings_hook:
					require_once CAMPAIGNPRESS_PLUGIN_DIR . '/admin/help-settings.php';
					return '';
					break;
					
				case $campaignpress_addons_hook:
					require_once CAMPAIGNPRESS_PLUGIN_DIR . '/admin/help-addons.php';
					return '';
					break;
			}
			
			// we return nothing as we've already included our help!
			return $contextual_help;
		}
		
		// called from page load
		function campaignpress_display_config_errors( $type = 'full' ) {
			
			global $campaignpress_settings;
			$input = $campaignpress_settings;
			$errors = campaignpress_check_settings( $input );
			
			if( count( $errors ) > 0 ) {
				if( 'simple' == $type ) {
					?>
						<div class="error" id="custom-error-box">
							<p><?php _e( 'You have a problem with your Campaign Press configuration.  Please rectify this via', 'campaignpress' ); ?> <a href="><?php admin_url( 'admin.php?page=campaignpress-settings' ); ?>"><?php _e( 'Settings', 'campaignpress'); ?></a>.</p>
						</div>
					<?php
				} else {
					?>
						<div class="error" id="custom-error-box">
							<p><?php _e( 'There is a problem with your Campaign Press configuration', 'campaignpress' ); ?></p>
							<p><span id="custom-error-text">
									<ul>
										<?php
											foreach( $errors as $error ) {
												echo "<li>$error</li>";
											}
										?>
									</ul>
								</span>
							</p>
						</div>
					<?php
				}
				return true;
			}
			
			return false;
		}
		
	/**
	* Functions that do ... stuff
	*/
	
		// Update field positions for custom fields
		add_action( 'wp_ajax_campaignpress_update_field_positions', 'campaignpress_update_field_positions' );
		function campaignpress_update_field_positions() {
			
			if ( ! campaignpress_admin_has_edit_cap() ) 
				die ( 'Access denied.');
			
			$nonce = $_POST['nonce'];
			if ( ! wp_verify_nonce( $nonce, 'campaignpress-nonce' ) )
				die ( 'Error!');
				
			global $wpdb, $campaignpress;
			
			try
			{
			
				// first remove all the positions from the previous current fields
				$query = "	UPDATE $campaignpress->field_table_name 
									SET 
										$campaignpress->field_table_name.position = 0,
										$campaignpress->field_table_name.displayed = 0
									WHERE 
										$campaignpress->field_table_name.displayed = 1;";
				$wpdb->query( $query );

				$fields = explode( ',', $_POST['fields'] );
				
				$field_count = count( $fields );
				for( $i = 0; $i < $field_count; $i++ ) {
				
					$field_id = (int) $fields[$i];
					$field_position = $i + 1;

					$params = array( 	
						'displayed' => 1,
						'position' => $field_position
					);
					
					$wpdb->update ( $campaignpress->field_table_name, $params, array( 'id' => $field_id ) );
					
				}
			
				// now we return success
				echo true;
			}
			catch( Exception $e )
			{
				// we've got problems, echo them (will get picked up by the JS call & displayed on the page)
				echo $e->getMessage();
			}

			exit();

		}
		
		// Add an existing (CM) client
		add_action( 'wp_ajax_campaignpress_add_existing_client', 'campaignpress_add_existing_client' );
		function campaignpress_add_existing_client() {
		
				if ( ! campaignpress_admin_has_edit_cap() ) 
				die ( 'Access denied.');
			
			$nonce = $_POST['nonce'];
			if ( ! wp_verify_nonce( $nonce, 'campaignpress-nonce' ) )
				die ( 'Error!');

			global $wpdb, $campaignpress, $campaignpress_settings;
			
			try
			{
				// the api ID
				$api_id = trim( $_POST['api_client_id'] );
				if( strlen( $api_id ) < 1 ) {
					throw new Exception( '<li>' . __( 'API Client ID cannot be blank.', 'campaignpress' ) . '</li>' );
				}
				
				// first we make sure that we don't already have this client sitting in CM
				$query = $wpdb->prepare( "SELECT id FROM $campaignpress->client_table_name WHERE api_id = %s;",  $api_id );
				$client_id = $wpdb->get_var( $query );
				if( strlen( $client_id ) > 0 ) {
					throw new Exception( '<li>' . __( 'Client already exists in Campaign Press.', 'campaignpress' )  . '</li>' );
				}
				
				// grab our details from CM
				require_once CAMPAIGNPRESS_PLUGIN_DIR . '/lib/CMBase.php';
				$cm = new CampaignMonitor( $campaignpress_settings['apikey'] );
				$cm_result = $cm->clientGetDetail( $api_id );
				
				if ( isset( $cm_result['anyType']['Code'] ) || ! isset( $cm_result['anyType'] ) ) {
					// we must have an error
					throw new Exception( '<li>' . $cm_result['anyType']['Message'] . '</li>' );
				}
				
				$cm_client_detail = $cm_result['anyType'];

				// check the group 
				$group = new CampaignPressGroup();
				$group->access_level = $cm_client_detail['AccessAndBilling']['AccessLevel'];
				$group->billing_type = $cm_client_detail['AccessAndBilling']['BillingType'];
				$group->billing_currency = $cm_client_detail['AccessAndBilling']['Currency'];
				$group->billing_delivery_fee = $cm_client_detail['AccessAndBilling']['DeliveryFee'];
				$group->billing_recipient_fee = $cm_client_detail['AccessAndBilling']['CostPerRecipient'];
				$group->billing_spamtest_fee = $cm_client_detail['AccessAndBilling']['DesignAndSpamTestFee'];
				
				$query = $wpdb->prepare( "	SELECT id FROM $campaignpress->group_table_name 
														WHERE access_level = %d 
														AND billing_type = %s
														AND currency = %s
														AND delivery_fee = %d
														AND recipient_fee = %d
														AND test_fee = %d;", 
														$group->access_level,
														$group->billing_type,
														$group->billing_currency,
														$group->billing_delivery_fee,
														$group->billing_recipient_fee,
														$group->billing_spamtest_fee
												);
														
				$group_id = $wpdb->get_var( $query );
				if( strlen( $group_id ) < 1 ) {
					// create the group
					$params = array( 	
											'name' => "Temp Name",
											'description' => "New group for client " . $cm_client_detail['BasicDetails']['CompanyName'],
											'access_level' => $group->access_level,
											'billing_type' => $group->billing_type,
											'currency' => $group->billing_currency,
											'delivery_fee' => $group->billing_delivery_fee,
											'recipient_fee' => $group->billing_recipient_fee,
											'test_fee' => $group->billing_spamtest_fee
										);
										
					$wpdb->insert ( $campaignpress->group_table_name, $params );
					
					// get the last insert ID so we know the group ID
					$group_id = $wpdb->insert_id;
					$log_message = __( 'Created via interface.', 'campaignpress' );
					campaignpress_log_activity( 'group', $group_id, $log_message );
				}
				
				// create our client
				$wpdb->insert	( 	$campaignpress->client_table_name, 
										array( 	
													'api_id' => $api_id, 
													'created' => date('Y-m-d H:i:s'),
													'status' => 'Active',
													'group_id' => $group_id,
													'company' => $cm_client_detail['BasicDetails']['CompanyName'],
													'contact_name' => $cm_client_detail['BasicDetails']['ContactName'],
													'email' => $cm_client_detail['BasicDetails']['EmailAddress'],
													'country' => $cm_client_detail['BasicDetails']['Country'],
													'timezone' => $cm_client_detail['BasicDetails']['Timezone'],
													'username' => $cm_client_detail['AccessAndBilling']['Username']
												)
									);
												
				// log activity
				$client_id = $wpdb->insert_id;
				$log_message = __( 'Created via interface.', 'campaignpress' );
				campaignpress_log_activity( 'client', $client_id, $log_message );

				// all done
				echo true;
			
			}
			catch( Exception $e )
			{
				echo $e->getMessage();
			}

			exit();
		}
		
		// Approve a client
		add_action( 'wp_ajax_campaignpress_approve_client', 'campaignpress_approve_client' );
		function campaignpress_approve_client() {
				
			if ( ! campaignpress_admin_has_edit_cap() ) 
				die ( 'Access denied.');
			
			$nonce = $_POST['nonce'];
			if ( ! wp_verify_nonce( $nonce, 'campaignpress-nonce' ) )
				die ( 'Error!');

			global $wpdb, $campaignpress, $campaignpress_settings;
			
			try
			{
				// first we make sure that this is a client, and that their status is Awaiting Approval
				$query = $wpdb->prepare( "SELECT * FROM $campaignpress->client_table_name WHERE id = %d;",  $_POST['client_id'] );
				$client_db = $wpdb->get_row( $query );
				
				// create a client object out of our details
				$client = new CampaignPressClient();
				
				// populate it
				$client->id = $client_db->id;
				$client->status = $client_db->status;
				$client->group_id = $client_db->group_id;
				$client->company = $client_db->company;
				$client->contact_name = $client_db->contact_name;
				$client->email = $client_db->email;
				$client->username = $client_db->username;
				$client->country = $client_db->country;
				$client->timezone = $client_db->timezone;
				$client->additional_info = $client_db->additional_info;
				
				if( 'Awaiting Approval' == $client->status ) {
				
					// first we have a go at creating this bad boy in CM
					require_once CAMPAIGNPRESS_PLUGIN_DIR . '/lib/CMBase.php';
					$cm = new CampaignMonitor( $campaignpress_settings['apikey'] );

					// client's basic details 
					$cm_result = $cm->clientCreate( $client->company, $client->contact_name, $client->email, $client->country, $client->timezone );	

          if( ! isset( $cm_result['anyType'] ) ) {
            // problems for sure!
            throw new Exception( '<li>' . __( 'Unexpected result from API.', 'campaignpress' ) . '</li>' );
          }
          
          // sometimes we get a code back... sometimes we don't... :(
          if( is_array( $cm_result['anyType'] ) && ( 0 != $cm_result['anyType']['Code'] ) ) {
            // also problems for sure - we received a code and it was NOT good!
            throw new Exception( '<li>' . $cm_result['anyType']['Message'] . '</li>' );
          }
          
          // everything else is good news - grab the API ID!
					$client->api_id = $cm_result['anyType'];
					
					// now access
					$query = $wpdb->prepare( "SELECT * FROM $campaignpress->group_table_name WHERE id = %d;", $client->group_id );
					$group = $wpdb->get_row( $query );
					
					// only proceed with these things if the access level is above zero
					if( $group->access_level > 0 ) {

						// first sort out username & password
						if( strlen( $client->temp_password ) < 1 ) {
							$unencrypted_password = campaignpress_generate_password();
						} else {
							$unencrypted_password = campaignpress_decrypt( $client->temp_password, $campaignpress_settings['cp_key'] );
						}
						$client->temp_password = campaignpress_encrypt( $unencrypted_password, $campaignpress_settings['cp_key'] );
						
						if( strlen( $client->username ) < 1 ) {
							// generate a username from the company name
							$username = strtolower( str_replace( ' ', '_', $client->company ) );
							$client->username = preg_replace('/[^_A-Za-z0-9-]/', '', $username);
						}

						$cm_result = $cm->clientUpdateAccessAndBilling( 
																						$client->api_id, 
																						$group->access_level, 
																						$client->username, 
																						$unencrypted_password, 
																						$group->billing_type, 
																						$group->currency, 
																						$group->delivery_fee, 
																						$group->recipient_fee, 
																						$group->test_fee 
																						);
						if( ! isset( $cm_result['Result']['Code'] ) || 0 != $cm_result['Result']['Code'] ) {
							throw new Exception( '<li>' . $cm_result['Result']['Message'] . '</li>' );
						}

						// at this point everything's OK - update the DB with new details
						$params = array( 	
												'api_id' => $client->api_id,
												'status' => 'Active',
												'username' => $client->username,
												'temp_password' => $client->temp_password
											);
					

						// update the database
						$wpdb->update ( $campaignpress->client_table_name, $params, array( 'id' => $client->id ) );							
						
					}
					
					// log approve complete
					$log_message = __( 'Client approved.', 'campaignpress');
					campaignpress_log_activity( 'client', $client->id, $log_message );
					
					// notify the client about the approval if required (can be done manually later)
					if( $campaignpress_settings['cn_on_approval_email'] ) {
						campaignpress_notify_client( $client, 'welcome' );
					}		

					echo true;
				}
			}
			catch( Exception $e )
			{
				echo $e->getMessage();
			}

			exit();
					
		}
		
		// Send a welcome email to a client
		add_action( 'wp_ajax_campaignpress_welcome_client', 'campaignpress_welcome_client' );
		function campaignpress_welcome_client() {
				
			if ( ! campaignpress_admin_has_edit_cap() ) 
				die ( 'Access denied.');
			
			$nonce = $_POST['nonce'];
			if ( ! wp_verify_nonce( $nonce, 'campaignpress-nonce' ) )
				die ( 'Error!');

			global $wpdb, $campaignpress, $campaignpress_settings;
			
			try
			{
				$client_id = $_POST['client_id'];
			
				// first we make sure that this is a client, and that they havent already had their welcome email sent 
				$query = $wpdb->prepare( "SELECT * FROM $campaignpress->client_table_name WHERE id = %d;",  $client_id );
				$client_db = $wpdb->get_row( $query );
				
				if( ! $client_db ) {
					throw new Exception( '<li>' . __( 'Invalid client.', 'campaignpress' ) . '</li>' );
				}
				
				if( 'Active' != $client_db->status ) {
					throw new Exception( '<li>' . __( 'Client is not active.', 'campaignpress' ) . '</li>' );
				}
				
				$query = $wpdb->prepare( "SELECT id FROM $campaignpress->activity_table_name WHERE item_id = %d AND item_type = %s AND description = %s LIMIT 1;", $client_id, 'client', 'Sent welcome email to client.' );
				$sent_welcome_email = $wpdb->get_var( $query );
				if( $sent_welcome_email ) {
					throw new Exception( '<li>' . __( 'Welcome email already sent.', 'campaignpress' ) . '</li>' );
				}
				
				// create a client object out of our details
				$client = new CampaignPressClient();
				
				// populate it
				$client->id = $client_db->id;
				$client->status = $client_db->status;
				$client->group_id = $client_db->group_id;
				$client->company = $client_db->company;
				$client->contact_name = $client_db->contact_name;
				$client->email = $client_db->email;
				$client->username = $client_db->username;
				$client->temp_password = $client_db->temp_password;
				$client->country = $client_db->country;
				$client->timezone = $client_db->timezone;
				$client->additional_info = $client_db->additional_info;
				
				// send welcome email!
				campaignpress_notify_client( $client, 'welcome' );

				echo true;
			}
			catch( Exception $e )
			{
				echo $e->getMessage();
			}

			exit();
					
		}
		
		// Save a client
	   add_action( 'wp_ajax_campaignpress_save_client', 'campaignpress_save_client' );
		function campaignpress_save_client() {
				
			if ( ! campaignpress_admin_has_edit_cap() ) 
				die ( 'Access denied.');
			
			$nonce = $_POST['nonce'];
			if ( ! wp_verify_nonce( $nonce, 'campaignpress-nonce' ) )
				die ( 'Error!');

			global $wpdb, $campaignpress, $campaignpress_settings;
			
			// validate our goodies
			try
			{
				// create a client object out of our details
				$client = new CampaignPressClient();
				
				// populate it
				$client->id = $_POST['client_id'];
				$client->status = $_POST['client_status'];
				$client->group_id = $_POST['group_id'];
				$client->company = $_POST['client_company'];
				$client->contact_name = $_POST['client_contact_name'];
				$client->email = $_POST['client_email'];
				$client->username = $_POST['client_username'];
				$client->country = $_POST['client_country'];
				$client->timezone = $_POST['client_timezone'];
				$client->additional_info = $_POST['client_additional_info'];

				// validate (will tidy up the data and throw exception with errors if we've got issues)
				$client->validate_data();
				
				// process any custom fields
				$query = $wpdb->prepare( "	SELECT * FROM $campaignpress->field_table_name WHERE displayed = %d AND system_field != %d ORDER BY position ASC;", 1, 1 );
				$all_fields = $wpdb->get_results( $query );
				
				$errors = array();
				
				foreach( $all_fields as $field_db ) {
				
					$field = new CampaignPressField();
					$field->id = $field_db->id;
					$field->name = $field_db->name;
					$field->datatype = $field_db->datatype;
					$field->position = $field_db->position;
					$field->system_field = $field_db->system_field;
					// we forcefully make no fields required - we don't want annoying required error messages popping up :-)
					$field->required = 0;
					// add the options
					$options = explode( '||', $field_db->value_options );
					foreach( $options as $option ) {
						$option = trim( $option );
						if( strlen( $option ) > 0 ) {
							$field->add_option( $option );
						}
					}
					
					try
					{
						// validate that bad boy & if we've got any errors throw an exception
						$_POST = $field->validate_value( $_POST );
					}
					catch( Exception $e )
					{
						$errors[] = $e->getMessage();
					}
				}
				
				if( count( $errors ) > 0 ) {
					$error_text = '';
					foreach( $errors as $error ) {
						$error_text .= "<li>$error</li>";
					}
					throw new Exception( $error_text );
				}
				
				// we only allow group changes for unapproved clients at the moment, so we set this initially but might unset later
				$group_id = $client->group_id;
				$username = $client->username;
				
				// get the API id from the DB
				if( 'Awaiting Approval' != $client->status ) {
				
					$query = $wpdb->prepare( "SELECT api_id FROM $campaignpress->client_table_name WHERE id = %d;", $client->id );
					$client_db = $wpdb->get_row( $query );
					$client->api_id = $client_db->api_id;
					
          // first we give CM a go
					require_once CAMPAIGNPRESS_PLUGIN_DIR . '/lib/CMBase.php';
					$cm = new CampaignMonitor( $campaignpress_settings['apikey'] );

					// update the client's basic details 
					$cm_result = $cm->clientUpdateBasics( $client->api_id, $client->company, $client->contact_name, $client->email, $client->country, $client->timezone );
					if( ! isset( $cm_result['anyType']['Code'] ) || 0 != $cm_result['anyType']['Code'] ) {
						throw new Exception( '<li>Updating basic details failed: ' . $cm_result['anyType']['Message'] . '</li>' );
					}

					if( campaignpress_client_can_update_access() ) { 
						// now billing details from the group
						$query = $wpdb->prepare( "SELECT * FROM $campaignpress->group_table_name WHERE id = %d;", $client->group_id );
						$group = $wpdb->get_row( $query );
						
						$password = '';
            
						$cm_result = $cm->clientUpdateAccessAndBilling( 
																						$client->api_id, 
																						$group->access_level, 
																						$client->username, 
																						$password, 
																						$group->billing_type, 
																						$group->currency, 
																						$group->delivery_fee, 
																						$group->recipient_fee, 
																						$group->test_fee 
																						);
						if( ! isset( $cm_result['anyType']['Code'] ) || 0 != $cm_result['anyType']['Code'] ) {
							throw new Exception( '<li>Updating access &amp; billing details failed: ' . var_dump($cm_result). '</li>' );
						}
					} else {
						$group_id = '';
						$username = '';
						$password = '';
					}
				}
				
				// at this point everything's OK - insert or update!
				$params = array( 	
										'created' => date('Y-m-d H:i:s'),
										'status' => $client->status,
										'company' => $client->company,
										'contact_name' => $client->contact_name,
										'email' => $client->email,
										'country' => $client->country,
										'timezone' => $client->timezone,
										'additional_info' => $client->additional_info
									);
									
				if( $username != '' ) {
					$params['username'] = $username;
				}
				
				if( $group_id != '' ) {
					$params['group_id'] = $group_id;
				}
				
				$response = 'added';
				if( strlen( $client->id ) < 1 ) {
					throw new Exception( 'Adding of clients is as-yet unsupported.  Are you trying to be sneaky?' );
				} else {
					// update the database
					$wpdb->update( $campaignpress->client_table_name, $params, array( 'id' => $client->id ) );
					$log_message = __( 'Client modified &amp; saved via interface.', 'campaignpress' );
					$response = 'saved';
				}
				
				// log activity
				campaignpress_log_activity( 'client', $client->id, $log_message );
				
				// now update those custom fields!
				foreach( $all_fields as $field ) {
				
					$val = $_POST["cp-field-$field->id"];
					
					// now we do the inserts
					$params = array( 	
										'field_value' => $val,
										'campaignpress_client_id' => $client->id,
										'campaignpress_field_id' => $field->id
									);

					// insert into or update the database
					$query = $wpdb->prepare( "SELECT id FROM $campaignpress->client_field_value_table_name WHERE campaignpress_client_id = %d AND campaignpress_field_id = %d;", $client->id, $field->id );
					$field_value_id = $wpdb->get_var( $query );
					
					if( strlen( $field_value_id ) > 0 ) {
						// update
						$wpdb->update( $campaignpress->client_field_value_table_name, $params, array( 'campaignpress_client_id' => $client->id, 'campaignpress_field_id' => $field->id ) );
					} else {
						// insert
						$wpdb->insert( $campaignpress->client_field_value_table_name, $params );
					}
				}
			
				// now we return success
				echo $response;
			}
			catch( Exception $e )
			{
				// we've got problems, echo them (will get picked up by the JS call & displayed on the page)
				echo $e->getMessage();
			}

			exit();
		}
		
		// Delete a client
		add_action( 'wp_ajax_campaignpress_delete_client', 'campaignpress_delete_client' );
		function campaignpress_delete_client() {
				
			if ( ! campaignpress_admin_has_edit_cap() ) 
				die ( 'Access denied.');
			
			$nonce = $_POST['nonce'];
			if ( ! wp_verify_nonce( $nonce, 'campaignpress-nonce' ) )
				die ( 'Error!');

			global $wpdb, $campaignpress;
			
			// validate our goodies (only allow deletion of inactive clients)
			try
			{
				if( empty( $_POST['client_id'] ) ) {
					throw new Exception( 'Invalid client ID.' );
				} else {
					$client_id = absint( $_POST['client_id'] );
					$query = $wpdb->prepare( "SELECT status FROM $campaignpress->client_table_name WHERE id = %d LIMIT 1;", $client_id );
					$status = $wpdb->get_var( $query );
					if( 'Active' != $status ) {
						// delete client's audit history
						$query = $wpdb->prepare( "DELETE FROM $campaignpress->activity_table_name WHERE item_id = %d AND item_type = %s;", $client_id, 'client' );
						$wpdb->query( $query );
						// delete client's field values
						$query = $wpdb->prepare( "DELETE FROM $campaignpress->client_field_value_table_name WHERE campaignpress_client_id = %d;", $client_id );
						$wpdb->query( $query );
						// delete the main client
						$query = $wpdb->prepare( "DELETE FROM $campaignpress->client_table_name WHERE id = %d LIMIT 1;", $client_id );
						$wpdb->query( $query );
					}
				}
			
				// now we return success
				echo true;
			}
			catch( Exception $e )
			{
				// we've got problems, echo them (will get picked up by the JS call & displayed on the page)
				echo $e->getMessage();
			}

			exit();
		}
		
		// Delete a group
		add_action( 'wp_ajax_campaignpress_delete_group', 'campaignpress_delete_group' );
		function campaignpress_delete_group() {
				
			if ( ! campaignpress_admin_has_edit_cap() ) 
				die ( 'Access denied.');
			
			$nonce = $_POST['nonce'];
			if ( ! wp_verify_nonce( $nonce, 'campaignpress-nonce' ) )
				die ( 'Error!');

			global $wpdb, $campaignpress;
			
			// validate our goodies
			try
			{
				if( empty( $_POST['group_id'] ) ) {
					throw new Exception( 'Invalid group ID.' );
				} else {
				
					$group_id = absint( $_POST['group_id'] );
					
					// make sure we don't have any clients
					$query = $wpdb->prepare( "SELECT client_id FROM $campaignpress->client_table_name WHERE group_id = %d LIMIT 1;", $group_id );
					$client_db = $wpdb->get_row( $query );
					if( isset( $client_db ) ) {
						throw new Exception( __( 'Cannot delete group while clients belong to it.', 'campaignpress' ) );
					}
					
					// all good, delete
					$query = $wpdb->prepare( "DELETE FROM $campaignpress->group_table_name WHERE id = %d LIMIT 1;", $group_id );
					$wpdb->query( $query );
				}
			
				// now we return success
				echo true;
			}
			catch( Exception $e )
			{
				// we've got problems, echo them (will get picked up by the JS call & displayed on the page)
				echo $e->getMessage();
			}

			exit();
		}
		
		// Save a group
	   add_action( 'wp_ajax_campaignpress_save_group', 'campaignpress_save_group' );
		function campaignpress_save_group() {
				
			if ( ! campaignpress_admin_has_edit_cap() ) 
				die ( 'Access denied.');
			
			$nonce = $_POST['nonce'];
			if ( ! wp_verify_nonce( $nonce, 'campaignpress-nonce' ) )
				die ( 'Error!');

			global $wpdb, $campaignpress_settings, $campaignpress;
			
			$group_id = trim( $_POST['group_id'] );
			if( 'new' != $group_id ) {
				$group_id = (int) $group_id;
			}
			
			// validate our goodies
			try
			{
				// create a group object out of our details
				$group = new CampaignPressGroup();

				// populate it
				$group->name = $_POST['group_name'];
				$group->desc = $_POST['group_desc'];
				$group->access_reports = $_POST['group_access_reports'];
				$group->access_sub_mgmt = $_POST['group_access_sub_mgmt'];
				$group->access_sub_imports = $_POST['group_access_sub_imports'];
				$group->access_camp_create_send = $_POST['group_access_camp_create_send'];
				$group->access_camp_import = $_POST['group_access_camp_import'];
				$group->access_spam = $_POST['group_access_spam'];
				$group->billing_type = $_POST['group_billing_type'];
				$group->billing_currency = $_POST['group_billing_currency'];
				$group->billing_delivery_fee = $_POST['group_billing_delivery_fee'];
				$group->billing_recipient_fee = $_POST['group_billing_recipient_fee'];
				$group->billing_spamtest_fee = $_POST['group_billing_spamtest_fee'];
				
				// validate (will tidy up the data and throw exception with errors if we've got issues)
				$group->validate_data();

				// at this point everything's OK - insert or update!
				$params = array( 	
										'name' => $group->name,
										'description' => $group->desc,
										'access_level' => $group->access_level,
										'billing_type' => $group->billing_type,
										'currency' => $group->billing_currency,
										'delivery_fee' => $group->billing_delivery_fee,
										'recipient_fee' => $group->billing_recipient_fee,
										'test_fee' => $group->billing_spamtest_fee
									);
				
				$response = 'added';

				if( 'new' == $group_id || empty( $group_id ) ) {
					// insert into the database
					$wpdb->insert ( $campaignpress->group_table_name, $params );
					$group_id = $wpdb->insert_id;
					$log_message = __( 'Group created via interface.', 'campaignpress' );
				} else {
					// update the database
					$wpdb->update ( $campaignpress->group_table_name, $params, array( 'id' => $group_id ) );
					$log_message = __( 'Group modified &amp; saved via interface.', 'campaignpress' );
					$response = 'saved';
				}
				
				// log activity
				campaignpress_log_activity( 'group', $group_id, $log_message );
			
				// now we return success
				echo $response;
			}
			catch( Exception $e )
			{
				// we've got problems, echo them (will get picked up by the JS call & displayed on the page)
				echo $e->getMessage();
			}

			exit();
		}
	
	/**
	* Styles
	*/
	
		// hooks
		add_action( 'admin_print_styles', 'campaignpress_admin_enqueue_styles' );
		
		function campaignpress_admin_enqueue_styles() {
		
			global $plugin_page;

			if ( ! isset( $plugin_page ) || 
					( 	'campaignpress' != $plugin_page && 
						'campaignpressaddons' != $plugin_page && 
						'campaignpress-settings' != $plugin_page && 
						'campaignpress-groups' != $plugin_page && 
						'campaignpress-clients' != $plugin_page ) )
				return;
				
			wp_enqueue_style( 'campaignpress-jquery-ui', CAMPAIGNPRESS_PLUGIN_URL . '/css/jquery-ui-1.8.5.custom.css', array(), CAMPAIGNPRESS_VERSION, 'all' );
			wp_enqueue_style( 'campaignpress-admin', CAMPAIGNPRESS_PLUGIN_URL . '/css/styles-admin.css', array(), CAMPAIGNPRESS_VERSION, 'all' );
			
		}
	
	
	/**
	* Access functions
	*/
	
		function campaignpress_admin_has_edit_cap() {
			return current_user_can( CAMPAIGNPRESS_ADMIN_READ_WRITE_CAPABILITY );
		}

		function campaignpress_admin_has_read_cap() {
			if ( ! current_user_can ( CAMPAIGNPRESS_ADMIN_READ_CAPABILITY ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.', 'campaignpress') );
			}
		}
	
		function campaignpress_client_can_update_access() {
			// can't do this at the moment - limit of the API
			return false;
		}		
	
		function campaignpress_show_addon_advert() {
			
			if( ! CAMPAIGNPRESS_SHOW_ADDON_ADVERTS )
				return false;
				
			return true;
				
			$check = mt_rand( 0, 99 );
			if( $check > 30 )
				return true;

			return false;
		}

	/**
	* Installation & Uninstallation
	*/
		// hooks
		add_action( 'activate_' . CAMPAIGNPRESS_PLUGIN_BASENAME, 'campaignpress_install' );
		
		// disabled so we don't blitz any data that we've got stored if we accidentally deactivate
		//add_action( 'deactivate_' . CAMPAIGNPRESS_PLUGIN_BASENAME, 'campaignpress_uninstall' );
		
		function campaignpress_install() {
    
      if( ! current_user_can( 'activate_plugins' ) ) {
				 exit( __( 'Insufficient privileges.', 'campaignpress' ) );
      }
      
			// for accessing our wp database
			global $wpdb, $campaignpress;
			
			$charset_collate = '';
			if ( $wpdb->has_cap( 'collation' ) ) {
				if ( ! empty( $wpdb->charset ) )
					$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
				if ( ! empty( $wpdb->collate ) )
					$charset_collate .= " COLLATE $wpdb->collate";
			}
			
			// addons table
			$table_name = $campaignpress->addon_table_name;
			if ( ! campaignpress_table_exists( $table_name ) ) {
			
				$wpdb->query( "	CREATE TABLE IF NOT EXISTS $table_name (
										`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
										`code` VARCHAR(100) NOT NULL ,
										`name` VARCHAR(255) NOT NULL ,
										`updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
										`description` TEXT NULL ,
										`released` DATETIME NULL DEFAULT NULL ,
										`latest_version` VARCHAR(20) NOT NULL ,
										`required_cp_version` VARCHAR(20) NOT NULL ,
										`class_name` VARCHAR(100) NOT NULL ,
										`dependent_classes` TEXT NULL ,
										PRIMARY KEY (`id`) ) $charset_collate;" );
										
				// insert our current known plugins
				$params = array( 	
										'name' => 'Sync',
										'code' => 'sync',
										'description' => 'Synchronise clients, groups, subscriber lists & custom fields from Campaign Monitor with Campaign Press.',
										'released' => '2010-10-15',
										'latest_version' => '1.0.0',
										'required_cp_version' => '1.0.0',
										'class_name' => 'CampaignPressSyncHelper'
									);
				$wpdb->insert ( $campaignpress->addon_table_name, $params );
				
				$params = array( 	
										'name' => 'Custom Fields',
										'code' => 'custom-fields',
										'description' => 'Use custom fields to gather any data you want from your clients through the sign up form.',
										'released' => '2010-10-18',
										'latest_version' => '1.0.0',
										'required_cp_version' => '1.0.0',
										'class_name' => 'CampaignPressCFHelper'
									);
				$wpdb->insert ( $campaignpress->addon_table_name, $params );
				
				$params = array( 	
										'name' => 'Subscriber Link',
										'code' => 'subscriber-link',
										'description' => 'Link your sign up form to a subscriber list within Campaign Monitor to allow opt-in or automatic subscription & population of custom fields on that list.  Requires CM Sync &amp; Custom Fields add-ons.',
										'released' => '2010-10-20',
										'latest_version' => '1.0.0',
										'required_cp_version' => '1.0.0',
										'class_name' => 'CampaignPressSLHelper',
										'dependent_classes' => 'CampaignPressSyncHelper,CampaignPressCFHelper'
									);
				$wpdb->insert ( $campaignpress->addon_table_name, $params );				
			
			}
				
			if ( ! campaignpress_table_exists( $table_name ) ) {
				exit( sprintf( __( 'Could not create table in database (%s). Activation aborted.', 'campaignpress' ), $table_name ) );
			}
			
			// groups table
			$table_name = $campaignpress->group_table_name;
			if ( ! campaignpress_table_exists( $table_name ) ) {
			
				$wpdb->query( "	CREATE TABLE IF NOT EXISTS $table_name (
										`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
										`name` VARCHAR(100) NOT NULL ,
										`updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
										`description` TEXT NULL ,
										`access_level` TINYINT(2) NOT NULL ,
										`billing_type` VARCHAR(50) NOT NULL ,
										`currency` VARCHAR(10) NULL ,
										`delivery_fee` DECIMAL(20,2) NULL ,
										`recipient_fee` DECIMAL(20,2) NULL ,
										`test_fee` DECIMAL(20,2) NULL ,
										PRIMARY KEY (`id`) ) $charset_collate;" );
			}
				
			if ( ! campaignpress_table_exists( $table_name ) ) {
				exit( sprintf( __( 'Could not create table in database (%s). Activation aborted.', 'campaignpress' ), $table_name ) );
			}
			
			// clients table
			$table_name = $campaignpress->client_table_name;
			if ( ! campaignpress_table_exists( $table_name ) ) {
			
				$wpdb->query( "	CREATE TABLE IF NOT EXISTS $table_name (
										`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
										`api_id` CHAR(32) NULL ,
										`created` DATETIME NULL DEFAULT NULL ,
										`updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
										`status` VARCHAR(100) NOT NULL ,
										`company` VARCHAR(150) NOT NULL ,
										`contact_name` VARCHAR(150) NOT NULL ,
										`email` VARCHAR(200) NOT NULL ,
										`username` VARCHAR(255) NULL ,
										`temp_password` VARCHAR(255) NULL ,
										`country` VARCHAR(50) NOT NULL ,
										`timezone` VARCHAR(100) NOT NULL ,
										`additional_info` TEXT NULL ,
										`group_id` INT UNSIGNED NOT NULL ,
										PRIMARY KEY (`id`) ,
										INDEX `fk_campaignpress_client_campaignpress_group` (`group_id` ASC) ,
										CONSTRAINT `fk_campaignpress_client_campaignpress_group`
											FOREIGN KEY (`group_id` )
											REFERENCES `$campaignpress->group_table_name` (`id` )
											ON DELETE NO ACTION
											ON UPDATE NO ACTION) $charset_collate;" );
			}
			
			if ( ! campaignpress_table_exists( $table_name ) ) {
				exit( sprintf( __( 'Could not create table in database (%s). Activation aborted.', 'campaignpress' ), $table_name ) );
			}
			
			// activity table
			$table_name = $campaignpress->activity_table_name;
			if ( ! campaignpress_table_exists( $table_name ) ) {
			
				$wpdb->query( "	CREATE TABLE IF NOT EXISTS $table_name (
										`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
										`description` VARCHAR(255) NOT NULL ,
										`occurred` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
										`item_id` BIGINT(20) UNSIGNED NOT NULL ,
										`item_type` VARCHAR(20) NOT NULL ,
										PRIMARY KEY (`id`) ) $charset_collate;" );
			}
			
			if ( ! campaignpress_table_exists( $table_name ) ) {
				exit( sprintf( __( 'Could not create table in database (%s). Activation aborted.', 'campaignpress' ), $table_name ) );
			}
			
			// list table
			$table_name = $campaignpress->list_table_name;
			if ( ! campaignpress_table_exists( $table_name ) ) {
			
				$wpdb->query( "	CREATE TABLE IF NOT EXISTS $table_name (
										`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
										`api_id` CHAR(32) NOT NULL ,
										`name` VARCHAR(255) NOT NULL ,
										`campaignpress_client_id` BIGINT(20) UNSIGNED NOT NULL ,
										PRIMARY KEY (`id`) ,
										INDEX `fk_campaignpress_list_campaignpress_client1` (`campaignpress_client_id` ASC) ,
										CONSTRAINT `fk_campaignpress_list_campaignpress_client1`
											FOREIGN KEY (`campaignpress_client_id` )
											REFERENCES `$campaignpress->client_table_name` (`id` )
											ON DELETE NO ACTION
											ON UPDATE NO ACTION) $charset_collate;" );
			}
			
			if ( ! campaignpress_table_exists( $table_name ) ) {
				exit( sprintf( __( 'Could not create table in database (%s). Activation aborted.', 'campaignpress' ), $table_name ) );
			}
			
			// field table
			$table_name = $campaignpress->field_table_name;
			if ( ! campaignpress_table_exists( $table_name ) ) {
			
				$wpdb->query( "	CREATE TABLE IF NOT EXISTS $table_name (
										`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
										`name` VARCHAR(255) NOT NULL ,
										`datatype` VARCHAR(45) NOT NULL ,
										`value_options` TEXT NULL ,
										`displayed` TINYINT(1) NOT NULL ,
										`position` TINYINT(3) NOT NULL ,
										`required` TINYINT(1) NOT NULL ,
										`system_field` TINYINT(1) NOT NULL ,
										PRIMARY KEY (`id`) ) $charset_collate;" );
										
				// insert our system fields
				$params = array( 	
										'name' => 'Company Name',
										'datatype' => 'Text',
										'displayed' => 1,
										'position' => 1,
										'required' => 1,
										'system_field' => 1
									);
				
				$wpdb->insert ( $campaignpress->field_table_name, $params );
				$field_id = $wpdb->insert_id;
				$log_message = __( 'Field created via install.', 'campaignpress' );
				campaignpress_log_activity( 'field', $field_id, $log_message );
				
				$params = array( 	
										'name' => 'Contact Name',
										'datatype' => 'Text',
										'displayed' => 1,
										'position' => 2,
										'required' => 1,
										'system_field' => 1
									);
				
				$wpdb->insert ( $campaignpress->field_table_name, $params );
				$field_id = $wpdb->insert_id;
				$log_message = __( 'Field created via install.', 'campaignpress' );
				campaignpress_log_activity( 'field', $field_id, $log_message );
				
				$params = array( 	
										'name' => 'Email',
										'datatype' => 'Email',
										'displayed' => 1,
										'position' => 3,
										'required' => 1,
										'system_field' => 1
									);
				
				$wpdb->insert ( $campaignpress->field_table_name, $params );
				$field_id = $wpdb->insert_id;
				$log_message = __( 'Field created via install.', 'campaignpress' );
				campaignpress_log_activity( 'field', $field_id, $log_message );
				
				$params = array( 	
										'name' => 'Country',
										'datatype' => 'Country',
										'displayed' => 0,
										'position' => 0,
										'required' => 0,
										'system_field' => 1
									);
				
				$wpdb->insert ( $campaignpress->field_table_name, $params );
				$field_id = $wpdb->insert_id;
				$log_message = __( 'Field created via install.', 'campaignpress' );
				campaignpress_log_activity( 'field', $field_id, $log_message );
				
				$params = array( 	
										'name' => 'Timezone',
										'datatype' => 'Timezone',
										'displayed' => 0,
										'position' => 0,
										'required' => 0,
										'system_field' => 1
									);
				
				$wpdb->insert ( $campaignpress->field_table_name, $params );
				$field_id = $wpdb->insert_id;
				$log_message = __( 'Field created via install.', 'campaignpress' );
				campaignpress_log_activity( 'field', $field_id, $log_message );
				
				$params = array( 	
										'name' => 'Additional Info',
										'datatype' => 'Textarea',
										'displayed' => 0,
										'position' => 0,
										'required' => 0,
										'system_field' => 1
									);
				
				$wpdb->insert ( $campaignpress->field_table_name, $params );
				$field_id = $wpdb->insert_id;
				$log_message = __( 'Field created via install.', 'campaignpress' );
				campaignpress_log_activity( 'field', $field_id, $log_message );						
			}
			
			if ( ! campaignpress_table_exists( $table_name ) ) {
				exit( sprintf( __( 'Could not create table in database (%s). Activation aborted.', 'campaignpress' ), $table_name ) );
			}
			
			// list field table
			$table_name = $campaignpress->list_field_table_name;
			if ( ! campaignpress_table_exists( $table_name ) ) {
			
				$wpdb->query( "	CREATE TABLE IF NOT EXISTS $table_name (
										`campaignpress_list_id` BIGINT(20) UNSIGNED NOT NULL ,
										`campaignpress_field_id` BIGINT(20) UNSIGNED NOT NULL ,
										`cm_key` VARCHAR(255) NOT NULL ,
										INDEX `fk_campaignpress_list_field_campaignpress_list1` (`campaignpress_list_id` ASC) ,
										INDEX `fk_campaignpress_list_field_campaignpress_field1` (`campaignpress_field_id` ASC) ,
										CONSTRAINT `fk_campaignpress_list_field_campaignpress_list1`
											FOREIGN KEY (`campaignpress_list_id` )
											REFERENCES `$campaignpress->list_table_name` (`id` )
											ON DELETE NO ACTION
											ON UPDATE NO ACTION,
										CONSTRAINT `fk_campaignpress_list_field_campaignpress_field1`
											FOREIGN KEY (`campaignpress_field_id` )
											REFERENCES `$campaignpress->field_table_name` (`id` )
											ON DELETE NO ACTION
											ON UPDATE NO ACTION) $charset_collate;" );
			}
			
			if ( ! campaignpress_table_exists( $table_name ) ) {
				exit( sprintf( __( 'Could not create table in database (%s). Activation aborted.', 'campaignpress' ), $table_name ) );
			}
			
			// client field value table
			$table_name = $campaignpress->client_field_value_table_name;
			if ( ! campaignpress_table_exists( $table_name ) ) {
			
				$wpdb->query( "	CREATE TABLE IF NOT EXISTS $table_name (
											`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
											`field_value` VARCHAR(255) NOT NULL ,
											`campaignpress_client_id` BIGINT(20) UNSIGNED NOT NULL ,
											`campaignpress_field_id` BIGINT(20) UNSIGNED NOT NULL ,
											PRIMARY KEY (`id`) ,
											INDEX `fk_campaignpress_client_field_value_campaignpress_client1` (`campaignpress_client_id` ASC) ,
											INDEX `fk_campaignpress_client_field_value_campaignpress_field1` (`campaignpress_field_id` ASC) ,
											CONSTRAINT `fk_campaignpress_client_field_value_campaignpress_client1`
												FOREIGN KEY (`campaignpress_client_id` )
												REFERENCES `$campaignpress->client_table_name` (`id` )
												ON DELETE NO ACTION
												ON UPDATE NO ACTION,
											CONSTRAINT `fk_campaignpress_client_field_value_campaignpress_field1`
												FOREIGN KEY (`campaignpress_field_id` )
												REFERENCES `$campaignpress->field_table_name` (`id` )
												ON DELETE NO ACTION
												ON UPDATE NO ACTION) $charset_collate;" );
			}
			
			if ( ! campaignpress_table_exists( $table_name ) ) {
				exit( sprintf( __( 'Could not create table in database (%s). Activation aborted.', 'campaignpress' ), $table_name ) );
			}
			
		}
		
		function campaignpress_uninstall() {
		
			// unset options
			delete_option( 'campaignpress_settings' );
			
			global $wpdb, $campaignpress;
						
			$table_name = $campaignpress->client_field_value_table_name;
			if ( campaignpress_table_exists( $table_name ) ) {
				$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			}

			$table_name = $campaignpress->list_field_table_name;
			if ( campaignpress_table_exists( $table_name ) ) {
				$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			}
			
			$table_name = $campaignpress->field_table_name;
			if ( campaignpress_table_exists( $table_name ) ) {
				$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			}
			
			$table_name = $campaignpress->list_table_name;
			if ( campaignpress_table_exists( $table_name ) ) {
				$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			}
			
			$table_name = $campaignpress->activity_table_name;
			if ( campaignpress_table_exists( $table_name ) ) {
				$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			}
			
			$table_name = $campaignpress->client_table_name;
			if ( campaignpress_table_exists( $table_name ) ) {
				$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			}
			
			$table_name = $campaignpress->group_table_name;
			if ( campaignpress_table_exists( $table_name ) ) {
				$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			}
			
			$table_name = $campaignpress->addon_table_name;
			if ( campaignpress_table_exists( $table_name ) ) {
				$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
			}

		}


?>