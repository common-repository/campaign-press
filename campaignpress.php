<?php
/*
Plugin Name: Campaign Press
Plugin URI: http://floatingmonk.co.nz/campaignpress/
Description: Campaign Press makes it easy to gather sign ups and manage your Campaign Monitor clients through Wordpress. 
Version: 1.0.5
Author: Brendan Kilfoil
Author URI: http://floatingmonk.co.nz
*/

/*

	Copyright (c) 2010, Brendan Kilfoil <brendan@floatingmonk.co.nz>
	All rights reserved.
	
	Campaign Press is distributed under the GNU General Public License, Version 2,
	June 1991. Copyright (C) 1989, 1991 Free Software Foundation, Inc., 51 Franklin
	St, Fifth Floor, Boston, MA 02110, USA

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
	ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	
	Third party components are under their respective licenses.
	
*/

	define( 'CAMPAIGNPRESS_VERSION', '1.0.5' );

	if ( ! defined( 'CAMPAIGNPRESS_PLUGIN_BASENAME' ) )
		define( 'CAMPAIGNPRESS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

	if ( ! defined( 'CAMPAIGNPRESS_PLUGIN_NAME' ) )
		define( 'CAMPAIGNPRESS_PLUGIN_NAME', trim( dirname( CAMPAIGNPRESS_PLUGIN_BASENAME ), '/' ) );

	if ( ! defined( 'CAMPAIGNPRESS_PLUGIN_DIR' ) )
		define( 'CAMPAIGNPRESS_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . CAMPAIGNPRESS_PLUGIN_NAME );

	if ( ! defined( 'CAMPAIGNPRESS_PLUGIN_URL' ) )
		define( 'CAMPAIGNPRESS_PLUGIN_URL', WP_PLUGIN_URL . '/' . CAMPAIGNPRESS_PLUGIN_NAME );
		
	if ( ! defined( 'CAMPAIGNPRESS_ADMIN_READ_CAPABILITY' ) )
		define( 'CAMPAIGNPRESS_ADMIN_READ_CAPABILITY', 'edit_posts' );

	if ( ! defined( 'CAMPAIGNPRESS_ADMIN_READ_WRITE_CAPABILITY' ) )
		define( 'CAMPAIGNPRESS_ADMIN_READ_WRITE_CAPABILITY', 'manage_options' );
		
	if ( ! defined( 'CAMPAIGNPRESS_SHOW_ADDON_ADVERTS' ) )
		define( 'CAMPAIGNPRESS_SHOW_ADDON_ADVERTS', true );	
		
	// start loading
	require_once CAMPAIGNPRESS_PLUGIN_DIR . '/loader.php';

?>