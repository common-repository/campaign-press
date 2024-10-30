<?php

	/**
	* These functions are commonly used throughout Campaign Press and thus reside here :-)
	*/
	
	/**
	* Logs activity in the activity table
	*
	* @param string $item_type the type of item we're logging for - e.g. group, field etc.
	* @param int $item_id the id of the item we're logging for.
	* @param string $activity_description a description of what just happened (reason for log).
	*/
	function campaignpress_log_activity( $item_type, $item_id, $activity_description ) {
		
		global $wpdb, $campaignpress;
		
		// at this point everything's OK - insert or update!
		$params = array(
								'description' => $activity_description,
								'item_id' => $item_id,
								'item_type' => $item_type
							);
			
		$wpdb->insert ( $campaignpress->activity_table_name, $params );
		
	}
		

	/**
	* Checks if a given table exists in the WP database.
	*
	* @param string $table the name of the table we're checking for.
	* @return string the name of the table that was found that matches the given table name.
	*/
	function campaignpress_table_exists( $table ) {
	
		global $wpdb;
		
		if ( null == $table || strlen($table) == 0 )
			return false;

		return strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) ) == strtolower( $table );
	}
	
	/**
	* Returns a list of currencies supported by CM & their major and minor units.
	*
	* @return mixed an array of currency objects.
	*/
	function campaignpress_get_billing_currencies() {
		
		$currencies['USD'] = array	( 
													'code' 		=> 'USD',
													'name'		=> 'US Dollars',
													'major'		=> '&#36;',
													'minor'		=> 'cents',
													'delivery'	=> '5.00',
													'recipient'	=> '1.00',
													'spamtest'	=> '5.00'
												);
														
		$currencies['GBP'] = array	( 
													'code' 		=> 'GBP',
													'name'		=> 'Great British Pounds',
													'major'		=> '&#163;',
													'minor'		=> 'pence',
													'delivery'	=> '3.00',
													'recipient'	=> '0.60',
													'spamtest'	=> '3.00'
												);

		$currencies['EUR'] = array	( 
													'code' 		=> 'EUR',
													'name'		=> 'Euro',
													'major'		=> '&#8364;',
													'minor'		=> 'cents',
													'delivery'	=> '4.00',
													'recipient'	=> '0.80',
													'spamtest'	=> '4.00'
												);
														
		$currencies['CAD'] = array	( 
													'code' 		=> 'CAD',
													'name'		=> 'Canadian Dollars',
													'major'		=> '&#36;',
													'minor'		=> 'cents',
													'delivery'	=> '5.00',
													'recipient'	=> '1.10',
													'spamtest'	=> '5.00'
												);
														
		$currencies['AUD'] = array	( 
													'code' 		=> 'AUD',
													'name'		=> 'Australian Dollars',
													'major'		=> '&#36;',
													'minor'		=> 'cents',
													'delivery'	=> '6.00',
													'recipient'	=> '1.30',
													'spamtest'	=> '6.00'
												);

		$currencies['NZD'] = array	( 
													'code' 		=> 'NZD',
													'name'		=> 'New Zealand Dollars',
													'major'		=> '&#36;',
													'minor'		=> 'cents',
													'delivery'	=> '7.00',
													'recipient'	=> '1.50',
													'spamtest'	=> '7.00'
												);
														
		return $currencies;
	}

	/**
	* Returns a list of US States supported by CM.
	*
	* @return mixed an array of states in string format.
	*/
	function campaignpress_get_us_states() {
		
		$states = array(
								'653185' => 'Alabama',
								'653186' => 'Alaska',
								'653187' => 'Arizona',
								'653188' => 'Arkansas',
								'653189' => 'California',
								'653190' => 'Colorado',
								'653191' => 'Connecticut',
								'653192' => 'Delaware',
								'653193' => 'District of Columbia',
								'653194' => 'Florida',
								'653195' => 'Georgia',
								'653196' => 'Hawaii',
								'653197' => 'Idaho',
								'653198' => 'Illinois',
								'653199' => 'Indiana',
								'653200' => 'Iowa',
								'653201' => 'Kansas',
								'653202' => 'Kentucky',
								'653203' => 'Louisiana',
								'653204' => 'Maine',
								'653205' => 'Maryland',
								'653206' => 'Massachusetts',
								'653207' => 'Michigan',
								'653208' => 'Minnesota',
								'653209' => 'Mississippi',
								'653210' => 'Missouri',
								'653211' => 'Montana',
								'653212' => 'Nebraska',
								'653213' => 'Nevada',
								'653214' => 'New Hampshire',
								'653215' => 'New Jersey',
								'653216' => 'New Mexico',
								'653217' => 'New York',
								'653218' => 'North Carolina',
								'653219' => 'North Dakota',
								'653220' => 'Ohio',
								'653221' => 'Oklahoma',
								'653222' => 'Oregon',
								'653223' => 'Pennsylvania',
								'653224' => 'Rhode Island',
								'653225' => 'South Carolina',
								'653226' => 'South Dakota',
								'653227' => 'Tennessee',
								'653228' => 'Texas',
								'653229' => 'Utah',
								'653230' => 'Vermont',
								'653231' => 'Virginia',
								'653232' => 'Washington',
								'653233' => 'West Virginia',
								'653234' => 'Wisconsin',
								'653235' => 'Wyoming'
							);
		return $states;
	}
	
	/**
	* Returns a list of timezones supported by CM.
	*
	* @return mixed an array of timezones in string format.
	*/
	function campaignpress_get_timezones() {
		$timezones = array(
									"(GMT) Casablanca", 
									"(GMT) Coordinated Universal Time", 
									"(GMT) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London", 
									"(GMT) Monrovia, Reykjavik", 
									"(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna", 
									"(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague", 
									"(GMT+01:00) Brussels, Copenhagen, Madrid, Paris", 
									"(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb", 
									"(GMT+01:00) West Central Africa", 
									"(GMT+02:00) Amman", 
									"(GMT+02:00) Athens, Bucharest, Istanbul", 
									"(GMT+02:00) Beirut", 
									"(GMT+02:00) Cairo", 
									"(GMT+02:00) Harare, Pretoria", 
									"(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius", 
									"(GMT+02:00) Jerusalem", 
									"(GMT+02:00) Minsk", 
									"(GMT+02:00) Windhoek", 
									"(GMT+03:00) Baghdad", 
									"(GMT+03:00) Kuwait, Riyadh", 
									"(GMT+03:00) Moscow, St. Petersburg, Volgograd", 
									"(GMT+03:00) Nairobi", 
									"(GMT+03:30) Tehran", 
									"(GMT+04:00) Abu Dhabi, Muscat", 
									"(GMT+04:00) Baku", 
									"(GMT+04:00) Caucasus Standard Time", 
									"(GMT+04:00) Port Louis", 
									"(GMT+04:00) Tbilisi", 
									"(GMT+04:00) Yerevan", 
									"(GMT+04:30) Kabul", 
									"(GMT+05:00) Ekaterinburg", 
									"(GMT+05:00) Islamabad, Karachi", 
									"(GMT+05:00) Tashkent", 
									"(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi", 
									"(GMT+05:30) Sri Jayawardenepura", 
									"(GMT+05:45) Kathmandu", 
									"(GMT+06:00) Astana", 
									"(GMT+06:00) Dhaka", 
									"(GMT+06:00) Novosibirsk", 
									"(GMT+06:30) Yangon (Rangoon)", 
									"(GMT+07:00) Bangkok, Hanoi, Jakarta", 
									"(GMT+07:00) Krasnoyarsk", 
									"(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi", 
									"(GMT+08:00) Irkutsk", 
									"(GMT+08:00) Kuala Lumpur, Singapore", 
									"(GMT+08:00) Perth", 
									"(GMT+08:00) Taipei", 
									"(GMT+08:00) Ulaanbaatar", 
									"(GMT+09:00) Osaka, Sapporo, Tokyo", 
									"(GMT+09:00) Seoul", 
									"(GMT+09:00) Yakutsk", 
									"(GMT+09:30) Adelaide", 
									"(GMT+09:30) Darwin", 
									"(GMT+10:00) Brisbane", 
									"(GMT+10:00) Canberra, Melbourne, Sydney", 
									"(GMT+10:00) Guam, Port Moresby", 
									"(GMT+10:00) Hobart", 
									"(GMT+10:00) Vladivostok", 
									"(GMT+11:00) Magadan, Solomon Is., New Caledonia", 
									"(GMT+12:00) Auckland, Wellington", 
									"(GMT+12:00) Coordinated Universal Time+12", 
									"(GMT+12:00) Fiji", 
									"(GMT+12:00) Petropavlovsk-Kamchatsky", 
									"(GMT+13:00) Nuku'alofa", 
									"(GMT-01:00) Azores", 
									"(GMT-01:00) Cape Verde Is.", 
									"(GMT-02:00) Coordinated Universal Time-02", 
									"(GMT-02:00) Mid-Atlantic", 
									"(GMT-03:00) Brasilia", 
									"(GMT-03:00) Buenos Aires", 
									"(GMT-03:00) Cayenne, Fortaleza", 
									"(GMT-03:00) Greenland", 
									"(GMT-03:00) Montevideo", 
									"(GMT-03:30) Newfoundland", 
									"(GMT-04:00) Asuncion", 
									"(GMT-04:00) Atlantic Time (Canada)", 
									"(GMT-04:00) Cuiaba", 
									"(GMT-04:00) Georgetown, La Paz, Manaus, San Juan", 
									"(GMT-04:00) Santiago", 
									"(GMT-04:30) Caracas", 
									"(GMT-05:00) Bogota, Lima, Quito", 
									"(GMT-05:00) Eastern Time (US & Canada)", 
									"(GMT-05:00) Indiana (East)", 
									"(GMT-06:00) Central America", 
									"(GMT-06:00) Central Time (US & Canada)", 
									"(GMT-06:00) Guadalajara, Mexico City, Monterrey - New", 
									"(GMT-06:00) Guadalajara, Mexico City, Monterrey - Old", 
									"(GMT-06:00) Saskatchewan", 
									"(GMT-07:00) Arizona", 
									"(GMT-07:00) Chihuahua, La Paz, Mazatlan - New", 
									"(GMT-07:00) Chihuahua, La Paz, Mazatlan - Old", 
									"(GMT-07:00) Mountain Time (US & Canada)", 
									"(GMT-08:00) Baja California", 
									"(GMT-08:00) Pacific Time (US & Canada)", 
									"(GMT-09:00) Alaska", 
									"(GMT-10:00) Hawaii", 
									"(GMT-11:00) Coordinated Universal Time-11", 
									"(GMT-11:00) Samoa", 
									"(GMT-12:00) International Date Line West"
								);
		return $timezones;
	}
	
	/**
	* Returns a list of countries supported by CM.
	*
	* @return mixed an array of countries in string format.
	*/
	function campaignpress_get_countries() {
		$countries = array(
									"United States of America",
									"United Kingdom",
									"Australia",
									"Canada",
									"Afghanistan",
									"Albania",
									"Algeria",
									"American Samoa",
									"Andorra",
									"Angola",
									"Anguilla",
									"Antigua & Barbuda",
									"Argentina",
									"Armenia",
									"Aruba",
									"Australia",
									"Austria",
									"Azerbaijan",
									"Azores",
									"Bahamas",
									"Bahrain",
									"Bangladesh",
									"Barbados",
									"Belarus",
									"Belgium",
									"Belize",
									"Benin",
									"Bermuda",
									"Bhutan",
									"Bolivia",
									"Bonaire",
									"Bosnia & Herzegovina",
									"Botswana",
									"Brazil",
									"British Indian Ocean Ter",
									"Brunei",
									"Bulgaria",
									"Burkina Faso",
									"Burundi",
									"Cambodia",
									"Cameroon",
									"Canada",
									"Canary Islands",
									"Cape Verde",
									"Cayman Islands",
									"Central African Republic",
									"Chad",
									"Channel Islands",
									"Chile",
									"China",
									"Christmas Island",
									"Cocos Island",
									"Columbia",
									"Comoros",
									"Congo",
									"Congo Democratic Rep",
									"Cook Islands",
									"Costa Rica",
									"Cote DIvoire",
									"Croatia",
									"Cuba",
									"Curacao",
									"Cyprus",
									"Czech Republic",
									"Denmark",
									"Djibouti",
									"Dominica",
									"Dominican Republic",
									"East Timor",
									"Ecuador",
									"Egypt",
									"El Salvador",
									"Equatorial Guinea",
									"Eritrea",
									"Estonia",
									"Ethiopia",
									"Falkland Islands",
									"Faroe Islands",
									"Fiji",
									"Finland",
									"France",
									"French Guiana",
									"French Polynesia",
									"French Southern Ter",
									"Gabon",
									"Gambia",
									"Georgia",
									"Germany",
									"Ghana",
									"Gibraltar",
									"Great Britain",
									"Greece",
									"Greenland",
									"Grenada",
									"Guadeloupe",
									"Guam",
									"Guatemala",
									"Guinea",
									"Guyana",
									"Haiti",
									"Honduras",
									"Hong Kong",
									"Hungary",
									"Iceland",
									"India",
									"Indonesia",
									"Iran",
									"Iraq",
									"Ireland",
									"Isle of Man",
									"Israel",
									"Italy",
									"Jamaica",
									"Japan",
									"Jordan",
									"Kazakhstan",
									"Kenya",
									"Kiribati",
									"Korea North",
									"Korea South",
									"Kuwait",
									"Kyrgyzstan",
									"Laos",
									"Latvia",
									"Lebanon",
									"Lesotho",
									"Liberia",
									"Libya",
									"Liechtenstein",
									"Lithuania",
									"Luxembourg",
									"Macau",
									"Macedonia",
									"Madagascar",
									"Malawi",
									"Malaysia",
									"Maldives",
									"Mali",
									"Malta",
									"Marshall Islands",
									"Martinique",
									"Mauritania",
									"Mauritius",
									"Mayotte",
									"Mexico",
									"Midway Islands",
									"Moldova",
									"Monaco",
									"Mongolia",
									"Montserrat",
									"Morocco",
									"Mozambique",
									"Myanmar",
									"Namibia",
									"Nauru",
									"Nepal",
									"Netherland Antilles", 
									"Netherlands",
									"Nevis",
									"New Caledonia", 
									"New Zealand", 
									"Nicaragua",
									"Niger",
									"Nigeria",
									"Niue",
									"Norfolk Island",
									"Norway",
									"Oman",
									"Pakistan",
									"Palau Island", 
									"Palestine",
									"Panama",
									"Papua New Guinea", 
									"Paraguay",
									"Peru",
									"Philippines",
									"Pitcairn Island", 
									"Poland",
									"Portugal",
									"Puerto Rico", 
									"Qatar",
									"Reunion",
									"Romania",
									"Russia",
									"Rwanda",
									"Saipan",
									"Samoa",
									"Samoa American", 
									"San Marino", 
									"Sao Tome & Principe",
									"Saudi Arabia", 
									"Senegal",
									"Serbia & Montenegro", 
									"Seychelles",
									"Sierra Leone",
									"Singapore",
									"Slovakia",
									"Slovenia",
									"Solomon Islands",
									"Somalia",
									"South Africa", 
									"Spain",
									"Sri Lanka",
									"St Barthelemy", 
									"St Eustatius",
									"St Helena",
									"St Kitts-Nevis",
									"St Lucia",
									"St Maarten",
									"St Pierre & Miquelon",
									"St Vincent & Grenadines",
									"Sudan",
									"Suriname",
									"Swaziland",
									"Sweden",
									"Switzerland",
									"Syria",
									"Tahiti",
									"Taiwan",
									"Tajikistan",
									"Tanzania",
									"Thailand",
									"Togo",
									"Tokelau",
									"Tonga",
									"Trinidad & Tobago",
									"Tunisia",
									"Turkey",
									"Turkmenistan",
									"Turks & Caicos Is",
									"Tuvalu",
									"Uganda",
									"Ukraine",
									"United Arab Emirates",
									"United Kingdom",
									"United States of America",
									"Uruguay",
									"Uzbekistan",
									"Vanuatu",
									"Vatican City State",
									"Venezuela",
									"Vietnam",
									"Virgin Islands (Brit)",
									"Virgin Islands (USA)",
									"Wake Island",
									"Wallis & Futana Is",
									"Yemen",
									"Zambia",
									"Zimbabwe"
								);
		return $countries;		
	}
	
	/**
	* Returns a list of statuses that the client can be at.
	*
	* @return mixed an array of statuses.
	*/
	function campaignpress_get_client_statuses() {
		$statuses = array(
									"Awaiting Approval", 
									"Active",
                  "Inactive"
								);
		return $statuses;
	}
	
	/**
	* Encrypts a password for DB storage
	*
	* @param string $input_string the password to encrypt
	* @param string $key the key to use to encrypt the password
	* @return string the encrypted password
	*/
	function campaignpress_encrypt( $input_string, $key ){
  
    // check how we encrypt
    $method = 'mcrypt';
    if( ! function_exists( 'mcrypt_get_iv_size' ) ) {
      $method = 'base64';
    }
    
    if( 'base64' == $method ) {
      $result = '';
      for($i = 0; $i < strlen($input_string); $i++) {
          $char = substr($input_string, $i, 1);
          $keychar = substr($key, ($i % strlen($key))-1, 1);
          $char = chr(ord($char) + ord($keychar));
          $result .= $char;
      }
      // we need to identify that this isn't mcrypt
      return '[mcrypt]:' . base64_encode($result);
    } else {
      $iv_size = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB );
      $iv = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
      $h_key = hash( 'sha256', $key, true );
      return base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, $h_key, $input_string, MCRYPT_MODE_ECB, $iv ) );
    }
	}

	/**
	* Decrypts a password for retrieval
	*
	* @param string $encrypted_input_string the encrypted password
	* @param string $key the key to use to decrypt the password
	* @return string the decrypted password
	*/
	function campaignpress_decrypt( $encrypted_input_string, $key ) {
  
    // check how we decrypt
    $method = 'mcrypt';
    if( ! function_exists( 'mcrypt_get_iv_size' ) ) {
      $method = 'base64';
    } else {
      if( '[mcrypt]:' == substr( $encrypted_input_string, 0, 9 ) ) {
        $method = 'base64';
      }
    }
    
    if( 'mcrypt' == $method ) {
      $iv_size = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB );
      $iv = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
      $h_key = hash( 'sha256', $key, true );
      return trim(mcrypt_decrypt( MCRYPT_RIJNDAEL_256, $h_key, base64_decode( $encrypted_input_string ), MCRYPT_MODE_ECB, $iv ) );
    } else {
      // first we rip the [mcrypt]: off the string then decode
      $encrypted_input_string = str_replace( '[mcrypt]:', '', $encrypted_input_string );
      $result = '';
      $encrypted_input_string = base64_decode($encrypted_input_string);
      for($i = 0; $i < strlen($encrypted_input_string); $i++) {
          $char = substr($encrypted_input_string, $i, 1);
          $keychar = substr($key, ($i % strlen($key))-1, 1);
          $char = chr(ord($char) - ord($keychar));
          $result .= $char;
      }
      return $result;
    }
    
	}

	/**
	* Generates a super-random key to use to uniquely identify a system 
	*/
	function campaignpress_generate_key() {
	
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				// 32 bits for "time_low"
				mt_rand(0, 0xffff), mt_rand(0, 0xffff),
				// 16 bits for "time_mid"
				mt_rand(0, 0xffff),
				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand(0, 0x0fff) | 0x4000,
				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand(0, 0x3fff) | 0x8000,
				// 48 bits for "node"
				mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
			);
	}	
	
	/**
	* Generates a random 9-character long password
	*/
	function campaignpress_generate_password() {
		$length = 9;
		$strength = 4;
		$vowels = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		
		if ($strength & 1) {
			$consonants .= 'BDGHJLMNPQRSTVWXZ';
		}
		if ($strength & 2) {
			$vowels .= "AEUY";
		}
		if ($strength & 4) {
			$consonants .= '23456789';
		}
		if ($strength & 8) {
			$consonants .= '@#$%';
		}
	 
		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}
	
	/**
	* Checks for configuration errors (usually called on page load)
	*
	* @param mixed $input the input as an array
	* @return mixed an array of errors (if any)
	*/
	function campaignpress_check_settings( $input ) {
		
		global $campaignpress_settings;
		
		$errors = array();

		$input['apikey'] = trim( $input['apikey'] );
		if( ! preg_match( '/^[a-z0-9]{32}$/i', $input['apikey'] ) ) {
			$errors[] = __('API Key invalid - should be 32 characters in length and consist of only letters &amp; numbers.', 'campaignpress');
		}
	
		$input['approval_group'] = trim( $input['approval_group'] );
		if( strlen( $input['approval_group'] ) < 1 ) {
			$errors[] = __('Initial sign up group cannot be blank.', 'campaignpress');
		}
		
		$input['cn_on_approval_email_template'] = trim( $input['cn_on_approval_email_template'] );
		if( strlen( $input['cn_on_approval_email_template'] ) < 1 ) {
			$errors[] = __('Client welcome email template cannot be blank.', 'campaignpress');
		}
		$input['cn_on_approval_email_subject'] = trim( $input['cn_on_approval_email_subject'] );
		if( strlen( $input['cn_on_approval_email_subject'] ) < 1 ) {
			$errors[] = __('Client welcome email subject cannot be blank.', 'campaignpress');
		}
		
		$input['cn_on_approval_email'] = trim( $input['cn_on_approval_email'] );
		
		$input['cn_on_signup_email'] = trim( $input['cn_on_signup_email'] );
		if ( $input['cn_on_signup_email'] ) {
		
			$input['cn_on_signup_email_template'] = trim( $input['cn_on_signup_email_template'] );
			if( strlen( $input['cn_on_signup_email_template'] ) < 1 ) {
				$errors[] = __('Client sign up email is enabled, template cannot be blank.', 'campaignpress');
			}
			$input['cn_on_signup_email_subject'] = trim( $input['cn_on_signup_email_subject'] );
			if( strlen( $input['cn_on_signup_email_subject'] ) < 1 ) {
				$errors[] = __('Client sign up email is enabled, subject cannot be blank.', 'campaignpress');
			}
		}			
		
		$input['notifications_from_email'] = trim( $input['notifications_from_email'] );
		if( strlen( $input['notifications_from_email'] ) > 0 ) {
			if( strlen( $input['notifications_from_email'] ) > 0 && ! is_email( $input['notifications_from_email'] ) ) {
				$errors[] = __('From email address is invalid', 'campaignpress') . ' (' . $email . ').';
			}
		} else {
			$errors[] = __('From email address cannot be blank.', 'campaignpress');
		}
		
		$input['admin_email'] = trim( $input['admin_email'] );
		if( strlen( $input['admin_email'] ) > 0 ) {
			$emails = explode( ',', $input['admin_email'] );
			foreach( $emails as $email ) {
				$email = trim( $email );
				if( strlen( $email ) > 0 && ! is_email( $email ) ) {
					$errors[] = __('Admin email address is invalid', 'campaignpress') . ' (' . $email . ').';
				}
			}
		} else {
			$errors[] = __('Admin email address cannot be blank.', 'campaignpress');
		}
		
		$input['override_mail'] = trim( $input['override_mail'] );
		if( strlen( $input['override_mail'] ) > 0 ) {
		
			// we want to override mail, thus security, SMTP server and port must be set
			$input['override_mail_type'] = trim( $input['override_mail_type'] );
			switch( $input['override_mail_type'] ) {
			
				case 'Unsecured':
				case 'ssl':
				case 'tls':
					break;
				
				default:
					$errors[] = __( 'Invalid SMTP server security specified.', 'campaignpress' );
					break;
			}
			
			$input['override_mail_server'] = trim( $input['override_mail_server'] );
			if( strlen( $input['override_mail_server'] ) < 1 ) {
				$errors[] = __( 'Mail override enabled, SMTP server cannot be blank.', 'campaignpress' );
			}
			
			$input['override_mail_port'] = trim( $input['override_mail_port'] );
			if( strlen( $input['override_mail_port'] ) < 1 ) {
				$errors[] = __( 'Mail override enabled, port cannot be blank.', 'campaignpress' );
			}
			
		}
		
		$input['override_mail_username'] = trim( $input['override_mail_username'] );
		$input['override_mail_password'] = trim( $input['override_mail_password'] );
		
		return $errors;	
		
	}
	
	/**
	* True/false if there are configuration errors
	*/
	function campaignpress_config_has_errors() {
		
		global $campaignpress_settings;
		$input = $campaignpress_settings;
		$errors = campaignpress_check_settings( $input );
		if( count( $errors ) > 0 ) {
			return true;
		}
		return false;
	}

	/**
	* Sends a notification email to the admin(s)
	*
	* @param object $object any object such as a client that might be used in the email
	* @param string $notify_type the type of notification to send
	* @return string the result of the send - either true or an error
	*/
	function campaignpress_notify_admin( $object, $notify_type ) {
		
		global $campaignpress_settings;
		
		$message = '';
		$subject = '';
		
		// message, subject & log message
		switch( $notify_type ) 
		{
			case 'client-signup':
				$message  = $object->contact_name . ' ' . __( 'from' , 'campaignpress' ) . ' ' . $object->company . ' ' . __( 'has just signed up through Campaign Press' , 'campaignpress' ) . ".\r\n\r\n";
				$message .= __( 'Visit' , 'campaignpress' ) . ' ' . admin_url( 'admin.php?page=campaignpress-clients&client=' ) . $object->id . ' ' . __( 'to approve or delete this client' , 'campaignpress' ) . '.';
				$subject = __( 'New Campaign Press sign up from' , 'campaignpress' ) . ' ' . $object->company;
				break;
		}

		// from and to
		$from = $campaignpress_settings['notifications_from_email'];
		$from_name = $campaignpress_settings['notifications_from_name'];
		if( strlen( $from_name ) < 1 ) {
			$from_name = 'Campaign Press Site';
		}
		
		// multiple emails are a possibility
		$to = $campaignpress_settings['admin_email'];
		
		// send the email
		return campaignpress_mail( $subject, $message, $from, $from_name, $to );
	}
	
	/**
	* Sends a notification email to the client
	*
	* @param object $client the client object used for retrieving details
	* @param string $notify_type the type of notification to send
	* @return string the result of the send - either true or an error
	*/
	function campaignpress_notify_client( $client, $notify_type ) {
		
		global $campaignpress_settings;
		
		$message = '';
		$subject = '';
		$log_message = '';
		
		// message, subject & log message
		switch( $notify_type ) 
		{
			case 'welcome':
				$message = campaignpress_translate_client_message( $client, $campaignpress_settings['cn_on_approval_email_template'] );
				$subject = campaignpress_translate_client_message( $client, $campaignpress_settings['cn_on_approval_email_subject'] );
				$log_message = __( 'welcome email to client.', 'campaignpress' );
				break;
				
			case 'signup':
				$message = campaignpress_translate_client_message( $client, $campaignpress_settings['cn_on_signup_email_template'] );
				$subject = campaignpress_translate_client_message( $client, $campaignpress_settings['cn_on_signup_email_subject'] );
				$log_message = __( 'sign up email to client.', 'campaignpress' );
				break;
		}

		// from and to
		$from = $campaignpress_settings['notifications_from_email'];
		$from_name = $campaignpress_settings['notifications_from_name'];
		if( strlen( $from_name ) < 1 ) {
			$from_name = 'Campaign Press Site';
		}
		$to = $client->email;
		
		// send the email
		if( campaignpress_mail( $subject, $message, $from, $from_name, $to ) ) {
			$log_message_result = __( 'Sent', 'campaignpress' );
		} else {
			$log_message_result = __( 'Failed to send', 'campaignpress' );
		}
		
		// build the final log message
		$log_message = $log_message_result . ' ' . $log_message;
		
		// log message
		campaignpress_log_activity( 'client', $client->id, $log_message );
		
	}
	
	/**
	* Replaces [field] with actual field value
	*
	* @param object $client the client object used for retrieving details
	* @param string $message the message to translate
	* @return string the translated message
	*/
	function campaignpress_translate_client_message( $client, $message ) {
		
		global $campaignpress_settings;
		
		$first_name = $client->contact_name;
		$names = explode( ' ', $client->contact_name );
		foreach( $names as $name ) {
			$first_name = $name;
			break;
		}
		
		// unencrypt our password ($client object stores it encrypted)
		$temp_password = campaignpress_decrypt( $client->temp_password, $campaignpress_settings['cp_key'] );
		
		$message = str_replace( '[contact_name]', $client->contact_name, $message );
		$message = str_replace( '[company]', $client->company, $message );
		$message = str_replace( '[username]', $client->username, $message );
		$message = str_replace( '[temp_password]', $temp_password, $message );
		$message = str_replace( '[first_name]', $first_name, $message );
		
		return $message;
	}
	
	/**
	* Makes the call to send an email either via wp_mail or via the included PHPMailer class
	*
	* @param string $subject the email subject
	* @param string $message the email body/message
	* @param string $from the from email address
	* @param string $from_name the from name
	* @param string $to the to email address
	* @return string the result of the send - either true or an error
	*/
	function campaignpress_mail( $subject, $message, $from, $from_name, $to ) {
		
		global $campaignpress_settings;
		
		if( $campaignpress_settings['override_mail'] ) {
			// send using smtp
			return campaignpress_smtp_mail( $subject, $message, $from, $from_name, $to);
		} else {
			// send using WP 
			'From: My Name <myname@mydomain.com>' . "\r\n\\";
			$headers = 'From: ' . $from_name . ' <' . $from . ">\r\n\\";
			return wp_mail( $to, $subject, $message, $headers );
		}
		
	}

	/**
	* Sends an email using PHPMailer
	*
	* @param string $subject the email subject
	* @param string $message the email body/message
	* @param string $from the from email address
	* @param string $from_name the from name
	* @param string $to the to email address
	* @return string the result of the send - either true or an error
	*/
	function campaignpress_smtp_mail( $subject, $message, $from, $from_name, $to) {
		
		global $campaignpress_settings;
		
		if( ! class_exists('PHPMailer') ) {
			require_once CAMPAIGNPRESS_PLUGIN_DIR . '/lib/phpmailer/class.phpmailer.php';
			require_once CAMPAIGNPRESS_PLUGIN_DIR . '/lib/phpmailer/class.smtp.php';
		}

		// create our mail object and get it ready for sending!
		$mail = new PHPMailer();
		$mail->ClearAllRecipients();
		$mail->ClearAddresses();
		$mail->ClearAttachments();
		$mail->CharSet = 'utf-8';
		$mail->SetLanguage('en', CAMPAIGNPRESS_PLUGIN_DIR . '/lib/phpmailer/');
		$mail->PluginDir = CAMPAIGNPRESS_PLUGIN_DIR . '/lib/phpmailer/';
		$mail->IsSMTP();
		$mail->Host	= $campaignpress_settings['override_mail_server'];
		$mail->Port = $campaignpress_settings['override_mail_port'];

		if ( 'tls' == $campaignpress_settings['override_mail_type'] || 'ssl' == $campaignpress_settings['override_mail_type'] ) {
			$mail->SMTPSecure = $campaignpress_settings['override_mail_type'];
		}

		if ( strlen( $campaignpress_settings['override_mail_username'] ) > 0 ){
			$mail->SMTPAuth = true;
			$mail->Username = $campaignpress_settings['override_mail_username'];
			$mail->Password = campaignpress_decrypt( $campaignpress_settings['override_mail_password'], $campaignpress_settings['cp_key'] );
		}

		// who's this from?
		$mail->From = $from;
		$mail->FromName = $from_name;

		// who's it to?
		if ( !is_array( $to ) )
			$to = explode( ',', $to );
			
		foreach( $to as $address ){
			$mail->AddAddress( trim( $address) );
		}
		
		// subject & body
		$mail->Subject = $subject;
		$mail->Body = $message;

		// try sending it!
		if( $mail->Send() ) {
			return true;
		} else {
			return $mail->ErrorInfo;
		}
	}
	
    
  /**
	* Adds file paths to an array of file paths
	*
	* @param string $dir the directory to search
	* @param string &$files files we've already got
  * @param string $ext an optional file extension
	* @return array the php files in this and any nested directories
	*/
  function campaignpress_get_files( $dir, &$files, $ext = null ) { 
  
    // get a handle on the dir
    $handle = opendir($dir); 
    
    // go through each file in the dir and get the php ones
    while( ( $file = readdir( $handle ) ) !== false ) { 
    
      // skip these "files"
      if ( '.' == $file || '..' == $file ) { 
        continue; 
      } 
      
      $filepath = $dir == '.' ? $file : $dir . '/' . $file; 
      
      if( is_link( $filepath ) ) 
        continue; 
          
      if( is_file( $filepath ) ) {
        
        if( null != $ext ) {
          if( substr( $filepath, -4 ) == $ext ) {
            $files[] = $filepath; 
          }
        } else {
          $files[] = $filepath; 
        }
        
      } else if( is_dir( $filepath ) ) {
      
        campaignpress_get_files( $filepath, $files, $ext ); 
        
      }
    }
    
    // close our handle
    closedir($handle); 
    
  } 
	
  
	/**
	* Gets a list of addons from the database
	*
	* @return array the list of addons
	*/
	function campaignpress_get_addons() {
	
		global $wpdb, $campaignpress;
	
		// in future this will be done via webservice!
		$campaignpress_addons = array();
		
		$query = $wpdb->prepare( "SELECT * FROM $campaignpress->addon_table_name;" );
		$addons_db = $wpdb->get_results( $query );
	
		foreach( $addons_db as $addon_db ) {
			$addon = new CampaignPressAddon();
			$addon->code = $addon_db->code;
			$addon->name = __( $addon_db->name, 'campaignpress' );
			$addon->description = __( $addon_db->description, 'campaignpress' );
			$addon->class_name = $addon_db->class_name;
			$addon->url = "http://floatingmonk.co.nz/campaignpress/addons/$addon_db->code/";
			$addon->required_cp_version = $addon_db->required_cp_version;
			$addon->latest_version = $addon_db->latest_version;
			$addon->release_date = $addon_db->released;
			$campaignpress_addons[$addon->code] = $addon;
			
		}

		return $campaignpress_addons;
	}	

	/**
	* Goes through the addons directory and includes the addons
	*/
	campaignpress_load_addons();
	function campaignpress_load_addons() {
		
		global $wpdb, $campaignpress;
 
    // first make sure we've got an addons directory
    $addons_dir = CAMPAIGNPRESS_PLUGIN_DIR . '/addons';
    if( ! is_dir( $addons_dir ) ) {
      return;
    }
    
    // grab the PHP files from this directory
    $addon_files = array(); 
    campaignpress_get_files( $addons_dir, $addon_files, '.php' ); 

    foreach( $addon_files as $file ) {
      // include our class
      include_once $file;

      // load the only class that should be present & get the meta data so we can insert it into the DB If required
      if( preg_match( '/class ([a-zA-Z0-9_]+)/', file_get_contents( $file ), $matches ) ) {
      
        $class_name = $matches[1];
        
        $function_call = array( $class_name, 'class_meta' );
        $addon_meta = call_user_func( $function_call );

        if( is_array( $addon_meta ) ) {

          // if it's not in the DB then add it!
          $query = $wpdb->prepare( "SELECT code FROM $campaignpress->addon_table_name WHERE code = %s;", $addon_meta['code'] );
          $code = $wpdb->get_var( $query );
          if( strlen( $code ) < 1 ) {
            $params = array( 	
                        'name' => $addon_meta['name'],
                        'code' => $addon_meta['code'],
                        'description' => $addon_meta['description'],
                        'released' =>  $addon_meta['released'],
                        'latest_version' => $addon_meta['latest_version'],
                        'required_cp_version' => $addon_meta['required_cp_version'],
                        'class_name' => $addon_meta['class_name']
                      );
            $wpdb->insert ( $campaignpress->addon_table_name, $params );
          } else {
            // we have this entry in the database, check if it needs updating
            $query = $wpdb->prepare( "SELECT id FROM $campaignpress->addon_table_name WHERE code = %s AND latest_version < %s;", $addon_meta['code'], $addon_meta['current_version'] );
            $addon_id = $wpdb->get_var( $query );
            if( strlen( $addon_id ) > 0 ) {
              // we have to update this badboy
              $params = array( 	
                          'name' => $addon_meta['name'],
                          'code' => $addon_meta['code'],
                          'description' => $addon_meta['description'],
                          'released' =>  $addon_meta['released'],
                          'latest_version' => $addon_meta['latest_version'],
                          'required_cp_version' => $addon_meta['required_cp_version'],
                          'class_name' => $addon_meta['class_name']
                        );
              $wpdb->update ( $campaignpress->addon_table_name, $params, array( 'id' => $addon_id ) );
            }
          }
        }
      }
    } 
	}
	
	/**
	* Checks if an addon exists
	*
  * @param string $addon_code the addon code
	* @return bool whether or not the addon is installed
	*/
	function campaignpress_addon_exists( $addon_code ) {
		
		$addons = campaignpress_get_addons();
		if( isset( $addons[$addon_code] ) ) {
			if( $addons[$addon_code]->addon_is_installed() ) {
				return true;
			}
		}
		
		return false;
	}
	
?>