
	<div class="wrap campaignpressadmin">

	<?php screen_icon( 'campaignpress-addons' ); ?>

	<h2><?php _e( 'Add-Ons', 'campaignpress' ); ?></h2>

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
				<th scope="col" class="manage-column" style="width:150px;"><?php _e( 'Name', 'campaignpress' ); ?></th> 
				<th scope="col" class="manage-column" style="width:150px;"><?php _e( 'Installed', 'campaignpress' ); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e( 'Description', 'campaignpress' ); ?></th> 
			</tr> 
		</thead> 
		
		<tfoot> 
			<tr class="thead"> 
				<th scope="col" class="manage-column" style=""><?php _e( 'Name', 'campaignpress' ); ?></th> 
				<th scope="col" class="manage-column" style=""><?php _e( 'Installed', 'campaignpress' ); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e( 'Description', 'campaignpress' ); ?></th> 
			</tr> 
		</tfoot> 
		
		<tbody id="addons" class="list:addon addon-list">
		<?php foreach( $addons as $addon ) { 
					$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
					$style_second = ( ' class="alternate" style="border: none;"' == $style_second ) ? '' : ' class="alternate" style="border: none;"';
					
					// check if installed
					$installed = $addon->addon_is_installed_html();
					if( ! $installed ) {
						$installed = '<a href="' . $addon->url . '">' . __( 'Get Add-On', 'campaignpress' ) . '</a>';
					}
					//$latest_version = $addon->latest_version();
					
		?>
			<tr id="addon-<?php echo $addon->code; ?>" <?php echo $style; ?>>
				<td>
					<?php echo esc_html( $addon->name ); ?>
				</td>
				<td><?php echo $installed; ?></td>
				<td><?php echo esc_html( $addon->description ); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<p><?php campaignpress_footer(); ?></p>
	
	</div>
