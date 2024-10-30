
	function campaignpress_force_location ( new_location ) {
		if( window.location.href == new_location ) {
			window.location.reload();
		} else {
			window.location = new_location;
		}
	}
	
	String.prototype.capitalize = function(){
		return this.replace( /(^|\s)([a-z])/g , function(m,p1,p2){ return p1+p2.toUpperCase(); } );
	};
	
	jQuery(document).ready(function($) {
	
		/**
		* General goodies
		*/

		jQuery( "[id^='dialoglink-']" ).click( function() {
			// check which dialog we're opening
			var orig_id = jQuery( this ).attr( 'id' );
			var id = orig_id.replace( 'dialoglink-', '' );
			var class_name = id.split( '-' )[0];
			var dialog_id = '#' + id + '-dialog';
			var dialog_title = id.replace( '-', ' ').capitalize();
			jQuery( dialog_id ).dialog(
													{
														title: dialog_title,
														dialogClass: class_name,
														autoOpen: false,
														draggable: false,
														modal: true,
														resizable: false
													}
												);
			// show the dialog
			jQuery( dialog_id ).dialog( 'open' );
			
			// link cancel text to dialog
			jQuery( '.' + id + '-cancel-text' ).click( function() {
				jQuery( dialog_id ).dialog( 'close' );
				return false;
			});
			
			return false;
		});
		
		
		
		
		/**
		* Tabs on settings page
		*/

		jQuery( '#settings-tabs' ).bind('tabsselect', function(event, ui) {
			jQuery( "#_new_wp_http_referer" ).val( jQuery( "#_orig_wp_http_referer").val() + ui.tab.hash );
			document.location.hash = ui.tab.hash;
			jQuery( '.updated' ).fadeOut( 'fast' );
		});
		
		jQuery( '#settings-tabs' ).tabs({ fx: { opacity: 'toggle' } });  
		if( document.location.hash != '' ) {
			tabSelect = document.location.hash.substr( 1, document.location.hash.length );
			jQuery( "#settings-tabs" ).tabs( 'select', tabSelect );
			jQuery( "#_new_wp_http_referer" ).val( jQuery( "#_orig_wp_http_referer").val() + '#' + tabSelect );
		}
		
		
		/**
		* Sign Up Fields UI functionality
		*/
		
		var field_positions_updated = false;
		
		function campaignpress_update_field_positions () {
		
			if( ! field_positions_updated ) {
				
				field_positions_updated = true;

				// disable the sortable
				jQuery( ".connectedSortable" ).sortable( "option", "disabled", true );
			
				// determine the new positions
				var fields = new Array();
				var current_pos = 0;
				jQuery("#displayed-fields > li").each( function() {
					fields[current_pos] = jQuery( this ).attr( 'id' ).replace( 'custom-field-', '' );
					current_pos++;
				});

				// implode
				var fields_string = fields.join(',')

				// do ajax call
				var data = {
					action: 'campaignpress_update_field_positions',
					nonce: CampaignPress.nonce,
					fields: fields_string
				};
				
				jQuery.post(CampaignPress.ajaxurl, data, function(response) {
					if( true != response ) {
						jQuery( '#custom-error-text' ).html( response );
						jQuery( '#custom-error-box' ).fadeIn( 'slow' );
					}
				});

				// re-enable sortable
				jQuery( ".connectedSortable" ).sortable( "option", "disabled", false );
				
				field_positions_updated = false;
			}
		}
		
		jQuery( '#displayed-fields, #hidden-fields' ).sortable( 
																						{ 
																							stop: campaignpress_update_field_positions,
																							opacity: 0.7,
																							placeholder: 'placeholder',
																							cancel: '.static, .editmode',
																							cursor: 'move',
																							dropOnEmpty: true,
																							connectWith: '.connectedSortable'
																					   } 
																					).disableSelection();
	
		/**
		* Other UI functionality
		*/
		
		// billing stuff
		var currencies = [];
		if( 'undefined' != typeof( CampaignPress.currencies ) ) {
			var currency_codes = CampaignPress.currencies.split( ',' );
			for( var key in currency_codes ) {
				var code = currency_codes[key];
				var values = CampaignPress["currency_" + code].split( ',' );
				currencies[code] = [];
				currencies[code]['code'] = values[0];
				currencies[code]['name'] = values[1];
				currencies[code]['major'] = values[2];
				currencies[code]['minor'] = values[3];
				currencies[code]['delivery'] = values[4];
				currencies[code]['recipient'] = values[5];
				currencies[code]['spamtest'] = values[6];
				currencies[code]['ClientPaysAtStandardRate_desc'] = values[7];
				currencies[code]['ClientPaysWithMarkup_desc'] = values[8];
			}
		}

		check_billing_defaults();
		jQuery('#group_billing_type').change(function() { check_billing_defaults(); });
		
		function check_billing_defaults() {
			var billing_type = jQuery('#group_billing_type').val();
			if ( billing_type ) {
				
				// we're on the right page, get currencies
				switch ( billing_type ) {
					case 'ClientPaysWithMarkup':
					
						for ( var key in currencies ) {
							jQuery( "#" + key + "_desc" ).html( currencies[key]['ClientPaysWithMarkup_desc'] );
						}
						jQuery("#billing_currency").show( 'fast' );
						jQuery("#billing_fees").show( 'fast' );
						break;
						
					case 'ClientPaysAtStandardRate': 
					
						for ( var key in currencies ) {
							jQuery( "#" + key + "_desc" ).html( currencies[key]['ClientPaysAtStandardRate_desc'] );
						}
						jQuery("#billing_currency").show( 'fast' );
						jQuery("#billing_fees").hide( 'fast' );
						break;
						
					case 'UserPaysOnClientsBehalf':
					
						jQuery("#billing_currency").hide( 'fast' );
						jQuery("#billing_fees").hide( 'fast' );
						break;
						
				}
			}
		}
		
		// change of currency
		var current_currency = '';
		var current_delivery = '';
		var current_recipient = '';
		var current_spamtest = '';
		check_billing_currency();
		jQuery('#group_billing_currency').change(function() { check_billing_currency(); });
		function check_billing_currency() {
			var billing_currency = jQuery('#group_billing_currency').val();
			if ( billing_currency ) {
				// make sure we don't override values that are already there from saved group
				if( ! current_currency ) {
					current_currency = billing_currency;
					current_delivery = jQuery( '#group_billing_delivery_fee' ).val();
					current_recipient = jQuery( '#group_billing_recipient_fee' ).val();
					current_spamtest = jQuery( '#group_billing_spamtest_fee' ).val();
				}
				// set major & minor goodies
				jQuery( '#currency_delivery' ).html( currencies[billing_currency]['major'] );
				jQuery( '#currency_recipient' ).html( currencies[billing_currency]['minor'] );
				jQuery( '#currency_spamtest' ).html( currencies[billing_currency]['major'] );
				if( jQuery( '#group_id' ).val() && jQuery( '#group_billing_currency' ).val() == current_currency ) {	
					jQuery( '#group_billing_delivery_fee' ).val( current_delivery );
					jQuery( '#group_billing_recipient_fee' ).val( current_recipient );
					jQuery( '#group_billing_spamtest_fee' ).val( current_spamtest );
				} else {
					jQuery( '#group_billing_delivery_fee' ).val( currencies[billing_currency]['delivery'] );
					jQuery( '#group_billing_recipient_fee' ).val( currencies[billing_currency]['recipient'] );
					jQuery( '#group_billing_spamtest_fee' ).val( currencies[billing_currency]['spamtest'] );
				}
			}
		}
		
		// access defaults
		check_access_defaults();
		jQuery('#group_access_sub_mgmt').click(function() { check_access_defaults(); });
		
		function check_access_defaults() {
			if( jQuery('#group_access_sub_mgmt').is(':checked') ) {
				jQuery("#group_access_sub_imports").removeAttr('disabled');
			} else {
				jQuery("#group_access_sub_imports").removeAttr('checked');
				jQuery("#group_access_sub_imports").attr('disabled', true);
			}
			if( jQuery('#group_access_camp_create_send').is(':checked') ) {
				jQuery("#group_access_camp_import").removeAttr('disabled');
				jQuery("#group_access_spam").removeAttr('disabled');
			} else {
				jQuery("#group_access_camp_import").removeAttr('checked');
				jQuery("#group_access_camp_import").attr('disabled', true);
				jQuery("#group_access_spam").removeAttr('checked');
				jQuery("#group_access_spam").attr('disabled', true);
			}
		}
		
		// show/hide billing defaults on settings page
		jQuery('#group_access_camp_create_send').is(':checked') ? jQuery("#billing_defaults").show() : jQuery("#billing_defaults").hide();
		jQuery('#group_access_camp_create_send').click(function() {
			jQuery("#billing_defaults").toggle( 'fast' );
			check_access_defaults();
		});
		
		// spam test fee
		jQuery('#group_access_spam').is(':checked') ? jQuery("#billing_fees_spamtest").show() : jQuery("#billing_fees_spamtest").hide();
		jQuery('#group_access_spam').click(function() {
			jQuery("#billing_fees_spamtest").toggle( 'fast' );
		});
		
		// show/hide elements on notifications page
		jQuery(".toggle-control").each( function() {
			var item_id = '#' +  jQuery( this ).attr( 'id' ) + '-item';
			jQuery( this ).is(':checked') ? jQuery( item_id ).show() : jQuery( item_id ).hide();
			jQuery( this ).click(function() {
				jQuery( item_id ).toggle( 'fast' );
			});
		});
		
		// approval button on client page
		jQuery('#campaignpress-client-approve-button').click(function($) {
			toggle_approval_confirmation();
			return false;
		});
		
		jQuery('#campaignpress-client-approve-cancel-text').click(function($) {
			toggle_approval_confirmation();
			return false;
		});
		
		function toggle_approval_confirmation() {
			if (jQuery('#campaignpress-client-approve-confirm-box').css('display') == 'none') {
				// first hide the approval button
				jQuery('#campaignpress-client-approve-button').fadeOut('fast', function() {
					// then display the other ones
					jQuery('#campaignpress-client-approve-confirm-box').fadeIn('fast');
				});
			} else {
				// first the confirmation stuff
				jQuery('#campaignpress-client-approve-confirm-box').fadeOut('fast', function() {			
					// then the original button
					jQuery('#campaignpress-client-approve-button').fadeIn('fast');
				});
			}
		}
				
		
		/**
		* Groups
		*/
		
		var group_deleted = false;
		
		jQuery('.delete-group-button').click(function($) {
			campaignpress_delete_group();
			return false;
		});
		
		function campaignpress_delete_group() {
			
			if( ! group_deleted ) {
				
				group_deleted = true;
				
				// determine which group we're deleting
				var group_id = jQuery( '#group_id' ).val();

				jQuery( '.delete-group-loading' ).css( 'display', 'inline' );
				jQuery( '.delete-group-button' ).css('color','#ccc');
				jQuery( '.delete-group-cancel-block' ).css( 'display', 'none' );

				var data = {
					action: 'campaignpress_delete_group',
					nonce: CampaignPress.nonce,
					group_id: group_id
				};

				jQuery.post(CampaignPress.ajaxurl, data, function(response) {
					if( true == response ) {
						campaignpress_force_location( CampaignPress.adminurl + '?page=campaignpress-groups&message=3' );
					} else {
						// we have problems
						jQuery( '.delete-group-loading' ).css( 'display', 'none' );
						jQuery( '.delete-group-button' ).css( 'color', '#464646' );
						jQuery( '.delete-group-cancel-block' ).css( 'display', 'inline' );
						group_deleted = false;
						jQuery( '.dialog-error-box' ).html( '<p><strong>' + CampaignPress.errormsg + ':</strong></p><p><ul>' + response + '</ul></p>' );
						jQuery( '.dialog-error-box' ).fadeIn( 'slow' );
					}
				});
			}
		}

		var group_saved = false;
		
		jQuery('#campaignpress-group-submit').click(function($) {
			campaignpress_save_group();
			return false;
		});
		
		function campaignpress_save_group() {
			
			if( ! group_saved ) {
			
				group_saved = true;
				
				jQuery('#campaignpress-group-ajax-loading').css( 'display', 'inline' );
				jQuery('#campaignpress-group-submit').css('color','#7cb8d4');
				jQuery('.updated').fadeOut( 'fast' );
				
				var data = {
					action: 'campaignpress_save_group',
					nonce: CampaignPress.nonce,
					group_id: jQuery("#group_id").val(),
					group_name: jQuery("#group_name").val(),
					group_desc: jQuery("#group_desc").val(),
					group_access_reports: jQuery('#group_access_reports').is(':checked') ? jQuery('#group_access_reports').val() : 0,
					group_access_sub_mgmt: jQuery('#group_access_sub_mgmt').is(':checked') ? jQuery('#group_access_sub_mgmt').val() : 0,
					group_access_sub_imports: jQuery('#group_access_sub_imports').is(':checked') ? jQuery('#group_access_sub_imports').val() : 0,
					group_access_camp_create_send: jQuery('#group_access_camp_create_send').is(':checked') ? jQuery('#group_access_camp_create_send').val() : 0,
					group_access_camp_import: jQuery('#group_access_camp_import').is(':checked') ? jQuery('#group_access_camp_import').val() : 0,
					group_access_spam: jQuery('#group_access_spam').is(':checked') ? jQuery('#group_access_spam').val() : 0,
					group_billing_type: jQuery("#group_billing_type").val(),
					group_billing_currency: jQuery("#group_billing_currency").val(),
					group_billing_delivery_fee: jQuery("#group_billing_delivery_fee").val(),
					group_billing_recipient_fee: jQuery("#group_billing_recipient_fee").val(),
					group_billing_spamtest_fee: jQuery("#group_billing_spamtest_fee").val()
				};

				jQuery.post(CampaignPress.ajaxurl, data, function(response) {
					if( response == 'added' ) {
						campaignpress_force_location( CampaignPress.adminurl + '?page=campaignpress-groups&message=1' );
					} else if( response == 'saved' ) {
						campaignpress_force_location( CampaignPress.adminurl + '?page=campaignpress-groups&group=' + jQuery( '#group_id' ).val() + '&message=2' );
					} else {
						// we either have problems or we've saved it
						jQuery('#campaignpress-group-ajax-loading').css( 'display', 'none' );
						jQuery('#campaignpress-group-submit').css( 'color', '#fff' );
						group_saved = false;
						jQuery('#group-updated-box').fadeOut( 'fast' );
						jQuery('#group-error-box').html( '<p><strong>' + CampaignPress.errormsg + ':</strong></p><p><ul>' + response + '</ul></p>' );
						jQuery('#group-error-box').fadeIn( 'slow' );
					}
				});
			}
		}
		
		
		/**
		* Clients
		*/
		
		var existing_client_added = false;
		
		jQuery('#campaignpress-client-add-existing-submit').click(function($) {
			campaignpress_add_existing_client();
			return false;
		});
		
		function campaignpress_add_existing_client() {
			
			if( ! existing_client_added ) {
				
				existing_client_added = true;
				
				jQuery('#campaignpress-client-ajax-loading').css( 'display', 'inline' );
				jQuery('#campaignpress-client-add-existing-submit').css('color','#7cb8d4');

				var data = {
					action: 'campaignpress_add_existing_client',
					nonce: CampaignPress.nonce,
					api_client_id: jQuery("#api_client_id").val()
				};

				jQuery.post(CampaignPress.ajaxurl, data, function(response) {
					if( 1 == response ) {
						campaignpress_force_location( CampaignPress.adminurl + '?page=campaignpress-clients&message=1' );
					} else {
						// we either have problems or we've saved it
						jQuery('#campaignpress-client-ajax-loading').css( 'display', 'none' );
						jQuery('#campaignpress-client-add-existing-submit').css( 'color', '#fff' );
						existing_client_added = false;
						jQuery('#client-error-box').html( '<p><strong>' + CampaignPress.errormsg + ':</strong></p><p><ul>' + response + '</ul></p>' );
						jQuery('#client-error-box').fadeIn( 'slow' );
					}
				});
			}
		}
		
		var client_welcomed = false;
		
		jQuery('.welcome-client-button').click(function($) {
			campaignpress_welcome_client();
			return false;
		});
		
		function campaignpress_welcome_client() {
			
			if( ! client_welcomed ) {
				
				client_welcomed = true;
				
				// determine which client we're approving
				var client_id = jQuery( '#client_id' ).val();

				jQuery( '.welcome-client-loading' ).css( 'display', 'inline' );
				jQuery( '.welcome-client-button' ).css('color','#ccc');
				jQuery( '.welcome-client-cancel-block' ).css( 'display', 'none' );

				var data = {
					action: 'campaignpress_welcome_client',
					nonce: CampaignPress.nonce,
					client_id: client_id
				};

				jQuery.post(CampaignPress.ajaxurl, data, function(response) {
					if( true == response ) {
						campaignpress_force_location( CampaignPress.adminurl + '?page=campaignpress-clients&client=' + client_id + '&message=4' );
					} else {
						// we have problems
						jQuery( '.welcome-client-loading' ).css( 'display', 'none' );
						jQuery( '.welcome-client-button' ).css( 'color', '#464646' );
						jQuery( '.welcome-client-cancel-block' ).css( 'display', 'inline' );
						client_welcomed = false;
						jQuery( '.dialog-error-box' ).html( '<p><strong>' + CampaignPress.errormsg + ':</strong></p><p><ul>' + response + '</ul></p>' );
						jQuery( '.dialog-error-box' ).fadeIn( 'slow' );
					}
				});
			}
		}
		
		
		var client_deleted = false;
		
		jQuery('.delete-client-button').click(function($) {
			campaignpress_delete_client();
			return false;
		});
		
		function campaignpress_delete_client() {
			
			if( ! client_deleted ) {
				
				client_deleted = true;
				
				// determine which client we're approving
				var client_id = jQuery( '#client_id' ).val();

				jQuery( '.delete-client-loading' ).css( 'display', 'inline' );
				jQuery( '.delete-client-button' ).css('color','#ccc');
				jQuery( '.delete-client-cancel-block' ).css( 'display', 'none' );

				var data = {
					action: 'campaignpress_delete_client',
					nonce: CampaignPress.nonce,
					client_id: client_id
				};

				jQuery.post(CampaignPress.ajaxurl, data, function(response) {
					if( true == response ) {
						campaignpress_force_location( CampaignPress.adminurl + '?page=campaignpress-clients&message=3' );
					} else {
						// we have problems
						jQuery( '.delete-client-loading' ).css( 'display', 'none' );
						jQuery( '.delete-client-button' ).css( 'color', '#464646' );
						jQuery( '.delete-client-cancel-block' ).css( 'display', 'inline' );
						client_deleted = false;
						jQuery( '.dialog-error-box' ).html( '<p><strong>' + CampaignPress.errormsg + ':</strong></p><p><ul>' + response + '</ul></p>' );
						jQuery( '.dialog-error-box' ).fadeIn( 'slow' );
					}
				});
			}
		}
		
		var client_approved = false;
		
		jQuery('.approve-client-button').click(function($) {
			campaignpress_approve_client();
			return false;
		});
		
		function campaignpress_approve_client() {
			
			if( ! client_approved ) {
				
				client_approved = true;
				
				// determine which client we're approving
				var client_id = jQuery( '#client_id' ).val();

				jQuery( '.approve-client-loading' ).css( 'display', 'inline' );
				jQuery( '.approve-client-button' ).css('color','#ccc');
				jQuery( '.approve-client-cancel-block' ).css( 'display', 'none' );

				var data = {
					action: 'campaignpress_approve_client',
					nonce: CampaignPress.nonce,
					client_id: client_id
				};

				jQuery.post(CampaignPress.ajaxurl, data, function(response) {
					if( true == response ) {
						campaignpress_force_location( CampaignPress.adminurl + '?page=campaignpress-clients&client=' + client_id + '&message=3' );
					} else {
						// we have problems
						jQuery( '.approve-client-loading' ).css( 'display', 'none' );
						jQuery( '.approve-client-button' ).css( 'color', '#464646' );
						jQuery( '.approve-client-cancel-block' ).css( 'display', 'inline' );
						client_approved = false;
						jQuery( '.dialog-error-box' ).html( '<p><strong>' + CampaignPress.errormsg + ':</strong></p><p><ul>' + response + '</ul></p>' );
						jQuery( '.dialog-error-box' ).fadeIn( 'slow' );
					}
				});
			}
		}

		var client_saved = false;
		
		jQuery('#campaignpress-client-submit').click(function($) {
			campaignpress_save_client();
			return false;
		});
		
		function campaignpress_save_client() {
			
			if( ! client_saved ) {
			
				client_saved = true;
				
				jQuery('#campaignpress-client-ajax-loading').css( 'display', 'inline' );
				jQuery('#campaignpress-client-submit').css('color','#7cb8d4');
				jQuery('.updated').fadeOut( 'fast' );
				
				var data = {
					action: 'campaignpress_save_client',
					nonce: CampaignPress.nonce,
					client_id: jQuery("#client_id").val(),
					client_status: jQuery("#client_status").val(),
					group_id: jQuery("#group_id").val(),
					client_company: jQuery("#client_company").val(),
					client_contact_name: jQuery("#client_contact_name").val(),
					client_email: jQuery("#client_email").val(),
					client_username: jQuery("#client_username").val(),
					client_country: jQuery("#client_country").val(),
					client_timezone: jQuery("#client_timezone").val(),
					client_additional_info: jQuery("#client_additional_info").val()
				};
				
				// do the custom input fields
				jQuery( '#custom-fields input' ).each( function($) {
					var value = '';
					if( jQuery( this ).is(':checkbox') ) {
						if( jQuery( this ).is(':checked') ) {
							value = 1;
						}
					} else {
						// just add the value
						value = jQuery( this ).val();
					}
					data[jQuery( this ).attr('id')] = value;
				});

				// do the custom select fields
				jQuery( '#custom-fields select' ).each( function($) {
					data[jQuery( this ).attr('id')] = jQuery( this ).val();
				});
				
				// do the custom textarea
				jQuery( '#custom-fields textarea' ).each( function($) {
					data[jQuery( this ).attr('id')] = jQuery( this ).val();
				});
				
				jQuery.post(CampaignPress.ajaxurl, data, function(response) {
					if( response == 'added' ) {
						campaignpress_force_location( CampaignPress.adminurl + '?page=campaignpress-clients&message=1' );
					} else if( response == 'saved' ) {
						campaignpress_force_location( CampaignPress.adminurl + '?page=campaignpress-clients&client=' + jQuery( '#client_id' ).val() + '&message=2' );
					} else {
						// we either have problems or we've saved it
						jQuery('#campaignpress-client-ajax-loading').css( 'display', 'none' );
						jQuery('#campaignpress-client-submit').css( 'color', '#fff' );
						client_saved = false;
						jQuery('#client-updated-box').fadeOut( 'fast' );
						jQuery('#client-error-box').html( '<p><strong>' + CampaignPress.errormsg + ':</strong></p><p><ul>' + response + '</ul></p>' );
						jQuery('#client-error-box').fadeIn( 'slow' );
					}
				});
			}
		}
		
	});