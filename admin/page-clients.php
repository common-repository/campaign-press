
	<?php 
	
	$disabled = '';
	if ( ! $can_edit ) {
		$disabled = 'disabled="disabled" readonly="readonly"';
	}
	
	?>

	<div class="wrap campaignpressadmin">
	 
	<?php screen_icon( 'campaignpress-clients' ); ?>
	
	<h2><?php _e( 'Clients', 'campaignpress' ); ?> <a href="<?php echo esc_url( admin_url( "admin.php?page=$plugin_page&client=new" ) ); ?>" class="button add-new-h2"><?php _e( 'Add Existing', 'campaignpress' ); ?></a></h2>
	
	<?php if( isset( $_GET['message'] ) ) { ?>
		<?php switch( $_GET['message'] ) { 
			case '1': ?>
				<div class="updated">
					<p><strong><?php _e( 'Client added', 'campaignpress'); ?>.</strong></p>
				</div>
			<?php break; ?>
			<?php case '3': ?>
				<div class="updated">
					<p><strong><?php _e( 'Client deleted', 'campaignpress'); ?>.</strong></p>
				</div>
			<?php break; ?>
		<?php } ?>
	<?php } ?>
	
	<?php if ( 0 == $states['All'] ) { ?>

		<div id='setting-error-updated' class='updated settings-error'>
			<p><?php printf( __( 'You have no clients recorded in Campaign Press.  You can either wait for a sign up, %s or %s to Sync your existing clients with Campaign Monitor if you have the CM Sync add-on', 'campaignpress' ), '<a href="' . esc_url( admin_url( "admin.php?page=$plugin_page&client=new" ) ) . '">' . __( 'add an existing client manually', 'campaignpress' ) . '</a>', '<a href="' . esc_url( admin_url( "admin.php?page=campaignpress-settings" ) ) . '">' . __( 'go to settings', 'campaignpress' ) . '</a>' ); ?>.</p>
		</div> 
			
	<?php } else { ?>

		<div class="filter"> 
		<ul class="subsubsub"> 
		
	<?php 	
				
				$status_count = count( $states );
				$status_i = 0;
				$status_link = '';
				foreach ( $states as $status => $count ) { 
					$status_i++;
					$status_link = '';
					$status_class = '';
					if ( $status != 'All' )
						$status_link = "&status=$status"; 
					
					if ( $current_status == $status )
						$status_class = ' class="current"';
	?>
	
						<li>
						<a href='?page=<?php echo $plugin_page; echo $status_link; ?>' <?php echo $status_class; ?>><?php echo $status; ?> <span class="count">(<?php echo $count; ?>)</span></a>
	
	<?php 		if ( $status_i < $status_count ) { ?>
	 |
	<?php 		} ?>					
						</li>	
	<?php		} ?>		
			
		</ul>
		</div> 

		<table class="widefat fixed" cellspacing="0"> 
			<thead> 
			<tr class="thead"> 
				<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th> 
				<th scope="col" id="company" class="manage-column" style=""><?php _e( 'Company', 'campaignpress' ); ?></th> 
				<th scope="col" id="contact_name" class="manage-column" style=""><?php _e( 'Contact Name', 'campaignpress' ); ?></th> 
				<th scope="col" id="email" class="manage-column" style=""><?php _e( 'E-Mail', 'campaignpress' ); ?></th> 
				<th scope="col" id="status" class="manage-column" style=""><?php _e( 'Status', 'campaignpress' ); ?></th> 
			</tr> 
			</thead> 
			 
			<tfoot> 
			<tr class="thead"> 
				<th scope="col"  class="manage-column column-cb check-column" style=""><input type="checkbox" /></th> 
				<th scope="col"  class="manage-column" style=""><?php _e( 'Company', 'campaignpress' ); ?></th> 
				<th scope="col"  class="manage-column" style=""><?php _e( 'Contact Name', 'campaignpress' ); ?></th> 
				<th scope="col"  class="manage-column" style=""><?php _e( 'E-Mail', 'campaignpress' ); ?></th> 
				<th scope="col"  class="manage-column" style=""><?php _e( 'Status', 'campaignpress' ); ?></th> 
			</tr> 
			</tfoot> 

			<tbody id="clients" class="list:client client-list">
			
	<?php 	

				$style = '';
				foreach ($clients as $client) { 
					$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
					$client_link = "?page=$plugin_page&client=$client->id";
	?>

					<tr id="client-<?php echo $client->id; ?>" <?php echo $style; ?>>
						<th scope='row' class='check-column'>
							<input type='checkbox' name='campaignpress-clients[]' id='client_<?php echo esc_html( $client->id ); ?>' class='subscriber' value='<?php echo esc_html( $client->id ); ?>' />
						</th>
						<td><a href="<?php echo $client_link; ?>"><?php echo esc_html( $client->company ); ?></a></td>
						<td><?php echo esc_html( $client->contact_name ); ?></td>
						<td><?php echo esc_html( $client->email ); ?></td>
						<td><?php echo esc_html( $client->status ); ?></td>
					</tr>
		
	<?php		} ?>

			</tbody>

		</table>

	<?php	} ?>

	<p><?php campaignpress_footer(); ?></p>
	
	</div>
	
	