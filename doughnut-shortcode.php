<?php
/*
    Total Users Pro Shortcode
    http://zourbuth.com/plugins/total-users-pro
    Copyright 2012  zourbuth.com  (email : zourbuth@gmail.com)

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


/*
 * Main function to generate shortcode using total_users_pro() function
 * See $defaults arguments for using total_users_pro() function
 * Shortcode does not generate the custom style and script */
function doughnut_post_sc($atts, $content) {
	extract( shortcode_atts( array( 
		'id' => ''
	), $atts )); 

	if( $id )
		$html = doughnut($id);
	else
		$html = __('Please provide an ID.', 'tup');
		
	return $html;	
}
add_shortcode('doughnut', 'doughnut_post_sc');



/**
 * Main function to generate user backend interface
 * See $defaults for function arguments
 */
function doughnut( $id ) {
	
	$widget = get_option('widget_doughnut');
	if( ! isset( $widget[$id] ) )
		return __('Settings not found, please check your settings.', 'tup');
		
	$instance = $widget[$id];
		
		$url_action = $instance['live'] ? 'https://www.paypal.com/webscr' : 'https://www.sandbox.paypal.com/webscr';

		// name, email, tithe, offering, thanksgiving, seed_and_pledges
		$html = '<form action="'.$url_action.'" class="doughnut-form" method="post">

				<!-- Identify your business so that you can collect the payments. -->
				<input type="hidden" name="business" value="'.$instance['email'].'">

				<!-- Specify a Donate button. -->
				<input type="hidden" name="cmd" value="_donations">

				<!-- Specify details about the contribution -->
				<input type="hidden" name="item_name" value="'.$instance['item_name'].'">
				<input type="hidden" name="item_number" value="'.$instance['item_number'].'">
				<input type="hidden" name="amount" value="">
				<input type="hidden" name="custom" value="">
				<input type="hidden" name="currency_code" value="USD">

				<!-- Custom field -->';
				
				if ( ! empty( $instance['fields'] ) ) {
					foreach ( $instance['fields'] as $key => $val ) {
						$name = strtolower(str_replace(' ', '_', $val));
						$html .= '<label>'.$val.'<input type="number" class="doughnut-amount" name="'.$name.'" value="0" /></label>';							
					}
				}
				
				$html .= '<h4 class="doughnut-total">Total <span>0</span></h4>';

				$html .= '<!-- Display the payment button. -->
				<p><input type="submit" value="'.$instance['button_text'].'" /></p>
			</form>';
			
	return $html;
} 
?>