
	<div class="wrap campaignpressadmin">

	<?php screen_icon( 'campaignpress-dashboard' ); ?>

	<h2><?php _e( 'Dashboard', 'campaignpress' ); ?></h2>
	
	<fieldset class="dashboard">
	<legend><?php printf( __( 'From the %s', 'campaignpress' ), '<a href="http://floatingmonk.co.nz/campaignpress/blog/">Blog</a>' ); ?></legend>
		<div class="inside">
			<?php foreach ($rss->items as $item) {
				$href = $item['link'];
				$title = $item['title'];
				$date = date( "F j, Y", strtotime( $item['pubdate'] ) );
				$desc = strlen( $item['description'] ) > 60 ? substr( $item['description'], 0, 60) . '...' : $item['description'];
			?>
			<li><?php echo $date; ?> - <a href="<?php echo $href; ?>"><?php echo $title; ?></a> - <?php echo $desc; ?></li>
			<?php } ?>
		</div> 
	</fieldset>

	<fieldset class="dashboard">
	<legend><?php _e( 'Clients Awaiting Approval', 'campaignpress' ); ?></legend>
		<div class="inside">
			<?php if( $all_clients_count > 0 ) { ?>
				<table class="widefat fixed" cellspacing="0"> 
				<thead> 
				<tr class="thead"> 
					<th scope="col" id="company" class="manage-column" style=""><?php _e( 'Company', 'campaignpress' ); ?></th> 
					<th scope="col" id="signed_up" class="manage-column" style=""><?php _e( 'Signed Up', 'campaignpress' ); ?></th> 
					<th scope="col" id="contact_name" class="manage-column" style=""><?php _e( 'Contact Name', 'campaignpress' ); ?></th> 
					<th scope="col" id="email" class="manage-column" style=""><?php _e( 'Email', 'campaignpress' ); ?></th> 
				</tr> 
				</thead> 
					<?php foreach( $all_clients as $client ) { 
								$link = admin_url( 'admin.php?page=campaignpress-clients&client=' . $client->id );
								$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
					?>
						<tr id="client-<?php echo $client->id; ?>" <?php echo $style; ?>>
							<td><a href="<?php echo $link; ?>"><?php echo esc_html( $client->company ); ?></a></td>
							<td><?php echo esc_html( $client->created ); ?></td>
							<td><?php echo esc_html( $client->contact_name ); ?></td>
							<td><?php echo esc_html( $client->email ); ?></td>
						</tr>
					<?php } ?>
				</table>
			<?php } else { ?>
				<?php _e( 'No clients awaiting approval.', 'campaignpress' ); ?>
			<?php } ?>
		</div> 
	</fieldset>
	
	<fieldset class="dashboard">
	<legend><?php _e( 'Latest Activity', 'campaignpress' ); ?></legend>
		<div class="inside">
			<?php if( $all_activity_count > 0 ) { ?>
				<table class="widefat fixed" cellspacing="0"> 
				<thead> 
				<tr class="thead"> 
					<th scope="col" id="occurred" class="manage-column" style=""><?php _e( 'Occurred', 'campaignpress' ); ?></th> 
					<th scope="col" id="type" class="manage-column" style=""><?php _e( 'Type', 'campaignpress' ); ?></th> 
					<th scope="col" id="id" class="manage-column" style=""><?php _e( 'ID', 'campaignpress' ); ?></th> 
					<th scope="col" id="desc" class="manage-column" style=""><?php _e( 'Description', 'campaignpress' ); ?></th> 
				</tr> 
				</thead> 
					<?php foreach( $all_activity as $activity ) { 
								$link = '';
								switch( $activity->item_type )
								{
									case 'client':
										$link = admin_url( 'admin.php?page=campaignpress-clients&client=' . $activity->item_id );
										break;
									
									case 'group':
										$link = admin_url( 'admin.php?page=campaignpress-groups&group=' . $activity->item_id );
										break;
										
									case 'field':
										$link = admin_url( 'admin.php?page=campaignpress-settings' );
										break;
								}
								$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
					?>
						<tr id="activity-<?php echo $activity->id; ?>" <?php echo $style; ?>>
							<td><?php echo esc_html( $activity->occurred ); ?></td>
							<td><?php echo esc_html( $activity->item_type ); ?></td>
							<td><a href="<?php echo $link; ?>"><?php echo esc_html( $activity->item_id ); ?></a></td>
							<td><?php echo esc_html( $activity->description ); ?></td>
						</tr>
					<?php } ?>
				</table>
			<?php } else { ?>
				<?php _e( 'No activity recorded.', 'campaignpress' ); ?>
			<?php } ?>
		</div> 
	</fieldset>
	
	<p><?php campaignpress_footer(); ?></p>
	
	</div>
