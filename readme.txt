=== Campaign Press ===

Contributors: floatingmonk, ryan_marsh
Plugin Name: Campaign Press
Plugin URI: http://floatingmonk.co.nz/campaignpress/
Donate link: http://floatingmonk.co.nz/campaignpress/addons/
Tags: campaign monitor, campaign press, email marketing
Author URI: http://floatingmonk.co.nz/
Author: Brendan Kilfoil of Floating Monk
Requires at least: 3.0
Tested up to: 3.0.3
Stable tag: trunk
Version: 1.0.5

Campaign Press makes it easy to gather sign ups and manage your Campaign Monitor clients through WordPress.


== Description ==

Campaign Press makes it easy to gather sign ups and manage your Campaign Monitor clients through Wordpress.  Campaign Press is in no way affiliated with Campaign Monitor.

For more information, please visit http://floatingmonk.co.nz/campaignpress/.


== Screenshots ==

1. Screenshots can be found at http://floatingmonk.co.nz/campaignpress/overview-and-screenshots/.

== Frequently Asked Questions ==

= Where can I find more information on Campaign Press =

Visit http://floatingmonk.co.nz/campaignpress/ for answers to all your questions.

== Installation ==

1. Extract the zip and upload the entire `campaign-press` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the `Plugins` menu in Wordpress.


== Changelog ==

= 1.0.5 =
* Fixed a bug which would stop required tables from being created in the DB (many thanks to RKB on the forums for his help on this one)
* Fixed a bug where choosing a timezone with an ampersand in it would cause a validation error on sign up.
* Changed the way that addons are included in Campaign Press due to some users having issues with the previous method.

= 1.0.4 =
* Added descriptive error messages for when the plugin doesn't activate.
* Fixed a bug where an array_filter warning would be generated in some circumstances.
* Fixed a bug where running a Sync would always create at least one new group if you pay on behalf of at least one client.

= 1.0.3 =
* Fixed a bug where approving accounts would cause an error in some cases
* Added the ability for password encryption without the mcrypt PHP module - so it's no longer required

= 1.0.2 =
* Fixed a bug that meant the plugin wouldn't activate when running PHP versions older than 5.3.0 (to do with calling static methods).
* Fixed a bug that meant that you couldn't create groups (which meant without the sync plugin you couldn't gather sign ups)

= 1.0.1 =
* Bug fixes

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.3 =
Many bugs have been fixed, support for PHP 5.2.6 added and no more dependency on mcrypt for password encryption.


== Add-Ons ==

Visit http://floatingmonk.co.nz/campaignpress/ to buy & download add-ons.