
	<?php 
	
	$disabled = '';
	if ( ! $can_edit ) {
		$disabled = 'disabled="disabled" readonly="readonly"';
	}
	
	?>

	<div class="wrap campaignpressadmin">
	 
	<?php screen_icon( 'campaignpress-clients' ); ?>
	
	<h2><?php _e( 'Groups', 'campaignpress' ); ?> <a href="<?php echo esc_url( admin_url( "admin.php?page=$plugin_page&group=new" ) ); ?>" class="button add-new-h2"><?php _e( 'Add New', 'campaignpress' ); ?></a></h2>
	
	<?php if( isset( $_GET['message'] ) ) { ?>
		<?php switch( $_GET['message'] ) { 
			case '1': ?>
				<div class="updated">
					<p><strong><?php _e( 'Group added', 'campaignpress'); ?>.</strong></p>
				</div>
			<?php break; ?>
			<?php case '3': ?>
				<div class="updated">
					<p><strong><?php _e( 'Group deleted', 'campaignpress'); ?>.</strong></p>
				</div>
			<?php break; ?>
		<?php } ?>
	<?php } ?>
	
	<?php if ( 0 == $all_groups_count ) { ?>

		<div class='updated'>
			<p><?php _e( 'You have no Access &amp; Billing Groups set up in Campaign Press.  Please', 'campaignpress' ) ?> <a href="<?php echo esc_url( admin_url( "admin.php?page=$plugin_page&group=new" ) ); ?>" ><?php _e( 'add a group', 'campaignpress' ) ?></a> <?php _e( 'or', 'campaignpress' ) ?> <a href="<?php echo esc_url( admin_url( "admin.php?page=campaignpress-settings" ) ); ?>" ><?php _e( 'go to settings', 'campaignpress' ) ?></a> <?php _e( 'to Sync your existing groups with Campaign Monitor', 'campaignpress' ) ?>.</p>
		</div> 
			
	<?php } else { ?>
	
		<div class="filter"> 
		<ul class="subsubsub"> 
			<li>
				<a href='?page=<?php echo $plugin_page;?>' class="current">All <span class="count">(<?php echo $all_groups_count; ?>)</span></a>
			</li>
		</ul>
		</div>	
		

			<table class="widefat fixed" cellspacing="0"> 
			<thead> 
			<tr class="thead"> 
				<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th> 
				<th scope="col" id="name" class="manage-column" style=""><?php _e( 'Name', 'campaignpress' ); ?></th> 
				<th scope="col" id="clients" class="manage-column" style=""><?php _e( 'Clients', 'campaignpress' ); ?></th> 
				<th scope="col" id="access_level" class="manage-column" style=""><?php _e( 'Access Level', 'campaignpress' ); ?></th> 
				<th scope="col" id="description" class="manage-column" style=""><?php _e( 'Description', 'campaignpress' ); ?></th> 
			</tr> 
			</thead> 
			 
			<tfoot> 
			<tr class="thead"> 
				<th scope="col"  class="manage-column column-cb check-column" style=""><input type="checkbox" /></th> 
				<th scope="col"  class="manage-column" style=""><?php _e( 'Name', 'campaignpress' ); ?></th> 
				<th scope="col"  class="manage-column" style=""><?php _e( 'Clients', 'campaignpress' ); ?></th> 
				<th scope="col"  class="manage-column" style=""><?php _e( 'Access Level', 'campaignpress' ); ?></th> 
				<th scope="col"  class="manage-column" style=""><?php _e( 'Description', 'campaignpress' ); ?></th> 
			</tr> 
			</tfoot> 

			<tbody id="groups" class="list:group group-list">
			
			<?php 	

				$style = '';
				foreach ($all_groups as $group) { 
					$client_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) as Counter FROM $campaignpress->client_table_name WHERE group_id = %d;", $group->id ) );
					$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
					$group_link = "?page=$plugin_page&group=$group->id";
			?>

					<tr id="client-<?php echo $group->id; ?>" <?php echo $style; ?>>
						<th scope='row' class='check-column'>
							<input type='checkbox' name='campaignpress-groups[]' id='client_<?php echo esc_html( $group->id ); ?>' class='subscriber' value='<?php echo esc_html( $group->id ); ?>' />
						</th>
						<td><a href="<?php echo $group_link; ?>"><?php echo esc_html( $group->name ); ?></a></td>
						<td><?php echo $client_count; ?></a></td>
						<td><?php echo esc_html( $group->access_level ); ?></td>
						<td><?php echo strlen( $group->description ) > $desc_len ? substr( esc_html( $group->description ), 0, $desc_len) . '...' : esc_html( $group->description ); ?></td>
					</tr>
		
			<?php	} ?>

			</tbody>

		</table>
		
	<?php	} ?>

	<p><?php campaignpress_footer(); ?></p>
	
	</div>
	
	