
	<?php 
	
	$disabled = '';
	if ( ! $can_edit ) {
		$disabled = 'disabled="disabled" readonly="readonly"';
	}
	
	?>

	<div class="wrap campaignpressadmin">

	<?php screen_icon( 'campaignpress-settings' ); ?>

	<h2><?php _e( 'Settings', 'campaignpress' ); ?></h2>	
	
	<?php campaignpress_display_config_errors(); ?>
	
	<div class="error" id="custom-error-box" style="display: none;">
		<p><?php _e( 'Error', 'campaignpress' ); ?></p><p><span id="custom-error-text"></span></p>
	</div>
	
	<?php if( isset( $_GET['message'] ) ) { ?>
		<?php switch( $_GET['message'] ) { 
					case '4': ?>
						<div class="updated">
							<p><strong><?php _e( 'Sync complete', 'campaignpress'); ?>.</strong></p>
						</div>
				<?php break; ?>
			<?php case '5': ?>
						<div class="updated">
							<p><strong><?php _e( 'Field added', 'campaignpress'); ?>.</strong></p>
						</div>
				<?php break; ?>
			<?php case '6': ?>
						<div class="updated">
							<p><strong><?php _e( 'Field saved', 'campaignpress'); ?>.</strong></p>
						</div>
				<?php break; ?>
			<?php case '7': ?>
						<div class="updated">
							<p><strong><?php _e( 'Field deleted', 'campaignpress'); ?>.</strong></p>
						</div>
				<?php break; ?>
		<?php } ?>
	<?php } ?>
	
	<form action="options.php" method="post">
	
	<?php settings_fields( 'campaignpress_settings' ); ?>
	<?php $options = get_option( 'campaignpress_settings' ); ?>
	<?php settings_errors( 'campaignpress_settings' ); ?>
	
	<!-- override the referer so we don't get any messages twice -->
	<input type="hidden" name="_wp_http_referer" id="_orig_wp_http_referer" value="<?php echo esc_url( admin_url( 'admin.php?page=campaignpress-settings' ) ); ?>" />	
	<input type="hidden" name="_wp_http_referer" id="_new_wp_http_referer" value="<?php echo esc_url( admin_url( 'admin.php?page=campaignpress-settings' ) ); ?>" />	
	
	<input type="hidden" name="campaignpress_settings[cp_key]" id="campaignpress_settings[cp_key]" value="<?php echo $cp_key; ?>" />	
	
	<div id="settings-tabs"> 
		
		<!-- Actual tabs -->
		<ul> 
			<li><a href="#tab_main"><?php _e( 'Main', 'campaignpress' ); ?></a></li>
			<li><a href="#tab_signup"><?php _e( 'Sign Up', 'campaignpress' ); ?></a></li>
			<li><a href="#tab_notifications"><?php _e( 'Notifications', 'campaignpress' ); ?></a></li>
		</ul>
		
		<!-- Tab data -->
		<div id="tab_main">
		
			<fieldset>
			<legend><?php _e( 'Information', 'campaignpress' ); ?></legend>
			<table class="form-table"> 
				<tr valign="top"> 
					<th scope="row"><label><?php _e( 'Version', 'campaignpress' ); ?></label></th> 
					<td><?php printf( __( 'You are running %s version %s by %s released %s.', 'campaignpress' ), '<a href="http://floatingmonk.co.nz/campaignpress/">Campaign Press</a>', CAMPAIGNPRESS_VERSION, '<a href="http://floatingmonk.co.nz/">Floating Monk</a>', '19 December 2010' ); ?></td> 
				</tr>
				<tr valign="top"> 
					<th scope="row"></th> 
					<td>
						<a href="http://floatingmonk.co.nz/campaignpress/blog/">Blog</a>, 
						<a href="http://floatingmonk.co.nz/campaignpress/addons/">Buy Add-Ons</a>, 
						<a href="http://twitter.com/monkshout/">Floating Monk on Twitter</a>, 
						<a href="http://twitter.com/brendankilfoil/">Brendan on Twitter</a>
					</td> 
				</tr>
			</table>
			</fieldset>

			<fieldset>
			<legend><?php _e( 'Campaign Monitor Connectivity', 'campaignpress' ); ?></legend>
			<table class="form-table"> 
				<tr valign="top"> 
					<th scope="row"><label for="campaignpress_settings[apikey]"><?php _e( 'API Key', 'campaignpress' ); ?></label></th> 
					<td><input name="campaignpress_settings[apikey]" type="text" id="campaignpress_settings[apikey]" value="<?php echo $options['apikey']; ?>" class="regular-text" <?php echo $disabled; ?> /></td> 
				</tr>
			</table>
			</fieldset>
			
			<?php if( ! campaignpress_addon_exists( 'sync' ) && campaignpress_show_addon_advert() ) { ?>
						
				<div class="faux-updated">
					<p><?php printf( __( 'Easily populate your clients, groups, subscriber lists &amp; custom fields from Campaign Monitor by installing the %s.', 'campaignpress' ), '<a href="http://floatingmonk.co.nz/campaignpress/addons/sync/" target="_blank">CM Sync Add-On</a>' ); ?></p>
				</div>

			<?php } ?>
			
			<?php campaignpress_settings_main(); ?>

			<?php if ( $can_edit ) { ?>
			<p class="submit"> 
				<input name="Submit" type="submit" class="button-primary" value="<?php echo esc_html( __('Save Settings', 'campaignpress' ) ); ?>" />
			</p> 
			<?php } ?>

		</div>
		<!-- End tab data -->
		
		<!-- Tab data -->
		<div id="tab_signup"> 
		
	   	<fieldset>
			<legend><?php _e( 'Settings', 'campaignpress' ); ?></legend>
				<table class="form-table"> 
				<tr valign="top"> 
					<th scope="row"><label for="campaignpress_settings[form_title]"><?php _e( 'Form title', 'campaignpress' ); ?></label></th> 
					<td><input name="campaignpress_settings[form_title]" type="text" id="campaignpress_settings[form_title]" value="<?php echo $options['form_title']; ?>" class="regular-text" <?php echo $disabled; ?> />
					<span class="side-link"><?php _e( 'Default is', 'campaignpress' ); ?> <i>Sign Up</i>.</span>
					</td> 
				</tr>
				<tr valign="top"> 
					<th scope="row"><label for="campaignpress_settings[form_submit_text]"><?php _e( 'Submit button text', 'campaignpress' ); ?></label></th> 
					<td><input name="campaignpress_settings[form_submit_text]" type="text" id="campaignpress_settings[form_submit_text]" value="<?php echo $options['form_submit_text']; ?>" class="regular-text" <?php echo $disabled; ?> />
					<span class="side-link"><?php _e( 'Default is', 'campaignpress' ); ?> <i>Sign Up</i>.</span>
					</td> 
				</tr>
				<tr valign="top"> 
					<th scope="row"><label for="campaignpress_settings[form_before_text]"><?php _e( 'Form Message', 'campaignpress' ); ?></label></th> 
					<td>
					<?php _e( 'Displayed before the sign up form.  HTML Allowed.  No Default.', 'campaignpress' ); ?><br/>
					<textarea name="campaignpress_settings[form_before_text]" rows="3" cols="50" id="campaignpress_settings[form_before_text]" class="large-text code" <?php echo $disabled; ?>><?php echo $options['form_before_text']; ?></textarea> 
					</td> 
				</tr>
				<tr valign="top"> 
					<th scope="row"><label for="campaignpress_settings[form_success_text]"><?php _e( 'Success message', 'campaignpress' ); ?></label></th> 
					<td>
					<?php _e( 'HTML Allowed.  Default is', 'campaignpress' ); ?> <i><?php _e( 'Sign up successful!', 'campaignpress' ); ?></i>. <br/>
					<textarea name="campaignpress_settings[form_success_text]" rows="3" cols="50" id="campaignpress_settings[form_success_text]" class="large-text code" <?php echo $disabled; ?>><?php echo $options['form_success_text']; ?></textarea> 
					</td> 
				</tr>
				<tr valign="top"> 
					<th scope="row"><label for="campaignpress_settings[form_class]"><?php _e( 'Custom class', 'campaignpress' ); ?></label></th> 
					<td><input name="campaignpress_settings[form_class]" type="text" id="campaignpress_settings[form_class]" value="<?php echo $options['form_class']; ?>" class="regular-text" <?php echo $disabled; ?> />
					<span class="side-link"><?php _e( 'Class will be applied to DIV surrounding the form', 'campaignpress' ); ?>.</span>
					</td> 
				</tr>
			</table>
			</fieldset>
			
			<fieldset>
			<legend><?php _e( 'Defaults', 'campaignpress' ); ?></legend>
				<table class="form-table"> 
				<tr valign="top"> 
					<th scope="row"><label for="campaignpress_settings[default_country]"><?php _e( 'Country', 'campaignpress' ); ?></label></th> 
					<td>
						<select name="campaignpress_settings[default_country]" id="campaignpress_settings[default_country]" <?php echo $disabled; ?>> 
							<?php foreach( $countries as $country ) { ?>
								<option <?php if( $country == $options['default_country'] ) { echo 'selected="selected"'; } ?> value='<?php echo $country; ?>'><?php echo $country; ?></option> 
							<?php } ?>
						</select> 
					</td> 
				</tr>
				<tr valign="top"> 
					<th scope="row"><label for="campaignpress_settings[default_timezone]"><?php _e( 'Timezone', 'campaignpress' ); ?></label></th> 
					<td>
						<select name="campaignpress_settings[default_timezone]" id="campaignpress_settings[default_timezone]" <?php echo $disabled; ?>> 
							<?php foreach( $timezones as $timezone ) { ?>
								<option <?php if( $timezone == $options['default_timezone'] ) { echo 'selected="selected"'; } ?> value='<?php echo $timezone; ?>'><?php echo $timezone; ?></option> 
							<?php } ?>
						</select> 
					</td> 
				</tr>
				<tr valign="top"> 
					<th scope="row"><label for="campaignpress_settings[approval_group]"><?php _e( 'Group', 'campaignpress' ); ?></label></th> 
					<td>
					<?php if( $all_groups_count > 0 ) { ?>
						<select name="campaignpress_settings[approval_group]" id="campaignpress_settings[approval_group]" <?php echo $disabled; ?>> 
							<?php if( empty( $options['approval_group'] ) ) { ?>
								<option value=''>--</option> 
							<?php } ?>
							<?php foreach( $all_groups as $group ) { ?>
								<option <?php if( $group->id == $options['approval_group'] ) { echo 'selected="selected"'; } ?> value='<?php echo $group->id; ?>'><?php echo $group->name; ?></option> 
							<?php } ?>
						</select> 
					<?php } else { ?>
						<?php _e( 'No groups, please', 'campaignpress' ) ?> <a href="<?php echo esc_url( admin_url( "admin.php?page=campaignpress-groups&group=new" ) ); ?>" ><?php _e( 'add one', 'campaignpress' ) ?></a>.
					<?php } ?>
					<span class="side-link"><?php _e( 'Sets initial Access &amp; Billing for the client on approval', 'campaignpress' ); ?>.</span></td>
				</tr>
			</table>
			</fieldset>

			<fieldset>
			<legend><?php _e( 'Form Fields', 'campaignpress' ); ?></legend>
			
			<div style="float:left;">
				<table class="form-table"> 
					<tr valign="top"> 
						<th scope="row">
							<label><?php _e( 'Displayed', 'campaignpress' ); ?></label>
							<div class="hint">
								<p><?php _e( 'These fields will be displayed on the sign up form', 'campaignpress' ); ?>.</p>
								<p><?php _e( 'Custom fields from the list below can be dragged into this list', 'campaignpress' ); ?>.</p>
							</div>
						</th> 
						<td>
							<ul id="displayed-fields" class="connectedSortable">
								<?php foreach( $displayed_fields as $field ) { 
											$static = '';
											if( $field->required && $field->system_field ) {
												$static = ' static';
											}
								?>
									<li class="form-field <?php echo $static; ?>" id="custom-field-<?php echo $field->id; ?>">
									<?php echo strlen( $field->name ) > 20 ? substr( $field->name, 0, 20) . '...' : $field->name;  ?>
										<?php if( ! $field->system_field ) { ?>
											<div class="action">
												<a href="#" id="custom-field-edit-<?php echo esc_html( $field->id ); ?>"><?php _e( 'Edit', 'campaignpress' ); ?></a> | 
												<a href="#" id="dialoglink-delete-field-<?php echo esc_html( $field->id ); ?>"><?php _e( 'Del', 'campaignpress' ); ?></a>
											</div>
										<?php } ?>
									</li>
								<?php } ?>
							</ul>
						</td> 
					</tr>
				</table>

				<table class="form-table"> 
					<tr valign="top"> 
						<th scope="row">
							<label><?php _e( 'Hidden', 'campaignpress' ); ?></label>
							<div class="hint">
								<p><?php _e( 'These fields will not be displayed on the sign up form, but you can still fill them via the administration interface', 'campaignpress' ); ?>.</p>
							</div>
						</th> 
						<td>
							<ul id="hidden-fields" class="connectedSortable">
								<?php foreach( $hidden_fields as $field ) { ?>
									<li class="form-field" id="custom-field-<?php echo $field->id; ?>">
										<?php echo strlen( $field->name ) > 20 ? substr( $field->name, 0, 20) . '...' : $field->name;  ?>
										<?php if( 1 != $field->system_field ) { ?>
											<div class="action">
												<a href="#" id="custom-field-edit-<?php echo esc_html( $field->id ); ?>"><?php _e( 'Edit', 'campaignpress' ); ?></a> | 
												<a href="#" id="dialoglink-delete-field-<?php echo esc_html( $field->id ); ?>"><?php _e( 'Del', 'campaignpress' ); ?></a>
											</div>
										<?php } ?>
									</li>
								<?php } ?>
							</ul>
							<?php if( ! campaignpress_addon_exists( 'custom-fields' ) && campaignpress_show_addon_advert() ) { ?>
								<div class="faux-updated">
									<p><?php printf( __( 'Gather custom data from clients on sign up by installing the %s.', 'campaignpress' ), '<a href="http://floatingmonk.co.nz/campaignpress/addons/custom-fields/" target="_blank">Custom-Fields Add-On</a>' ); ?></p>
								</div>
							<?php } ?>
							<?php campaignpress_settings_signup_hidden_fields(); ?>
						</td>
					</tr>
				</table>

			</div>
			
			</fieldset>
			
			<?php campaignpress_settings_signup(); ?>
			
			<?php if ( $can_edit ) { ?>
				<p class="submit"> 
					<input name="Submit" type="submit" class="button-primary" value="<?php echo esc_html( __('Save Settings', 'campaignpress' ) ); ?>" />
				</p> 
			<?php } ?>

		</div>
		<!-- End tab data -->
		
		<!-- Tab data -->
		<div id="tab_notifications"> 
		
			<fieldset>
				<legend><?php _e( 'Settings', 'campaignpress' ); ?></legend>
				<table class="form-table">
					<tr valign="top"> 
						<th scope="row"><label for="notifications_from_name"><?php _e( 'From Name', 'campaignpress' ); ?></label></th> 
						<td>
							<input name="campaignpress_settings[notifications_from_name]" type="text" id="campaignpress_settings[notifications_from_name]" value="<?php echo $options['notifications_from_name']; ?>" class="regular-text" <?php echo $disabled; ?> /> <span class="side-link"><?php _e( 'Default is', 'campaignpress' ); ?> <i><?php _e( 'Campaign Press Site', 'campaignpress' ); ?></i></span>
						</td> 
					</tr>
					<tr valign="top"> 
						<th scope="row"><label for="notifications_from_email"><?php _e( 'From Email', 'campaignpress' ); ?></label></th> 
						<td>
							<input name="campaignpress_settings[notifications_from_email]" type="text" id="campaignpress_settings[notifications_from_email]" value="<?php echo $options['notifications_from_email']; ?>" class="regular-text" <?php echo $disabled; ?> /> <span class="side-link"><?php _e( 'Notifications will be sent from this email address', 'campaignpress' ); ?></span>
						</td> 
					</tr>
					<tr valign="top"> 
						<th scope="row"><label for="admin_email"><?php _e( 'Admin Email(s)', 'campaignpress' ); ?></label></th> 
						<td>
							<input name="campaignpress_settings[admin_email]" type="text" id="campaignpress_settings[admin_email]" value="<?php echo $options['admin_email']; ?>" class="regular-text" <?php echo $disabled; ?> /> <span class="side-link"><?php _e( 'If multiple emails are required, use a comma to separate them', 'campaignpress' ); ?></span>
						</td> 
					</tr>
				</table>
				<table class="form-table">
					<tr valign="top"> 
						<th scope="row"><label for="override_mail"><?php _e( 'Use SMTP for email', 'campaignpress' ); ?></label></th> 
						<td>
							<input name="campaignpress_settings[override_mail]" type="checkbox" class="toggle-control" id="override_mail" value="1" <?php if( 1 == $options['override_mail'] ) { echo "checked='checked'"; } ?> <?php echo $disabled; ?> /> 
							<label class="checkbox" for="override_mail"><?php _e( 'Override WP\'s default mail() sending with SMTP sending (use this if notifications are not being sent)', 'campaignpress' ); ?></label>
						</td> 
					</tr>
				</table>
				<div id="override_mail-item">
				<table class="form-table">
					<tr valign="top"> 
						<th scope="row"><label for="campaignpress_settings[override_mail_type]"><?php _e( 'Security', 'campaignpress' ); ?></label></th> 
						<td>
							<select name="campaignpress_settings[override_mail_type]" id="campaignpress_settings[override_mail_type]" <?php echo $disabled; ?>> 
									<option <?php if( 'Unsecured' == $options['override_mail_type'] ) { echo 'selected="selected"'; } ?> value='Unsecured'><?php _e( 'Unsecured', 'campaignpress' ); ?></option>
									<option <?php if( 'ssl' == $options['override_mail_type'] ) { echo 'selected="selected"'; } ?> value='ssl'><?php _e( 'SSL', 'campaignpress' ); ?></option>
									<option <?php if( 'tls' == $options['override_mail_type'] ) { echo 'selected="selected"'; } ?> value='tls'><?php _e( 'TLS', 'campaignpress' ); ?></option>
							</select> 
						</td> 
					</tr>
					<tr valign="top"> 
						<th scope="row"><label for="campaignpress_settings[override_mail_server]"><?php _e( 'SMTP Server', 'campaignpress' ); ?></label></th> 
						<td>
							<input name="campaignpress_settings[override_mail_server]" type="text" id="campaignpress_settings[override_mail_server]" value="<?php echo $options['override_mail_server']; ?>" class="regular-text" <?php echo $disabled; ?> /> <span class="side-link"><?php _e( 'e.g.', 'campaignpress' ); ?> <i>smtp.gmail.com</i></span>
						</td> 
					</tr>
					<tr valign="top"> 
						<th scope="row"><label for="campaignpress_settings[override_mail_port]"><?php _e( 'Port', 'campaignpress' ); ?></label></th> 
						<td>
							<input name="campaignpress_settings[override_mail_port]" type="text" id="campaignpress_settings[override_mail_port]" value="<?php echo $options['override_mail_port']; ?>" class="small-text" <?php echo $disabled; ?> /> <span class="side-link"><?php _e( 'e.g.', 'campaignpress' ); ?> <i>465</i></span>
						</td> 
					</tr>
					<tr valign="top"> 
						<th scope="row"><label for="campaignpress_settings[override_mail_username]"><?php _e( 'Username', 'campaignpress' ); ?></label></th> 
						<td>
							<input name="campaignpress_settings[override_mail_username]" type="text" id="campaignpress_settings[override_mail_username]" value="<?php echo $options['override_mail_username']; ?>" class="regular-text" <?php echo $disabled; ?> />
						</td> 
					</tr>
					<tr valign="top"> 
						<th scope="row"><label for="campaignpress_settings[override_mail_password]"><?php _e( 'Password', 'campaignpress' ); ?></label></th> 
						<td>
							<input name="campaignpress_settings[override_mail_password]" type="password" id="campaignpress_settings[override_mail_password]" value="<?php echo $override_mail_password_unenc; ?>" class="regular-text" <?php echo $disabled; ?> />
						</td> 
					</tr>
				</table>
				</div>
			</fieldset>
		
			<fieldset>
				<legend><?php _e( 'Sign Up', 'campaignpress' ); ?></legend>
				<table class="form-table">
					<tr valign="top"> 
						<th scope="row"><label for="an_on_signup_email"><?php _e( 'Notify Admin', 'campaignpress' ); ?></label></th> 
						<td>
							<input name="campaignpress_settings[an_on_signup_email]" type="checkbox" class="toggle-control" id="an_on_signup_email" value="1" <?php if( 1 == $options['an_on_signup_email'] ) { echo "checked='checked'"; } ?> <?php echo $disabled; ?> /> 
							<label class="checkbox" for="an_on_signup_email"><?php _e( 'Email admin after client sign up', 'campaignpress' ); ?></label>
						</td> 
					</tr>
				</table>
				<table class="form-table">
					<tr valign="top"> 
						<th scope="row"><label for="cn_on_signup_email"><?php _e( 'Notify Client', 'campaignpress' ); ?></label></th> 
						<td>
							<input name="campaignpress_settings[cn_on_signup_email]" type="checkbox" class="toggle-control" id="cn_on_signup_email" value="1" <?php if( 1 == $options['cn_on_signup_email'] ) { echo "checked='checked'"; } ?> <?php echo $disabled; ?> /> 
							<label class="checkbox" for="cn_on_signup_email"><?php _e( 'Email client after sign up', 'campaignpress' ); ?></label>
						</td> 
					</tr>
				</table>
				<div id="cn_on_signup_email-item">
				<table class="form-table">
					<tr valign="top"> 
						<th scope="row"><label for="campaignpress_settings[cn_on_signup_email_subject]"><?php _e( 'Subject', 'campaignpress' ); ?></label></th> 
						<td>
							<input name="campaignpress_settings[cn_on_signup_email_subject]" type="text" id="campaignpress_settings[cn_on_signup_email_subject]" value="<?php echo $options['cn_on_signup_email_subject']; ?>" class="regular-text" <?php echo $disabled; ?> />
						</td> 
					</tr>
					<tr valign="top"> 
						<th scope="row"><label for="campaignpress_settings[cn_on_signup_email_template]"><?php _e( 'Template', 'campaignpress' ); ?></label>
						<div class="email-codes">
							<p><?php _e( 'Available Shortcodes', 'campaignpress' ); ?></p>
							<div class="code">
							<ul>
								<li>[first_name]</li>
								<li>[contact_name]</li>
								<li>[company]</li>
								<li>[username]</li>
								<li>[temp_password]</li>
							</ul>					
							</div>
						</div>
						</th> 
						<td>
							<textarea name="campaignpress_settings[cn_on_signup_email_template]" rows="11" cols="50" id="campaignpress_settings[cn_on_signup_email_template]" class="large-text code" <?php echo $disabled; ?>><?php echo $options['cn_on_signup_email_template']; ?></textarea> 
						</td> 
					</tr>
				</table>
				</div>
			</fieldset>

			<fieldset>
				<legend><?php _e( 'Welcome', 'campaignpress' ); ?></legend>
				<table class="form-table">
					<tr valign="top"> 
						<th scope="row"><label for="cn_on_approval_email"><?php _e( 'Automatically Notify Client', 'campaignpress' ); ?></label></th> 
						<td>
							<input name="campaignpress_settings[cn_on_approval_email]" type="checkbox" id="cn_on_approval_email" value="1" <?php if( 1 == $options['cn_on_approval_email'] ) { echo "checked='checked'"; } ?> <?php echo $disabled; ?> /> 
							<label class="checkbox" for="cn_on_approval_email"><?php _e( 'Send welcome email to client on approval (can be sent manually after approval if unchecked)', 'campaignpress' ); ?></label>
						</td> 
					</tr>
				</table>
				<div id="cn_on_approval_email-item">
				<table class="form-table">
					<tr valign="top"> 
						<th scope="row"><label for="campaignpress_settings[cn_on_approval_email_subject]"><?php _e( 'Subject', 'campaignpress' ); ?></label></th> 
						<td>
							<input name="campaignpress_settings[cn_on_approval_email_subject]" type="text" id="campaignpress_settings[cn_on_approval_email_subject]" value="<?php echo $options['cn_on_approval_email_subject']; ?>" class="regular-text" <?php echo $disabled; ?> />
						</td> 
					</tr>
					<tr valign="top"> 
						<th scope="row"><label for="campaignpress_settings[cn_on_approval_email_template]"><?php _e( 'Template', 'campaignpress' ); ?></label>
						<div class="email-codes">
							<p><?php _e( 'Available Shortcodes', 'campaignpress' ); ?></p>
							<div class="code">
							<ul>
								<li>[first_name]</li>
								<li>[contact_name]</li>
								<li>[company]</li>
								<li>[username]</li>
								<li>[temp_password]</li>
							</ul>					
							</div>
						</div>
						</th> 
						<td>
							<textarea name="campaignpress_settings[cn_on_approval_email_template]" rows="11" cols="50" id="campaignpress_settings[cn_on_approval_email_template]" class="large-text code" <?php echo $disabled; ?>><?php echo $options['cn_on_approval_email_template']; ?></textarea> 
						</td> 
					</tr>
				</table>
				</div>
			</fieldset>
			
			<?php campaignpress_settings_notifications(); ?>

			<?php if ( $can_edit ) { ?>
			<p class="submit"> 
				<input name="Submit" type="submit" class="button-primary" value="<?php echo esc_html( __('Save Settings', 'campaignpress' ) ); ?>" />
			</p> 
			<?php } ?>

		</div>
		<!-- End tab data -->

	</div>
	
	</form>
	
	</div>