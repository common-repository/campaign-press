
	<?php 
	
	// temporary - while we can't update access & billing details through the API
	$name_disabled = '';
	if ( ! $can_edit ) {
		$name_disabled = 'disabled="disabled" readonly="readonly"';
	}
	
	$disabled = '';
	if ( ! $can_edit || ( ! campaignpress_client_can_update_access() && ( isset( $group_id ) && 0 < $all_clients_count ) ) ) {
		$can_edit = false;
		$disabled = 'disabled="disabled" readonly="readonly"';
	}
	
	?>

	<div class="wrap campaignpressadmin">
	
		<div id="delete-group-dialog" class="dialog-content">
			<div class="dialog-error-box"></div>
			<p>
				<?php echo esc_html( $group->name ); ?> <?php _e( 'will be deleted permanently', 'campaignpress' ); ?>.
			</p>
			<p class="dialog-buttons">
				<span class="delete-group-button button"><?php _e( 'Delete', 'campaignpress' ); ?></span>
				<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="delete-group-loading offset-ajax-loading" style="display:none;" alt="" /> 
				<span class="delete-group-cancel-block"><?php _e( 'or', 'campaignpress' ); ?> <a href="#" class="delete-group-cancel-text"><?php _e( 'cancel', 'campaignpress' ); ?></a></span>
			</p>
		</div>
	
		<?php screen_icon( 'campaignpress-group' ); ?>

		<?php if( 'new' != $group_id ) { ?>
			<h2><a href="?page=<?php echo $plugin_page; ?>"><?php _e( 'Groups', 'campaignpress' ); ?></a> &#187; <span id="group_name_heading"><?php echo esc_html( $group->name ); ?></span></h2>
		<?php } else { ?>
			<h2><a href="?page=<?php echo $plugin_page; ?>"><?php _e( 'Groups', 'campaignpress' ); ?></a> &#187; <?php _e( 'Add Group', 'campaignpress' ); ?></h2>
		<?php } ?>
		
		<?php
			if( campaignpress_config_has_errors() && isset( $group_id ) && 'new' != $group_id ) {
				campaignpress_display_config_errors();
				?>
				<p><?php _e( 'You must fix the issue(s) by visiting', 'campaignpress' ) ?> <a href="<?php echo esc_url( admin_url( "admin.php?page=campaignpress-settings" ) ); ?>" ><?php _e( 'settings', 'campaignpress' ); ?></a> <?php _e( 'before using CampaignPress', 'campaignpress' ) ?>.</strong></p>
				<?php
				return;
			}
		?>
		
		<div class="error" id="group-error-box" style="display: none;">
		</div>
		
		<?php if( isset( $_GET['message'] ) ) { ?>
			<?php switch( $_GET['message'] ) { 
				case '2': ?>
					<div class="updated">
						<p><strong><?php _e( 'Group saved', 'campaignpress'); ?>.</strong></p>
					</div>
				<?php break; ?>
			<?php } ?>
		<?php } ?>
		
		<!-- We're piggy backing on WP's post styles here for to shortcut -->
		<div id="poststuff" class="metabox-holder has-right-sidebar"> 
		
			<div class="inner-sidebar">  
				
				<?php if( $can_edit || ! $name_disabled ) { ?>
					<fieldset>
						<legend><?php _e( 'Control', 'campaignpress' ); ?></legend>
						<div class="inside"> 
								<?php if( strlen( $group->name ) > 0 && $can_edit ) { ?>
									<div class="misc-pub-section">
										<a href="#" id="dialoglink-delete-group"><div class="ui-icon ui-icon-trash" style="float: left;" ></div> <?php _e( 'Delete', 'campaignpress' ); echo ' ' . esc_html( $group->name ); ?></a>
									</div>
								<?php } ?>
								<div class="misc-pub-section">
									<p>
										<input name="Submit" type="submit" class="button-primary" id="campaignpress-group-submit" value="<?php _e('Save Changes', 'campaignpress' ); ?>" />
										<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="campaignpress-group-ajax-loading" class="offset-ajax-loading" style="display: none;" alt="" />
									</p> 
								</div>
							<div class="clear"></div>
						</div> 
					</fieldset>
				<?php } ?>
			
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
							<?php _e( 'No activity recorded against this group.', 'campaignpress' ); ?>
						<?php } ?>
						<div class="clear"></div>
					</div> 
				</fieldset>
				
				<fieldset>
					<legend><?php _e( 'Clients', 'campaignpress' ); ?></legend>
					<div class="inside"> 
						<?php if( $all_clients_count > 0 ) { ?>
								<?php foreach( $all_clients as $client ) { ?>
									<div class="misc-pub-section">
										<a href="<?php echo esc_url( admin_url( "admin.php?page=campaignpress-clients&client=$client->id" ) ); ?>"><?php echo $client->company; ?></a>
									</div>
								<?php } ?>
						<?php } else { ?>
							<?php _e( 'No clients belong to this group.', 'campaignpress' ); ?>
						<?php } ?>
						<div class="clear"></div>
					</div> 
				</fieldset>

			</div> 
			
			<div id="post-body"> 
				<div id="post-body-content"> 
							
					<form id="" action="" method="post">
					<input type="hidden" id="group_id" name="group_id" value="<?php echo $group_id; ?>" /> 
					<fieldset>
					<legend><?php _e( 'Group Details', 'campaignpress' ); ?></legend>
					<table class="form-table"> 
						<tr valign="top"> 
							<th scope="row"><label for="group_name"><?php _e( 'Name', 'campaignpress' ); ?></label></th> 
							<td><input name="group_name" type="text" id="group_name" value="<?php echo esc_html( $group->name ); ?>" class="regular-text" <?php echo $name_disabled; ?> /></td> 
						</tr>
						<tr valign="top"> 
							<th scope="row"><label for="group_desc"><?php _e( 'Description', 'campaignpress' ); ?></label></th> 
							<td><textarea name="group_desc" rows="4" cols="40" id="group_desc" class="large-text" <?php echo $name_disabled; ?>><?php echo esc_html( $group->desc ); ?></textarea></td> 
						</tr>
						<tr valign="top"> 
							<th scope="row"><label><?php _e( 'Access', 'campaignpress' ); ?></label></th> 
							<td>
								
								<ul>
									<li><input name="group_access_reports" type="checkbox" id="group_access_reports" value="1" <?php if( '1' == $group->access_reports ) { echo 'checked'; } ?> <?php echo $disabled; ?> /> <label class="checkbox" for="group_access_reports"><?php _e( 'Reports', 'campaignpress' ); ?></label></li>
									
									<li><input name="group_access_sub_mgmt" type="checkbox" id="group_access_sub_mgmt" value="2" <?php if( '2' == $group->access_sub_mgmt ) { echo 'checked'; } ?> <?php echo $disabled; ?> /> <label class="checkbox" for="group_access_sub_mgmt"><?php _e( 'Subscriber Management', 'campaignpress' ); ?></label></li>
									
									<li><input name="group_access_sub_imports" type="checkbox" id="group_access_sub_imports" value="16" <?php if( '16' == $group->access_sub_imports ) { echo 'checked'; } ?> <?php echo $disabled; ?> /> <label class="checkbox" for="group_access_sub_imports"><?php _e( 'Subscriber Imports', 'campaignpress' ); ?></label></li>
									
									<li><input name="group_access_camp_create_send" type="checkbox" id="group_access_camp_create_send" value="4" <?php if( '4' == $group->access_camp_create_send ) { echo 'checked'; } ?> <?php echo $disabled; ?> /> <label class="checkbox" for="group_access_camp_create_send"><?php _e( 'Create/Send Campaigns', 'campaignpress' ); ?></label></li>
									
									<li><input name="group_access_camp_import" type="checkbox" id="group_access_camp_import" value="32" <?php if( '32' == $group->access_camp_import ) { echo 'checked'; } ?> <?php echo $disabled; ?> /> <label class="checkbox" for="group_access_camp_import"><?php _e( 'Campaign Imports', 'campaignpress' ); ?></label></li>
									
									<li><input name="group_access_spam" type="checkbox" id="group_access_spam" value="8" <?php if( '8' == $group->access_spam ) { echo 'checked'; } ?> <?php echo $disabled; ?> /> <label class="checkbox" for="group_access_spam"><?php _e( 'Design and Spam Tests', 'campaignpress' ); ?></label></li>
									
								</ul>

							</td> 
						</tr>
						
					</table>
					
					<div id="billing_defaults">
					
					<table class="form-table"> 
						<tr valign="top"> 
							<th scope="row"><label for="group_billing_type"><?php _e( 'Billing Type', 'campaignpress' ); ?></label></th> 
							<td>
							<select name="group_billing_type" id="group_billing_type" <?php echo $disabled; ?>> 
								<option <?php if( 'UserPaysOnClientsBehalf' == $group->billing_type ) { echo 'selected="selected"'; } ?> value='UserPaysOnClientsBehalf'><?php _e( "We pay on client's behalf", 'campaignpress' ); ?></option> 
								<option <?php if( 'ClientPaysAtStandardRate' == $group->billing_type ) { echo 'selected="selected"'; } ?> value='ClientPaysAtStandardRate'><?php _e( 'Client pays standard rate', 'campaignpress' ); ?></option> 
								<option <?php if( 'ClientPaysWithMarkup' == $group->billing_type ) { echo 'selected="selected"'; } ?> value='ClientPaysWithMarkup'><?php _e( 'Client pays marked-up rate', 'campaignpress' ); ?></option> 
							</select> 
							</td>
						</tr>
					</table>
					
					<div id="billing_currency" style="display: none;">
					<table class="form-table"> 
						<tr valign="top"> 
							<th scope="row"><label for="group_billing_currency"><?php _e( 'Currency', 'campaignpress' ); ?></label></th> 
							<td>
							<select name="group_billing_currency" id="group_billing_currency" <?php echo $disabled; ?>> 
								<?php foreach( $currencies as $currency ) { ?>
									<option <?php if( $currency['code'] == $group->billing_currency ) { echo 'selected="selected"'; } ?> value='<?php echo $currency['code']; ?>' id="<?php echo $currency['code']; ?>_desc"></option> 
								<?php } ?>
							</select> 
							</td>
						</tr>
					</table>
					</div>
					
					<div id="billing_fees" style="display: none;">
						<table class="form-table"> 
							<tr valign="top"> 
									<th scope="row"><label for="group_billing_delivery_fee"><?php _e( 'Delivery Fee', 'campaignpress' ); ?></label></th> 
									<td><div id="currency_delivery" class="currency_major"></div><input name="group_billing_delivery_fee" type="text" id="group_billing_delivery_fee" value="<?php echo esc_html( $group->billing_delivery_fee ); ?>" class="small-text" <?php echo $disabled; ?> /></td>
							</tr>
							<tr valign="top"> 
									<th scope="row"><label for="group_billing_recipient_fee"><?php _e( 'Fee Per Recipient', 'campaignpress' ); ?></label></th> 
									<td><div class="currency_major"></div><input name="group_billing_recipient_fee" type="text" id="group_billing_recipient_fee" value="<?php echo esc_html( $group->billing_recipient_fee ); ?>" class="small-text" <?php echo $disabled; ?> /><span id="currency_recipient" class="currency_minor"></span></td>
							</tr>
						</table>
						
						<div id="billing_fees_spamtest">
						<table class="form-table"> 
							<tr valign="top"> 
									<th scope="row"><label for="group_billing_spamtest_fee"><?php _e( 'Design &amp; Spam Test Fee', 'campaignpress' ); ?></label></th> 
									<td><div id="currency_spamtest" class="currency_major"></div><input name="group_billing_spamtest_fee" type="text" id="group_billing_spamtest_fee" value="<?php echo esc_html( $group->billing_spamtest_fee ); ?>" class="small-text" <?php echo $disabled; ?> /></td>
							</tr>
						</table>
						</div>
						
					</div>
					
					
					</div>
					
					</fieldset>

					</form>

					<p><?php campaignpress_footer(); ?></p>
							
				</div>
			</div>
		
		</div>
		
	</div>