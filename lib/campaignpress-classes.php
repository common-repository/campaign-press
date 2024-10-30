<?php

	/**
	* This is where the custom classes used in CampaignPress go.
	*/
	
	class CampaignPressAddon
	{
		private $code;
		private $name;
		private $description;
		private $url;
		private $release_date;
		private $latest_version;
		private $class_name;
		private $dependent_classes;
		private $required_cp_version;
	
		public function __set($var, $val)
		{
			$this->$var = $val;
		}

		public function __get($var)
		{
			return $this->$var;
		}
		
		public function addon_is_installed_html()
		{
			$result = $this->addon_is_installed();
			
			if( ! ( true === $result ) && ! ( false === $result ) ) {
				return $result;
			}
			
			switch( $result )
			{
				case true:
					// installed, get the version
					return $this->installed_version();
					break;
					
				case false:
					// not installed, return blank
					return '';
					break;
			}
		}
		
		public function addon_is_installed()
		{
			if( ! class_exists ( $this->class_name ) ) {
				// definitely not installed
				return false;
			}
		
			// should be installed, check dependencies
			$errors = array();
		   if( isset( $dependent_classes ) ) {
				foreach( $dependent_classes as $dependent_class ) {
					if( ! class_exists( $dependent_class ) ) {
						$errors[] = sprintf( __( 'Missing class %s', 'campaignpress' , $dependent_class ) );
					}
				}
			}
			
			// check version of campaignpress
			$required_version = str_replace( '.', '', $this->required_cp_version );
			$current_version = str_replace( '.', '', CAMPAIGNPRESS_VERSION );
			if( $required_version > $current_version ) {
				$errors[] = sprintf( __( 'Campaign Press version %s required', 'campaignpress'), $this->required_cp_version );
			}
			
			if( count( $errors ) > 0 ) {
				return implode( ', ', $errors );
			}
			
			return true;
		}
		
		public function installed_version() 
		{
			if( $this->addon_is_installed() ) {
				$check_class = $this->class_name;
        // $class::method() only works in PHP >= 5.3.0, which means that the plugin won't install on previous versions - fix this!
        $function_call = array( $check_class, 'version' );
        return call_user_func( $function_call );
			}
			return false;
		}
		
		public function latest_version()
		{
			// in future - grab the latest version through the FM webservice!
			return $this->latest_version;
		}
	}
		
	class CampaignPressClient
	{
		private $id;
		private $api_id;
		private $status;
		private $group_id;
		private $company;
		private $contact_name;
		private $email;
		private $country;
		private $timezone;
		private $username;
		private $temp_password;
		private $additional_info;
		
		public function __set($var, $val)
		{
			$this->$var =  stripslashes_deep( $val );
		}

		public function __get($var)
		{
			return $this->$var;
		}
		
		/**
		 * Validate the data that we've got in this object - tidy it up and throw an exception if we've got problems.
		 */
		public function validate_data() 
		{
			
      // for checking the username is unique
      global $wpdb, $campaignpress;
      
			$name_trim = trim( $this->company );
			if( empty( $name_trim ) ) {
				$errors[] = __( 'Company cannot be empty.' , 'campaignpress' );
			} else {
				if( strlen( $this->company ) > 100 ) {
					$errors[] = __( 'Company cannot be longer than 100 characters.' , 'campaignpress' );
				}
			}

			$this->contact_name = trim( $this->contact_name );
			if( empty( $this->contact_name ) ) {
				$errors[] = __( 'Contact name cannot be empty.' , 'campaignpress' );
			} else {
				if( strlen( $this->contact_name ) > 100 ) {
					$errors[] = __( 'Contact name cannot be longer than 100 characters.' , 'campaignpress' );
				}
			}
			
			$this->email = trim( $this->email );
			if( empty( $this->email ) ) {
				$errors[] = __( 'Email cannot be empty.' , 'campaignpress' );
			} else {
				if( ! is_email( $this->email ) ) {
					$errors[] = __( 'Email is invalid.' , 'campaignpress' );
				}
			}
			
			$countries = campaignpress_get_countries();
			$this->country = trim( $this->country );
			if( empty( $this->country ) ) {
				$errors[] = __( 'Country cannot be empty.' , 'campaignpress' );
			} else {
				if( ! in_array( $this->country, $countries ) ) {
					$errors[] = __( 'Country is invalid.' , 'campaignpress' );
				}
			}
			
			$timezones = campaignpress_get_timezones();
			$this->timezone = trim( $this->timezone );
			if( empty( $this->timezone ) ) {
				$errors[] = __( 'Timezone cannot be empty.' , 'campaignpress' );
			} else {
				if( ! in_array( $this->timezone, $timezones ) ) {
					$errors[] = __( 'Timezone is invalid.' , 'campaignpress' );
				}
			}
			
			$statuses = campaignpress_get_client_statuses();
			$this->status = trim( $this->status );
			if( empty( $this->status ) ) {
				$errors[] = __( 'Status cannot be empty.' , 'campaignpress' );
			} else {
				if( ! in_array( $this->status, $statuses ) ) {
					$errors[] = __( 'Status is invalid.' , 'campaignpress' );
				}
			}
		
			$this->username = strtolower( trim( $this->username ) );
      if( empty( $this->username ) ) {
        $errors[] = __( 'Username cannot be empty.' , 'campaignpress' );
      } else {
        if( preg_match( '/[^_a-z0-9-]/', $this->username ) ) {
          $errors[] = __( 'Username may only contain letters, numbers, underscores &amp; must be lowercase.' , 'campaignpress' );
        } else {
          // username is OK - check that it's not in use by anything else
          $query = $wpdb->prepare( "SELECT id FROM $campaignpress->client_table_name WHERE UPPER(username) = UPPER(%s) LIMIT 1;", $this->username );
          $client_id = $wpdb->get_var( $query );
          if( strlen( $client_id ) > 0 ) {
            // a current client already uses this - check that it's not the current client
            if( ( strlen( $this->id ) > 0 ) && $client_id != $this->id ) {
              // different client - raise error
              $errors[] = sprintf( __( 'Username is already in use by %s.' , 'campaignpress' ), '<a href="' . admin_url( 'admin.php?page=campaignpress-clients&client=' ) . $client_id . '">' . __( 'Another Client', 'campaignpress' ) . '</a>' );
            }
          }
        }
      }
			
			$this->additional_info = trim( $this->additional_info );
			$this->group_id = (int) $this->group_id;
								
			if( count( $errors ) > 0 ) {
				$error_text = '';
				foreach( $errors as $error ) {
					$error_text .= "<li>$error</li>";
				}
				throw new Exception( $error_text );
			}	
		}
		
	}
	
	class CampaignPressField
	{
	
		private $id;
		private $name;
		private $datatype;
		private $displayed;
		private $position;
		private $required;
		private $list;
		private $options;
		private $system_field;
		
		public function __set($var, $val)
		{
			$this->$var = stripslashes_deep( $val );
		}

		public function __get($var)
		{
			return $this->$var;
		}
		
		/**
		 * Generates HTML for the form field
		 */
		public function generate_html( $current_value, $format = 'frontend' ) {
			
			global $disabled;
			
			$current_value = stripslashes_deep( $current_value );
			
			switch( $this->datatype ) 
			{
				case 'Email':
				case 'Number':
				case 'Text':
					if( 'client' == $format ) {
						?>
							<tr valign="top"> 
								<th scope="row"><label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?><?php if( $this->required ) { echo ' *'; } ?></label></th> 
								<td><input name="cp-field-<?php echo $this->id; ?>" type="text" id="cp-field-<?php echo $this->id; ?>" value="<?php echo $current_value; ?>" class="regular-text cp-<?php echo $this->datatype; ?>" <?php echo $disabled; ?> /></td> 
							</tr>
						<?php
					} else {				
						?>
							<div>
								<label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?></label><?php if( $this->required ) { echo ' *'; } ?><br/>
								<input type="text" class="cp-<?php echo $this->datatype; ?>" name="cp-field-<?php echo $this->id; ?>" value="<?php echo $current_value; ?>" />  
							</div>
						<?php
					}
					break;
					
				case 'Checkbox':
					if( 'client' == $format ) {
						?>
							<tr valign="top"> 
								<th scope="row"></th> 
								<td><input name="cp-field-<?php echo $this->id; ?>" type="checkbox" id="cp-field-<?php echo $this->id; ?>" value="1" <?php if( 1 == $current_value ) { echo 'checked="checked"'; } ?> class="cp-<?php echo $this->datatype; ?>" <?php echo $disabled; ?> /> <label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?><?php if( $this->required ) { echo ' *'; } ?></label></td> 
							</tr>
						<?php
					} else {				
						?>
							<div>
								<input type="checkbox" class="cp-<?php echo $this->datatype; ?>" id="cp-field-<?php echo $this->id; ?>" name="cp-field-<?php echo $this->id; ?>" value="1" <?php if( 1 == $current_value ) { echo 'checked="checked"'; } ?>/>
								<label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?><?php if( $this->required ) { echo ' *'; } ?></label>
							</div>
					<?php
					}
					break;
			
				case 'Country':
					$countries = campaignpress_get_countries();
					if( 'client' == $format ) {
						?>
							<tr valign="top"> 
								<th scope="row"><label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?></label></th> 
								<td>
									<select name="cp-field-<?php echo $this->id; ?>" id="cp-field-<?php echo $this->id; ?>" class="cp-<?php echo $this->datatype; ?>" <?php echo $disabled; ?>> 
										<?php foreach( $countries as $country ) { ?>
											<option <?php if( $country == $current_value ) { echo 'selected="selected"'; } ?> value='<?php echo $country; ?>'><?php echo $country; ?></option> 
										<?php } ?>
									</select>
								</td> 
							</tr>
						<?php
					} else {
						?>
							<div>
								<label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?><?php if( $this->required ) { echo ' *'; } ?></label> <br/>
								<select name="cp-field-<?php echo $this->id; ?>" id="cp-field-<?php echo $this->id; ?>" class="cp-<?php echo $this->datatype; ?>"> 
								<?php foreach( $countries as $country ) { ?>
									<option <?php if( $country == $current_value ) { echo 'selected="selected"'; } ?> value='<?php echo $country; ?>'><?php echo $country; ?></option> 
								<?php } ?>
								</select>
							</div>
						<?php
					}
					break;
				
				case 'Timezone':
					$timezones = campaignpress_get_timezones();
					if( 'client' == $format ) {
						?>
							<tr valign="top"> 
								<th scope="row"><label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?></label></th> 
								<td>
									<select name="cp-field-<?php echo $this->id; ?>" id="cp-field-<?php echo $this->id; ?>" class="cp-<?php echo $this->datatype; ?>" <?php echo $disabled; ?>> 
										<?php foreach( $timezones as $timezone ) { ?>
											<option <?php if( $timezone == $current_value ) { echo 'selected="selected"'; } ?> value='<?php echo $timezone; ?>'><?php echo $timezone; ?></option> 
										<?php } ?>
									</select>
								</td> 
							</tr>
						<?php
					} else {
						?>
							<div>
								<label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?><?php if( $this->required ) { echo ' *'; } ?></label> <br/>
								<select name="cp-field-<?php echo $this->id; ?>" id="cp-field-<?php echo $this->id; ?>" class="cp-<?php echo $this->datatype; ?>" <?php echo $disabled; ?>> 
								<?php foreach( $timezones as $timezone ) { ?>
									<option <?php if( $timezone == $current_value ) { echo 'selected="selected"'; } ?> value='<?php echo $timezone; ?>'><?php echo $timezone; ?></option> 
								<?php } ?>
								</select>
							</div>
						<?php
					}
					break;
					
				case 'Textarea':
					if( 'client' == $format ) {
						?>
							<tr valign="top"> 
								<th scope="row"><label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?></label></th> 
								<td>
									<textarea name="cp-field-<?php echo $this->id; ?>" id="cp-field-<?php echo $this->id; ?>" rows="5" cols="50" class="cp-<?php echo $this->datatype; ?>" <?php echo $disabled; ?>><?php echo $current_value; ?></textarea>
								</td> 
							</tr>
						<?php
					} else {
						?>
							<div>
								<label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?><?php if( $this->required ) { echo ' *'; } ?></label> <br/>
								<textarea name="cp-field-<?php echo $this->id; ?>" id="cp-field-<?php echo $this->id; ?>" rows="11" cols="50" class="cp-<?php echo $this->datatype; ?>" ><?php echo $current_value; ?></textarea> 
							</div>
						<?php
					}
					break;
					
				case 'USState':
					$states = campaignpress_get_us_states();
					if( 'client' == $format ) {
						?>
							<tr valign="top"> 
								<th scope="row"><label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?></label></th> 
								<td>
									<select name="cp-field-<?php echo $this->id; ?>" id="cp-field-<?php echo $this->id; ?>" class="cp-<?php echo $this->datatype; ?>" <?php echo $disabled; ?>> 
										<?php foreach( $states as $code => $state ) { ?>
											<option <?php if( $code == $current_value ) { echo 'selected="selected"'; } ?> value='<?php echo $code; ?>'><?php echo $state; ?></option> 
										<?php } ?>
									</select>
								</td> 
							</tr>
						<?php
					} else {
						?>
							<div>
								<label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?><?php if( $this->required ) { echo ' *'; } ?></label> <br/>
								<select name="cp-field-<?php echo $this->id; ?>" id="cp-field-<?php echo $this->id; ?>" class="cp-<?php echo $this->datatype; ?>"> 
								<?php foreach( $states as $code => $state ) { ?>
									<option <?php if( $code == $current_value ) { echo 'selected="selected"'; } ?> value='<?php echo $code; ?>'><?php echo $state; ?></option> 
								<?php } ?>
								</select>
							</div>
						<?php
					}
					break;
					
				case 'MultiSelectOne':
				case 'MultiSelectMany':
					$options = $this->get_options();
					if( ! is_array( $current_value ) ) {
						$current_value = array();
					}
					if( 'client' == $format ) {
						?>
							<tr valign="top"> 
								<th scope="row"><label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?></label></th> 
								<td>
									<select id="cp-field-<?php echo $this->id; ?>" name="cp-field-<?php echo $this->id; ?><?php if( 'MultiSelectMany' == $this->datatype ) { echo '[]'; } ?>" <?php if( 'MultiSelectMany' == $this->datatype ) { echo 'size="3" multiple="yes"'; } ?> class="cp-<?php echo $this->datatype; ?>" <?php echo $disabled; ?>> 
										<?php foreach( $options as $option ) { ?>
											<option <?php if( in_array( $option, $current_value ) ) { echo 'selected="selected"'; } ?> value='<?php echo $option; ?>'><?php echo $option; ?></option> 
										<?php } ?>
									</select>
								</td> 
							</tr>
						<?php
					} else {
						?>
							<div>
								<label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?><?php if( $this->required ) { echo ' *'; } ?></label> <br/>
								<select id="cp-field-<?php echo $this->id; ?>" name="cp-field-<?php echo $this->id; ?><?php if( 'MultiSelectMany' == $this->datatype ) { echo '[]'; } ?>" <?php if( 'MultiSelectMany' == $this->datatype ) { echo 'size="3" multiple="yes"'; } ?> class="cp-<?php echo $this->datatype; ?>"> 
								<?php foreach( $options as $option ) { ?>
									<option <?php if( in_array( $option, $current_value ) ) { echo 'selected="selected"'; } ?> value='<?php echo $option; ?>'><?php echo $option; ?></option> 
								<?php } ?>
								</select>
							</div>
						<?php
					}
					break;
					
				case 'Date':
					$current_value = explode( '-', $current_value);
					for( $i = 0; $i < 3; $i++ ) {
						if( ! isset( $current_value[$i] ) ) {
							$current_value[$i] = '';
						}
					}
					if( 'client' == $format ) {
						?>
							<tr valign="top"> 
								<th scope="row"><label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?></label></th> 
								<td>
									<select id="cp-field-<?php echo $this->id; ?>-month" name="cp-field-<?php echo $this->id; ?>-month" class="cp-<?php echo $this->datatype; ?>" <?php echo $disabled; ?>>
										<option <?php if( '' == $current_value[0] ) { echo 'selected="selected"'; } ?> value=""></option> 
										<option <?php if( 'Jan' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Jan">Jan</option> 
										<option <?php if( 'Feb' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Feb">Feb</option> 
										<option <?php if( 'Mar' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Mar">Mar</option> 
										<option <?php if( 'Apr' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Apr">Apr</option> 
										<option <?php if( 'May' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="May">May</option> 
										<option <?php if( 'Jun' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Jun">Jun</option> 
										<option <?php if( 'Jul' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Jul">Jul</option> 
										<option <?php if( 'Aug' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Aug">Aug</option> 
										<option <?php if( 'Sep' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Sep">Sep</option> 
										<option <?php if( 'Oct' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Oct">Oct</option> 
										<option <?php if( 'Nov' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Nov">Nov</option> 
										<option <?php if( 'Dec' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Dec">Dec</option> 
									</select>
									
									<select id="cp-field-<?php echo $this->id; ?>-day" name="cp-field-<?php echo $this->id; ?>-day" class="cp-<?php echo $this->datatype; ?>" <?php echo $disabled; ?>>
										<option <?php if( '' == $current_value[1] ) { echo 'selected="selected"'; } ?> value=""></option> 
										<?php for( $i = 1; $i < 32; $i++ ) { ?>
											<option <?php if( $i == $current_value[1] ) { echo 'selected="selected"'; } ?> value='<?php echo $i; ?>'><?php echo $i; ?></option> 
										<?php } ?>
									</select>
									
									<select id="cp-field-<?php echo $this->id; ?>-year" name="cp-field-<?php echo $this->id; ?>-year" class="cp-<?php echo $this->datatype; ?>" <?php echo $disabled; ?>>
										<option <?php if( '' == $current_value[2] ) { echo 'selected="selected"'; } ?> value=""></option> 
										<?php for( $i = 1900; $i < 2050; $i++ ) { ?>
											<option <?php if( $i == $current_value[2] ) { echo 'selected="selected"'; } ?> value='<?php echo $i; ?>'><?php echo $i; ?></option> 
										<?php } ?>
									</select>
								</td> 
							</tr>
						<?php
					} else {
						?>
							<div>
								<label for="cp-field-<?php echo $this->id; ?>"><?php echo esc_html( $this->name ); ?><?php if( $this->required ) { echo ' *'; } ?></label> <br/>
								
								<select name="cp-field-<?php echo $this->id; ?>-month" id="cp-field-<?php echo $this->id; ?>-month" class="cp-<?php echo $this->datatype; ?>">
									<option <?php if( '' == $current_value[0] ) { echo 'selected="selected"'; } ?> value=""></option> 
									<option <?php if( 'Jan' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Jan">Jan</option> 
									<option <?php if( 'Feb' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Feb">Feb</option> 
									<option <?php if( 'Mar' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Mar">Mar</option> 
									<option <?php if( 'Apr' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Apr">Apr</option> 
									<option <?php if( 'May' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="May">May</option> 
									<option <?php if( 'Jun' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Jun">Jun</option> 
									<option <?php if( 'Jul' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Jul">Jul</option> 
									<option <?php if( 'Aug' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Aug">Aug</option> 
									<option <?php if( 'Sep' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Sep">Sep</option> 
									<option <?php if( 'Oct' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Oct">Oct</option> 
									<option <?php if( 'Nov' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Nov">Nov</option> 
									<option <?php if( 'Dec' == $current_value[0] ) { echo 'selected="selected"'; } ?> value="Dec">Dec</option> 
								</select>
								
								<select name="cp-field-<?php echo $this->id; ?>-day" id="cp-field-<?php echo $this->id; ?>-day" class="cp-<?php echo $this->datatype; ?>">
									<option <?php if( '' == $current_value[1] ) { echo 'selected="selected"'; } ?> value=""></option> 
									<?php for( $i = 1; $i < 32; $i++ ) { ?>
										<option <?php if( $i == $current_value[1] ) { echo 'selected="selected"'; } ?> value='<?php echo $i; ?>'><?php echo $i; ?></option> 
									<?php } ?>
								</select>
								
								<select name="cp-field-<?php echo $this->id; ?>-year" id="cp-field-<?php echo $this->id; ?>-year" class="cp-<?php echo $this->datatype; ?>">
									<option <?php if( '' == $current_value[2] ) { echo 'selected="selected"'; } ?> value=""></option> 
									<?php for( $i = 1900; $i < 2050; $i++ ) { ?>
										<option <?php if( $i == $current_value[2] ) { echo 'selected="selected"'; } ?> value='<?php echo $i; ?>'><?php echo $i; ?></option> 
									<?php } ?>
								</select>
							</div>
						<?php
					}
					break;

				default:
					?>
						Undefined datatype :-)
					<?php
					break;
			}
		}
		
		public function add_option( $option )
		{
			$this->options[] = $option;
		}
		
		public function get_options()
		{
			if( ! is_array( $this->options ) ) {
				$this->options = array();
			}
			return $this->options;
		}
		
		public function get_options_string()
		{
			if( ! is_array( $this->options ) ) {
				return '';
			}
			return implode( '||', $this->options );
		}
		
		/** 
		 * Validate the actual value that's in this field
		 */
		public function validate_value( $post_vars )
		{
		
			// we use these later
			global $wpdb, $campaignpress;
		
			// we validate against these values for some fields
			$countries = campaignpress_get_countries();
			$timezones = campaignpress_get_timezones();
			$us_states = campaignpress_get_us_states();
			
		
			// key we use to access our value in the post vars
			$key = "cp-field-$this->id";
				
			// special case for dates
			if( 'Date' == $this->datatype ) {
				if( strlen( $post_vars[ $key . '-month' ] ) > 0 && strlen( $post_vars[ $key . '-day' ] ) > 0 && strlen( $post_vars[ $key . '-year' ] ) > 0 ) {
					$post_vars[$key] = $post_vars[ $key . '-month' ] . '-' . $post_vars[ $key . '-day' ] . '-' . $post_vars[ $key . '-year' ];
				}
			}
			
			// and for checkboxes
			if( 'Checkbox' == $this->datatype ) {
				$post_vars[$key] = (int)$post_vars[$key];
			}
			
			$post_vars[$key] = stripslashes_deep( $post_vars[$key] );
			$val = $post_vars[$key];
			
			// first check for no value at all 
			if( $this->required ) {
				$required_error = false;
				if( is_array( $val ) ) { 
					if( count( $val ) < 1 ) {
						$required_error = true;
					}
				} else {
					if( strlen( $val ) < 1 ) {
						$required_error = true;
					}
				}
				if( true == $required_error ) {
					throw new Exception( $this->name . ' ' . __( 'is a required field', 'campaignpress' ) );
				}
			}
			
			$len = 0;
			if( is_array( $val ) ) {
				$len = count( $val );
			} else {
				$len = strlen( $val );
			}
			
			// now we validate if we've got a value (and scrub the values!)
			if( $len > 0 ) {
			
				// special case for email which is required - make sure it's not already in our DB
				if( 3 == $this-> id ) {
					$query = $wpdb->prepare( "	SELECT id FROM $campaignpress->client_table_name WHERE email = %s AND status != %s;", $val, 'Inactive' );
					$already_exists = $wpdb->get_var( $query );
					if( $already_exists ) {
						throw new Exception( $this->name . ' ' . __( 'contains an email address that already exists, please choose another', 'campaignpress' ) );
					}
				}
							
				switch( $this->datatype )
				{
					case 'Number':
						if( ! is_numeric( $val ) ) {
							throw new Exception( $this->name . ' ' . __( 'contains an invalid number', 'campaignpress' ) );
						}
						break;
						
					case 'Email':
						if( ! is_email( $val ) ) {
							throw new Exception( $this->name . ' ' . __( 'contains an invalid email address', 'campaignpress' ) );
						}
						break;
						
					case 'Country':
						if( ! in_array( $val, $countries ) ) {
							throw new Exception( $this->name . ' ' . __( 'contains an invalid country', 'campaignpress' ) );
						}
						break;	
						
					case 'Timezone':
						if( ! in_array( $val, $timezones ) ) {
							throw new Exception( $this->name . ' ' . __( 'contains an invalid timezone', 'campaignpress' ) );
						}
						break;	
						
					case 'USState':
						if( ! array_key_exists( $val, $us_states ) ) {
							throw new Exception( $this->name . ' ' . sprintf( __( 'contains an invalid US state (%s)', 'campaignpress' ), $val ) );
						}
						break;

					case 'Text':
						// make sure we're not too long
						if( strlen( $val ) > 255 ) {
							throw new Exception( $this->name . ' ' . __( 'is too long (must be under 255 characters)', 'campaignpress' ) );
						}
						break;
					
					case 'MultiSelectOne':
						if( ! in_array( $val, $this->options ) ) {
							throw new Exception( $this->name . ' ' . sprintf( __( 'is set to an invalid option (%s)', 'campaignpress' ), $val ) );
						}
						break;
					
					case 'MultiSelectMany':
						// first we validate, then we need to make this into a string
						if( ! is_array( $val ) ) {
							$val = array( $val );
						}
						foreach( $val as &$item ) {
							if( 'null' == $item ) { 
								$item = '';
								continue;
							}
							if( ! in_array( $item, $this->options ) ) {
								throw new Exception( $this->name . ' ' . sprintf( __( 'contains an invalid option (%s)', 'campaignpress' ), $item ) );
							}
						}
						$post_vars[$key] = implode( '||', $val );
						break;
						
					case 'Date':
						// validate the date
						if( strlen( $val ) > 0 ) {
						
							$split_date = explode( '-', $val );
							
							$month_name = substr( $split_date[0], 0, 3 );
							
							$month_number = 0;
							
							if( strlen( $split_date[1] > 2 ) ) {
								$day = substr( $split_date[1], 0, 2 ); 
							} else {
								$day = $split_date[1];
							}
							
							if( strlen( $split_date[2] > 4 ) ) {
								$year = substr( $split_date[2], 0, 4 ); 
							} else {
								$year = $split_date[2];
							}

							for( $i = 1; $i <= 12 ; $i++ ) {  
								 if( strtolower( date( "M", mktime( 0, 0, 0, $i, 1, 0 ) ) ) == strtolower( $month_name ) ) {  
									  $month_number = $i;
									  break;
								 }  
							}
							
							$day = (int) $day;
							$year = (int) $year;
							$month_number = (int) $month_number;
							
							// now we validate it
							if( ! checkdate ( $month_number, $day, $year ) ) {
								throw new Exception( $this->name . ' ' . __( 'contains an invalid date', 'campaignpress' ) );
							}
						}
						
						break;
					
					case 'Textarea':
						// nothing they put in here will break anything, so all G
						break;
				}
			}
			
			// return the scrubbed values
			return $post_vars;
		}
		
		/**
		 * Validate the data that we've got in this object - tidy it up and throw an exception if we've got problems.
		 */
		public function validate_data() 
		{
			$this->name = trim( $this->name );
			if( empty( $this->name ) ) {
				$errors[] = __( 'Name cannot be empty.' , 'campaignpress' );
			} else {
				if( strlen( $this->name ) > 100 ) {
					$errors[] = __( 'Name cannot be longer than 100 characters.' , 'campaignpress' );
				}
			}	

			$this->datatype = trim( $this->datatype );
			if( empty( $this->datatype ) ) {
				$errors[] = __( 'Datatype cannot be empty.' , 'campaignpress' );
			} else {
				switch( $this->datatype ) 
				{
					case 'Text':
						break;
					
					case 'Textarea':
						break;
						
					case 'Number':
						break;
						
					case 'Checkbox':
						break;
					
					case 'Email':
						break;
						
					case 'Country':
						break;
						
					case 'Timezone':
						break;
						
					case 'Date':
						break;
						
					case 'MultiSelectOne':
						break;
					
					case 'MultiSelectMany':
						break;
						
					case 'USState':
						break;
						
					default:	
						$errors[] = __( 'Invalid Data Type.' , 'campaignpress' );
						break;
				}
			}
			
			$this->position = (int) trim( $this->required );
			
			$this->position = (int) trim( $this->position );
			if( $this->position > 999 ) {
				$errors[] = __( 'Invalid field position.' , 'campaignpress' );
			}
														
			if( count( $errors ) > 0 ) {
				$error_text = '';
				foreach( $errors as $error ) {
					$error_text .= "<li>$error</li>";
				}
				throw new Exception( $error_text );
			}
		}
		
	}

	class CampaignPressGroup
   {
	
		private $id;
		private $name;
		private $desc;
		private $access_reports;
		private $access_sub_mgmt;
		private $access_sub_imports;
		private $access_camp_create_send;
		private $access_camp_import;
		private $access_spam;
		private $access_level;
		private $billing_type;
		private $billing_currency;
		private $billing_delivery_fee;
		private $billing_recipient_fee;
		private $billing_spamtest_fee;
		
		private $updated;
		
		// list of clients that belong to this group
		private $clients;

		public function __set($var, $val)
		{
			$this->$var = stripslashes_deep( $val );
			
			if( 'access_level' == $var ) {
				$this->set_access();
			}
		}

		public function __get($var)
		{
			return $this->$var;
		}
		
		public function addClient( $client )
		{
			$this->clients[] = $client;
		}
		
		private function set_access() 
		{
			/*
			
				Weights of bits:
			
				$access_reports = 1
				$access_sub_mgmt = 2
				$access_sub_imports = 16
				$access_camp_create_send = 4
				$access_camp_import = 32
				$access_spam = 8

			*/
			
			// left padded with zeros
			$bits = sprintf( '%06s', trim( decbin( $this->access_level ) ) );
			
			$this->access_reports = 1 * substr( $bits, -1, 1 );
			$this->access_sub_mgmt = 2 * substr( $bits, -2, 1 );
			$this->access_camp_create_send = 4 * substr( $bits, -3, 1 );
			$this->access_spam = 8 * substr( $bits, -4, 1 );
			$this->access_sub_imports = 16 * substr( $bits, -5, 1 );
			$this->access_camp_import = 32 * substr( $bits, -6, 1 );
			
		}
		
		/**
		 * Validate the data that we've got in this object - tidy it up and throw an exception if we've got problems.
		 */
		public function validate_data() 
		{
    
      $errors = array();

			$this->access_sub_imports = (int) $this->access_sub_imports;
			$this->access_sub_mgmt = (int) $this->access_sub_mgmt;
			$this->access_camp_create_send = (int) $this->access_camp_create_send;
			$this->access_camp_import = (int) $this->access_camp_import;
			$this->access_reports = (int) $this->access_reports;
			$this->access_spam = (int) $this->access_spam;
		
			$this->name = trim( $this->name );
			if( empty( $this->name ) ) {
				$errors[] = __( 'Name cannot be empty.' , 'campaignpress' );
			} else {
				if( strlen( $this->name ) > 100 ) {
					$errors[] = __( 'Name cannot be longer than 100 characters.' , 'campaignpress' );
				}
			}	

			$this->desc = trim( $this->desc );
			if( empty( $this->desc ) ) {
				$errors[] = __( 'Description cannot be empty.' , 'campaignpress' );
			}

			if( ( $this->access_sub_imports > 0 ) && ( 0 == $this->access_sub_mgmt  ) ) {
				$errors[] = __( 'Cannot grant subscriber import access without subscriber management access.' , 'campaignpress' );
			}
			
			if( ( $this->access_camp_import > 0 ) && ( 0 == $this->access_camp_create_send  ) ) {
				$errors[] = __( 'Cannot grant campaign import access without campaign create &amp; send access.' , 'campaignpress' );
			}
			
			if( 'ClientPaysAtStandardRate' == $this->billing_type &&	empty( $this->billing_currency )	) {
				$errors[] = __( 'Currency cannot be empty if client pays at the standard rate.' , 'campaignpress' );
			} else {
				
				if( 'ClientPaysWithMarkup' == $this->billing_type ) {
				
					// list of currencies we're going to need for the page	
					$currencies = campaignpress_get_billing_currencies();
				
					// format the numbers
					$this->billing_delivery_fee = number_format( (float) $this->billing_delivery_fee, 2, '.', '' );
					$this->billing_recipient_fee = number_format( (float) $this->billing_recipient_fee, 2, '.', '' );
					
					if( $this->billing_delivery_fee <= $currencies[$this->billing_currency]['delivery'] ) {
						$errors[] = __( 'Delivery fee must be more than the base rate.' , 'campaignpress' );
					}
					if( $this->billing_recipient_fee <= $currencies[$this->billing_currency]['recipient'] ) {
						$errors[] = __( 'Recipient fee must be more than the base rate.' , 'campaignpress' );
					}
					
					if( $this->access_spam > 0 ) {
						$this->billing_spamtest_fee = number_format( (float) $this->billing_spamtest_fee, 2, '.', '' );
						if( $this->billing_spamtest_fee <= $currencies[$this->billing_currency]['spamtest']) {
							$errors[] = __( 'Design &amp; Spam Test fee must be more than the base rate.' , 'campaignpress' );
						}
					}
				}
				
				// form the access level
				$this->access_level = (int) 	$this->access_reports +
														$this->access_sub_mgmt + 
														$this->access_sub_imports +
														$this->access_camp_create_send +
														$this->access_camp_import +
														$this->access_spam;
														
				if( count( $errors ) > 0 ) {
					$error_text = '';
					foreach( $errors as $error ) {
						$error_text .= "<li>$error</li>";
					}
					throw new Exception( $error_text );
				}
			}
		}
		
		/**
		 * Compare most of the details except for name and description 
		 */
		public function basicDetailsMatch( $compare_group )
		{
			if( $this->access_level != $compare_group->access_level )
				return false;
				
			if( $this->billing_type != $compare_group->billing_type )
				return false;

			if( $this->billing_currency != $compare_group->billing_currency )
				return false;

			if( (float) ( $this->billing_delivery_fee + 0 ) != (float) ( $compare_group->billing_delivery_fee + 0 ) )
				return false;
				
			if( (float) ( $this->billing_recipient_fee + 0 ) != (float) ( $compare_group->billing_recipient_fee + 0 ) )
				return false;
				
			if( (float) ( $this->billing_spamtest_fee + 0 ) != (float) ( $compare_group->billing_spamtest_fee + 0 ) )
				return false;
				
			return true;
		}

	}

?>