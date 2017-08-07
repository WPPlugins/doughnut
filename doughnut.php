<?php
 /*
	Plugin Name: Doughnut - PayPal Donation Plugin
	Plugin URI: http://zourbuth.com/archives/852/doughnut-paypal-donation-for-wordpress-plugin/
	Description: A powerfull plugin, easy to use with simple setup to add a PayPal donation with custom fields as a widget or shortcode. 
	Version: 0.0.1
	Author: zourbuth
	Author URI: http://zourbuth.com
	License: Under GPL2

	Copyright 2012 zourbuth (email : zourbuth@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// Launch the plugin
add_action( 'plugins_loaded', 'doughnut_plugins_loaded' );


/* 
 * Load the plugin and some predifine constant with plugins_loaded action
 * Load the widget using the widgets_init
 * Since 1.0
 */
function doughnut_plugins_loaded() {
	define( 'DOUGHNUT_VERSION', '1.0' );
	define( 'DOUGHNUT_DIR', plugin_dir_path( __FILE__ ) );
	define( 'DOUGHNUT_URL', plugin_dir_url( __FILE__ ) );

	// require_once( DOUGHNUT_DIR . 'doughnut-utility.php' );
	require_once( DOUGHNUT_DIR . 'doughnut-shortcode.php' );

	add_action( 'widgets_init', 'doughnut_widgets_init' );
}


/* 
 * Register the extra widgets for further hook to widget_init
 * Load necessary widget file
 * Since 1.0
 */
function doughnut_widgets_init() {
	require_once( DOUGHNUT_DIR . 'doughnut-widget.php' );
	register_widget( 'Doughnut_Widget' );
}
?>