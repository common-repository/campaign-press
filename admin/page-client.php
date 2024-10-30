
	<?php 
	
	global $disabled;
	$disabled = '';
	if ( ! $can_edit || 'Inactive' == $client->status ) {
		$can_edit = false;
		$disabled = 'disabled="disabled" readonly="readonly"';
	}
	
	$groups_disabled = '';
	if( ( ! campaignpress_client_can_update_access() && 'Awaiting Approval' != $client->status ) || ! $can_edit ) { 
		$groups_disabled = 'disabled readonly="readonly"';
	}
	
	?>

	<div class="wrap campaignpressadmin">
	
		<div id="approve-client-dialog" class="dialog-content">
			<div class="dialog-error-box"></div>
			<p>
				<?php echo esc_html( $client->company ); ?> <?php _e( 'will be created in Campaign Monitor', 'campaignpress' ); ?>.
			</p>
			<p class="dialog-buttons">
				<span class="approve-client-button button"><?php _e( 'Approve', 'campaignpress' ); ?></span>
				<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="approve-client-loading offset-ajax-loading" style="display:none;" alt="" /> 
				<span class="approve-client-cancel-block"><?php _e( 'or', 'campaignpress' ); ?> <a href="#" class="approve-client-cancel-text"><?php _e( 'cancel', 'campaignpress' ); ?></a></span>
			</p>
		</div>
		
		<div id="delete-client-dialog" class="dialog-content">
			<div class="dialog-error-box"></div>
			<p>
				<?php echo esc_html( $client->company ); ?> <?php _e( 'will be deleted permanently', 'campaignpress' ); ?>.
			</p>
			<p class="dialog-buttons">
				<span class="delete-client-button button"><?php _e( 'Delete', 'campaignpress' ); ?></span>
				<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="delete-client-loading offset-ajax-loading" style="display:none;" alt="" /> 
				<span class="delete-client-cancel-block"><?php _e( 'or', 'campaignpress' ); ?> <a href="#" class="delete-client-cancel-text"><?php _e( 'cancel', 'campaignpress' ); ?></a></span>
			</p>
		</div>
		
		<div id="welcome-client-dialog" class="dialog-content">
			<div class="dialog-error-box"></div>
			<p>
				<?php echo esc_html( $client->company ); ?> <?php _e( 'will be sent a welcome email', 'campaignpress' ); ?>.
			</p>
			<p class="dialog-buttons">
				<span class="welcome-client-button button"><?php _e( 'Send', 'campaignpress' ); ?></span>
				<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="welcome-client-loading offset-ajax-loading" style="display:none;" alt="" /> 
				<span class="welcome-client-cancel-block"><?php _e( 'or', 'campaignpress' ); ?> <a href="#" class="welcome-client-cancel-text"><?php _e( 'cancel', 'campaignpress' ); ?></a></span>
			</p>
		</div>

		<?php screen_icon( 'campaignpress-client' ); ?>
	
		<h2><a href="?page=<?php echo $plugin_page; ?>"><?php _e( 'Clients', 'campaignpress' ); ?></a> &#187; <?php echo esc_html( $client->company ); ?></h2>
		
		<div class="error" id="client-error-box" style="display: none;">
		</div>
		
		<?php if( isset( $_GET['message'] ) ) { ?>
			<?php switch( $_GET['message'] ) { 
						case '2': ?>
							<div class="updated">
								<p><strong><?php _e( 'Client saved', 'campaignpress'); ?>.</strong></p>
							</div>
				<?php break; ?>
				<?php case '3': ?>
							<div class="updated">
								<p><strong><?php _e( 'Client approved', 'campaignpress'); ?>.</strong></p>
							</div>
				<?php break; ?>
				<?php case '4': ?>
							<div class="updated">
								<p><strong><?php _e( 'Sent welcome email to client', 'campaignpress'); ?>.</strong></p>
							</div>
				<?php break; ?>
			<?php } ?>
		<?php } ?>
		
		<!-- We're piggy backing on WP's post styles here for to shortcut -->
		<div id="poststuff" class="metabox-holder has-right-sidebar"> 
		
			<div class="inner-sidebar">  
			
				<fieldset>
					<legend><?php _e( 'Control', 'campaignpress' ); ?></legend>
					<div class="inside">
						<div class="<?php echo strtolower( str_replace( ' ', '', $client->status ) ); ?>" id="client-status-display">
							<?php echo esc_html( $client->status ); ?>
						</div>
						<?php if( $can_edit && 'Awaiting Approval' == $client->status ) { ?>
							<div class="misc-pub-section">
								<a href="#" id="dialoglink-approve-client"><div class="ui-icon ui-icon-check" style="float: left;" ></div><?php _e( 'Approve', 'campaignpress' ); echo ' ' .  esc_html( $client->company ); ?></a>
							</div>
						<?php } ?>
						<?php if( 'Active' != $client->status && strlen( $client->company ) > 0 ) { ?>
							<div class="misc-pub-section">
								<a href="#" id="dialoglink-delete-client"><div class="ui-icon ui-icon-trash" style="float: left;" ></div> <?php _e( 'Delete', 'campaignpress' ); echo ' ' .  esc_html( $client->company ); ?></a>
							</div>
						<?php } ?>
						<?php if( ! $sent_welcome_email ) { ?>
							<div class="misc-pub-section">
								<a href="#" id="dialoglink-welcome-client"><div class="ui-icon ui-icon-mail-closed" style="float: left;" ></div> <?php _e( 'Send welcome email', 'campaignpress' ); ?></a>
							</div>
						<?php } ?>
						<?php if( $can_edit ) { ?>
						<div class="misc-pub-section">
							<p> 
								<input name="Submit" type="submit" class="button-primary" id="campaignpress-client-submit"  value="<?php echo _e('Save Changes', 'campaignpress' ); ?>" />
								<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="campaignpress-client-ajax-loading" class="offset-ajax-loading" style="display: none;" alt="" />
							</p> 
						</div>
						<?php } ?>
						<div class="clear"></div>
					</div> 
				</fieldset>
			
				<fieldset>
					<legend><?php _e( 'Latest Activity', 'campaignpress' ); ?></legend>
					<div class="inside"> 
						<?php if( $all_activity_count > 0 ) { ?>
								<?php foreach( $all_activity as $activity ) { ?>
									<div class="misc-pub-section">
										<strong><?php echo $activity->occurred; ?></strong><br /><?php echo strlen( $activity->description ) > $act_desc_len ? substr( $activity->description, 0, $act_desc_len) . '...' : $activity->description; ?>
									</div>
								<?php } ?>
						<?php } else { ?>
							<?php _e( 'No activity recorded against this client.', 'campaignpress' ); ?>
						<?php } ?>
						<div class="clear"></div>
					</div> 
				</fieldset>
			
			</div> 
			
			<div id="post-body"> 
				<div id="post-body-content"> 
				
				<form id="" action="" method="post">
					<input type="hidden" id="client_id" name="client_id" value="<?php echo esc_html( $client_id ); ?>" /> 
					<input type="hidden" id="client_status" name="client_status" value="<?php echo esc_html( $client->status ); ?>" /> 
					<fieldset>
					<legend><?php _e( 'Client Details' ); ?></legend>
					<table class="form-table"> 
						<tr valign="top"> 
							<th scope="row"><label for="client_company"><?php _e( 'Company', 'campaignpress' ); ?></label></th> 
							<td><input name="client_company" type="text" id="client_company" value="<?php echo esc_html( $client->company ); ?>" class="regular-text" <?php echo $disabled; ?> /></td> 
						</tr>
						<tr valign="top"> 
							<th scope="row"><label for="client_contact_name"><?php _e( 'Contact Name', 'campaignpress' ); ?></label></th> 
							<td><input name="client_contact_name" type="text" id="client_contact_name" value="<?php echo esc_html( $client->contact_name ); ?>" class="regular-text" <?php echo $disabled; ?> /></td> 
						</tr>
						<tr valign="top"> 
							<th scope="row"><label for="client_email"><?php _e( 'Email', 'campaignpress' ); ?></label></th> 
							<td><input name="client_email" type="text" id="client_email" value="<?php echo esc_html( $client->email ); ?>" class="regular-text" <?php echo $disabled; ?> /></td> 
						</tr>
						<tr valign="top"> 
						<th scope="row"><label for="group_id"><?php _e( 'Group', 'campaignpress' ); ?></label></th> 
						<td>
						<?php if( $all_groups_count > 0 ) { ?>
							<select name="group_id" id="group_id" <?php echo $groups_disabled; ?>> 
								<?php if( empty( $client->group_id ) ) { ?>
									<option value=''>--</option> 
								<?php } ?>
								<?php foreach( $all_groups as $group ) { ?>
									<option <?php if( $group->id == $client->group_id ) { echo 'selected="selected"'; } ?> value='<?php echo esc_html( $group->id ); ?>'><?php echo esc_html( $group->name ); ?></option> 
								<?php } ?>
							</select> 
							<span class="side-link"><a href="<?php echo esc_url( admin_url( "admin.php?page=campaignpress-groups&group=$current_group->id" ) ); ?>"><?php _e( 'View', 'campaignpress' ); echo " $current_group->name"; ?></a></span>		
							<?php } else { ?>
								<?php _e( 'No groups.  Please', 'campaignpress' ) ?> <a href="<?php echo esc_url( admin_url( "admin.php?page=campaignpress-groups&group=new" ) ); ?>" ><?php _e( 'add one', 'campaignpress' ) ?></a>.
							<?php } ?>
							</td>
						</tr>
						<tr valign="top"> 
							<th scope="row"><label for="client_username"><?php _e( 'Username', 'campaignpress' ); ?></label></th> 
							<td><input name="client_username" type="text" id="client_username" value="<?php echo esc_html( $client->username ); ?>" class="regular-text" <?php echo $groups_disabled; ?> /></td> 
						</tr>
						<tr valign="top"> 
							<th scope="row"><label for="client_country"><?php _e( 'Country', 'campaignpress' ); ?></label></th> 
							<td>
								<select name="client_country" id="client_country" <?php echo $disabled; ?>>
									<?php foreach( $countries as $country ) { ?>
										<option <?php if( $country == $client->country ) { echo 'selected="selected"'; } ?> value='<?php echo $country; ?>'><?php echo $country; ?></option> 
									<?php } ?>
								</select>
							</td> 
						</tr>
						<tr valign="top"> 
							<th scope="row"><label for="client_timezone"><?php _e( 'Timezone', 'campaignpress' ); ?></label></th> 
							<td>
								<select name="client_timezone" id="client_timezone" <?php echo $disabled; ?>>
									<?php foreach( $timezones as $timezone ) { ?>
										<option <?php if( $timezone == $client->timezone ) { echo 'selected="selected"'; } ?> value='<?php echo $timezone; ?>'><?php echo $timezone; ?></option> 
									<?php } ?>
								</select>
							</td> 
						</tr>
						<tr valign="top"> 
							<th scope="row"><label for="client_additional_info"><?php _e( 'Additional Info', 'campaignpress' ); ?></label></th> 
							<td>
							<p> 
								<textarea name="client_additional_info" rows="5" cols="50" id="client_additional_info" class="large-text code" <?php echo $disabled; ?>><?php echo esc_html( $client->additional_info ); ?></textarea> 
								</p>
							</td> 
						</tr>
						
					</table>
					
					</fieldset>
					
					<?php campaignpress_hook_after_client_details(); ?>
					
					
				</form>
				
				<p><?php campaignpress_footer(); ?></p>
				
				</div>

			</div>
		
		</div>
		
		
		
	</div>