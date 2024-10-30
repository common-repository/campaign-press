
	<?php 
	
	$disabled = '';
	if ( ! $can_edit ) {
		$can_edit = false;
		$disabled = 'disabled="disabled" readonly="readonly"';
	}

	?>

	<div class="wrap campaignpressadmin">

		<?php screen_icon( 'campaignpress-client' ); ?>

		<h2><a href="?page=<?php echo $plugin_page; ?>"><?php _e( 'Clients', 'campaignpress' ); ?></a> &#187; <?php _e( 'Add Existing Client', 'campaignpress' ); ?></h2>

		<div class="error" id="client-error-box" style="display: none;">
		</div>

		<form id="" action="" method="post">
			<fieldset>
			<legend><?php _e( 'Client Details' ); ?></legend>
			<table class="form-table"> 
				<tr valign="top"> 
					<th scope="row"><label for="api_client_id"><?php _e( 'API Client ID', 'campaignpress' ); ?></label></th> 
					<td><input name="api_client_id" type="text" id="api_client_id" value="" class="regular-text" <?php echo $disabled; ?> /> <span class="side-link"><?php printf( __( 'This is available under %s inside Campaign Monitor.', 'campaignpress' ), '<code>Client Settings</code>' ); ?></span></td> 
				</tr>
				<tr valign="top"> 
					<th scope="row"></th> 
					<td>
					<?php if ( $can_edit ) { ?>
								<input name="Submit" type="submit" class="button-primary" id="campaignpress-client-add-existing-submit"  value="<?php echo _e('Add Client', 'campaignpress' ); ?>" />
								<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="campaignpress-client-ajax-loading" class="offset-ajax-loading" style="display: none;" alt="" />
					<?php } ?>
					</td> 
				</tr>
			</table>
			</fieldset>
		</form>

		<p><?php campaignpress_footer(); ?></p>
		
	</div>