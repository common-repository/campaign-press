<?php

	/**
	* These functions help with the frontend stuff.
	*/
	
	/**
	* Styles
	*/
	
		// hooks
		add_action( 'wp_print_styles', 'campaignpress_enqueue_styles' );
		
		function campaignpress_enqueue_styles() {
						
			wp_enqueue_style( 'campaignpress-frontend', CAMPAIGNPRESS_PLUGIN_URL . '/css/styles-frontend.css', array(), CAMPAIGNPRESS_VERSION, 'all' );
		}
	
	/**
	* Sign Up form processing
	*/
		
		// hooks
		add_action( 'init', 'campaignpress_init', 11 );

		// check for post
		function campaignpress_init() {
		
			// if we've got problems then don't process a thing
			if( campaignpress_config_has_errors() ) {
				return;
			}

			if ( isset( $_POST['_campaignpress_signup'] ) ) {
			
				try
				{
					$_POST = campaignpress_process_sign_up( $_POST );
				}
				catch( Exception $e )
				{
					$_POST['_campaignpress_signup_errors'] = $e->getMessage();
				}
			}
		}
		
		// make sure no one's entered any funny stuff into our fields
		function campaignpress_process_sign_up( $post_vars ) {
			
			global $wpdb, $campaignpress, $campaignpress_settings;
			
			$errors = Array();
			
			// loop through each of our fields
			$query = $wpdb->prepare( "SELECT * FROM $campaignpress->field_table_name WHERE displayed = %d OR ( system_field = %d AND required = %d ) ORDER BY position ASC;", 1, 1, 1 );
			$all_fields = $wpdb->get_results( $query );
			
			foreach( $all_fields as $field_db ) {
			
				$field = new CampaignPressField();
				$field->id = $field_db->id;
				$field->name = $field_db->name;
				$field->datatype = $field_db->datatype;
				$field->position = $field_db->position;
				$field->system_field = $field_db->system_field;
				$field->required = $field_db->required;
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
					$post_vars = $field->validate_value( $post_vars );
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
			
			// if we're here it means ALL GOOD! :D Now create the client in the DB
			// to do this we create a Client object and validate it, which may seem kinda funny
			// but we've got some different rules - and the ABOVE validation was required for custom fields anyway
			// create a client object out of our details
			$client = new CampaignPressClient();
			
			// we always know that certain post vars are going to be certain things...
			$company = $post_vars['cp-field-1'];
			$contact_name = $post_vars['cp-field-2'];
			$email = $post_vars['cp-field-3'];
			$country = $post_vars['cp-field-4'];
			if( strlen( $country ) < 1 ) {
				$country = $campaignpress_settings['default_country'];
			}
			$timezone = $post_vars['cp-field-5'];
			if( strlen( $timezone ) < 1 ) {
				$timezone = $campaignpress_settings['default_timezone'];
			}
			$additional_info = $post_vars['cp-field-6'];
			
			// populate it
			$client->status = 'Awaiting Approval';
			$client->company = $company;
			$client->contact_name = $contact_name;
			$client->email = $email;
			$client->group_id = $campaignpress_settings['approval_group'];
			$client->country = $country;
			$client->timezone = $timezone;
			$client->additional_info = $additional_info;
      
      // create a username from the company name
      $username = strtolower( str_replace( ' ', '_', $client->company ) );
      $client->username = preg_replace('/[^_A-Za-z0-9-]/', '', $username);
      
      // now we need to ensure that this is unique
      $valid_username = false;
      $i = 1;
      while( ! $valid_username ) {
        $i++;
        $query = $wpdb->prepare( "SELECT id FROM $campaignpress->client_table_name WHERE UPPER(username) = UPPER(%s) LIMIT 1;", $client->username );
        $client_id = $wpdb->get_var( $query );
        if( strlen( $client_id ) > 0 ) {
          // a current client already has this username, add something to the end
          $client->username = $client->username . $i;
        } else {
          // we're good to go!
          $valid_username = true;
        }
      }

			// validate (will tidy up the data and throw exception with errors if we've got issues)
			$client->validate_data();

			// we're good to go soldier
			$params = array( 	
									'created' => date('Y-m-d H:i:s'),
									'status' => $client->status,
									'company' => $client->company,
									'contact_name' => $client->contact_name,
									'email' => $client->email,
                  'username' => $client->username,
									'country' => $client->country,
									'timezone' => $client->timezone,
									'group_id' => $client->group_id,
									'additional_info' => $client->additional_info
								);
			

			// insert into the database
			$wpdb->insert ( $campaignpress->client_table_name, $params );
			$client->id = $wpdb->insert_id;
			$log_message = __( 'Client signed up via the site.', 'campaignpress' );
			
			// now insert the custom fields goodies
			foreach( $all_fields as $field ) {
			
				// create an array of fields we don't want to process
				$done_fields = array( '1', '2', '3', '4', '5', '6' );
				if( in_array( $field->id, $done_fields ) ) {
					// don't worry about this guy
					continue;
				}
			
				$val = $post_vars["cp-field-$field->id"];
				
				// now we do the inserts
				$params = array( 	
									'field_value' => $val,
									'campaignpress_client_id' => $client->id,
									'campaignpress_field_id' => $field->id
								);

				// insert into the database
				$wpdb->insert( $campaignpress->client_field_value_table_name, $params );
			
			}
		
			// log activity
			campaignpress_log_activity( 'client', $client->id, $log_message );
			
			// notify the admin about the sign up if required
			if( $campaignpress_settings['an_on_signup_email'] ) {
				campaignpress_notify_admin( $client, 'client-signup' );
			}
			
			// notify the client about the sign up if required
			if( $campaignpress_settings['cn_on_signup_email'] ) {
				campaignpress_notify_client( $client, 'signup' );
			}
			
			// all good
			$post_vars['_campaignpress_signup_successful'] = 1;
			
			return $post_vars;
		}
		
		
	/**
	* Sign Up form display
	*/
		// hooks
		add_shortcode( 'campaignpress-signup-form', 'campaignpress_generate_signup_form' );

		// generate sign up form code
		function campaignpress_generate_signup_form() {
		
			if( campaignpress_config_has_errors() ) {
				_e( 'Problem with Campaign Press configuration.', 'campaignpress' );
				return;
			}
			
			global $wpdb, $campaignpress, $campaignpress_settings;
		
			// check for a custom class
			$custom_class = '';
			if( ! empty( $campaignpress_settings['form_class'] ) ) {
				$custom_class = ' class="' . $campaignpress_settings['form_class'] . '"';
			}
			
			if( isset( $_POST['_campaignpress_signup_successful'] ) ) {
				$success_message = __( 'Sign up successful!', 'campaignpress' );
				if( strlen( $campaignpress_settings['form_success_text'] ) > 0 ) {
					$success_message = $campaignpress_settings['form_success_text'];
				}
				?>
				<div<?php echo $custom_class; ?>>
					<div id="cp-form-success">
						<?php echo $success_message; ?>
					</div>
				</div>				
				
				<?php
				return;
			}

			// grab a list of our fields
			$query = $wpdb->prepare( "	SELECT * FROM $campaignpress->field_table_name WHERE displayed = %d ORDER BY position ASC;", 1 );
			$all_fields = $wpdb->get_results( $query );
			$all_fields_count = count( $custom_fields );
			
			// form our hidden & displayed fields
			$displayed_fields = Array();
			foreach( $all_fields as $field ) {
				$cf = new CampaignPressField();
				$cf->id = $field->id;
				$cf->name = $field->name;
				$cf->datatype = $field->datatype;
				$cf->position = $field->position;
				$cf->system_field = $field->system_field;
				$cf->required = $field->required;
				// add the options
				$options = explode( '||', $field->value_options );
				foreach( $options as $option ) {
					$option = trim( $option );
					if( strlen( $option ) > 0 ) {
						$cf->add_option( $option );
					}
				}
				$displayed_fields[] = $cf;
			}
						
			$form_title = $campaignpress_settings['form_title'];
			if( empty( $form_title ) ) {
				$form_title = 'Sign Up';
			}
			$submit_text = $campaignpress_settings['form_submit_text'];
			if( empty( $submit_text ) ) {
				$submit_text = 'Sign Up';
			}
		
			// code to display our form				
			?>
			
			<div<?php echo $custom_class; ?>>
			
				<div id="cp-form-before-message">
					<?php
						if( strlen( $campaignpress_settings['form_before_text'] ) > 0 ) {
							echo $campaignpress_settings['form_before_text'];
						}
					?>
				</div>
						
				<form action="<?php echo add_query_arg( array() ); ?>" method="post"> 
					<input type='hidden' name='_campaignpress_signup' value='1' />
					<fieldset>
						
					<?php
					if( isset( $_POST['_campaignpress_signup_errors'] ) ) {
						echo '<div id="cp-form-errors"><strong>';
							_e( 'Please fix these errors and try again', 'campaignpress');
							echo '</strong><ul>';
							echo $_POST['_campaignpress_signup_errors'];
						echo '</ul></div>';
					}
					?>
				
					<legend><?php echo $form_title; ?></legend>
						<?php foreach( $displayed_fields as $field) { 
									$current_value = '';
									if ( isset( $_POST["cp-field-$field->id"] ) ) {
										$current_value = $_POST["cp-field-$field->id"];
									} else if( isset( $_POST["cp-field-$field->id-year"] ) ) {
										$current_value = $_POST["cp-field-$field->id-month"] . '-' . $_POST["cp-field-$field->id-day"] . '-' . $_POST["cp-field-$field->id-year"];									} else {
										// if we have a default value then use that instead
										if( 4 == $field->id ) {
											$current_value = $campaignpress_settings['default_country'];
										}
										if( 5 == $field->id ) {
											$current_value = $campaignpress_settings['default_timezone'];
										}
									}
									$field->generate_html( $current_value );
								} ?>
					</fieldset>
					<input name="Submit" type="submit" class="button-primary" value="<?php echo $submit_text; ?>" />
				</form>
			</div>
			
			<?php

		}

?>