<?php
/**
 * The Categories widget replaces the default WordPress Categories widget. This version gives total
 * control over the output to the user by allowing the input of all the arguments typically seen
 * in the wp_list_categories() function.
 *
 */
class Doughnut_Widget extends WP_Widget {

	// Setup variable for the widget
	var $prefix;
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 1.0
	 */
	function __construct() {
	
		// Give your own prefix name eq. your-theme-name-
		$this->prefix 		= 'doughnut';
		$this->textdomain 	= 'doughnut';
		
		// Set up the widget options
		$widget_options = array(
			'classname' => $this->prefix, 
			'description' => esc_html__( '[+] Advanced widget for paypal donation.', $this->textdomain )
		);

		// Set up the widget control options
		$control_options = array(
			'width' => 460,
			'height' => 350,
			'id_base' => $this->prefix
		);

		// Create the widget
		$this->WP_Widget( $this->prefix, esc_attr__( 'Doughnut - Paypal Donation', $this->textdomain ), $widget_options, $control_options );
		
		// Load the widget stylesheet for the widgets admin screen
		add_action( 'load-widgets.php', array(&$this, 'doughnut_widget_admin_script_style') );
		add_action('wp_ajax_doughnut_fields', array( &$this, 'doughnut_fields_ajax') );
		
		// Print the user costum style sheet
		if ( is_active_widget( false, false, $this->id_base, false ) && ! is_admin() ) {
			wp_enqueue_style( 'doughnut', DOUGHNUT_URL . 'css/doughnut.css' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'doughnut', DOUGHNUT_URL . 'js/jquery.doughnut.js' );
			add_action( 'wp_head', array( &$this, 'print_script' ) );			
		}
	}
	

	// Push the widget stylesheet widget.css into widget admin page
	function doughnut_widget_admin_script_style() {
		wp_enqueue_style( 'doughnut-dialog', DOUGHNUT_URL . 'css/dialog.css' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'doughnut-dialog', DOUGHNUT_URL . 'js/jquery.dialog.js' );
		wp_localize_script( 'doughnut-dialog', 'doughnut',  array(
			'nonce'		=> wp_create_nonce( 'doughnut-nonce' ),  // generate a nonce for further checking below
			'action'	=> 'doughnut_fields'
		));
	}
	

	// Push the widget stylesheet widget.css into widget admin page
	function doughnut_fields_ajax() {
		// Check the nonce and if not isset the id, just die
		// not best, but maybe better for avoid errors
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'doughnut-nonce' ) )
			die('Invalid nonce');
			
		$this->doughnut_field_form();	
		exit;
	}
	
	
	// Function for creating the sortable fields
	// @param $val empty string if not signed
	function doughnut_field_form( $val = 'Title' ) { ?>
		<ul class="gw-sortable">
			<li>
				<span class="controlDesc"><?php _e( 'Name: ', $this->textdomain ); ?><?php echo strtolower(str_replace(' ', '_', $val)) ; ?></span>				
				<span class="gw-delete"></span>
				<input type="text" class="widefat" name="<?php echo $this->get_field_name( 'fields' ); ?>[]" value="<?php echo $val; ?>" />
			</li>
		</ul><?php
	}
	
	
	/**
	 * Print the custom style and script
	 * @since 1.0
	 */		
	function print_script() {
		$settings = $this->get_settings();
		foreach ($settings as $key => $setting){
			$widget_id = $this->id_base . '-' . $key;
			if( is_active_widget( false, $widget_id, $this->id_base ) ) {
				// Print the widget style adnd script
				echo '<style type="text/css">';
					if ( $setting['bgImage'] )		echo '#' . $this->id . ' {background-image: url(' . $setting['bgImage'] . ')}'; '}';
				echo '</style>';
				
				if ( !empty( $setting['customstylescript'] ) ) echo $setting['customstylescript'];
			}
		}
	}
	
	
	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6.0
	 */
	function widget( $args, $instance ) {
		extract( $args );

		// Output the theme's widget wrapper
		echo $before_widget;	

		// If a title was input by the user, display it
		if ( !empty( $instance['title'] ) )
			echo $before_title . $titleIcon . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		// Print intro text if exist
		if ( !empty( $instance['intro_text'] ) )
			echo '<p class="'. $this->id . '-intro-text intro-text">' . $instance['intro_text'] . '</p>';
		
		$option = get_option('paypal-framework');
		// print_r($option);
		
		$url_action = isset( $instance['live'] ) ? 'https://www.paypal.com/webscr' : 'https://www.sandbox.paypal.com/webscr';

		// name, email, tithe, offering, thanksgiving, seed_and_pledges
		echo '<form action="'.$url_action.'" class="doughnut-form" method="post">

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
							echo '<label>'.$val.'<input type="number" class="doughnut-amount" name="'.$name.'" value="0" /></label>';							
						}
					}
					
					echo '<h4 class="doughnut-total">Total <span>0</span></h4>';

					echo '<!-- Display the payment button. -->
					<p><input type="submit" value="'.$instance['button_text'].'" /></p>
				</form>';
		
		// Print outro text if exist
		if ( !empty( $instance['outro_text'] ) )
			echo '<p class="'. $this->id . '-outro_text outro_text">' . $instance['outro_text'] . '</p>';

		// Close the theme's widget wrapper
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Set the instance to the new instance. */
		$instance = $new_instance;

		$instance['title'] 				= strip_tags( $new_instance['title'] );
		$instance['email'] 				= strip_tags( $new_instance['email'] );
		$instance['live'] 				= isset( $new_instance['live'] ) ? 1 : 0;
		$instance['item_name'] 			= strip_tags( $new_instance['item_name'] );
		$instance['button_text'] 		= strip_tags( $new_instance['button_text'] );
		$instance['item_number'] 		= strip_tags( $new_instance['item_number'] );
		$instance['fields'] 			= $new_instance['fields'];
		$instance['toggle_active'] 		= $new_instance['toggle_active'];
		$instance['intro_text'] 		= $new_instance['intro_text'];
		$instance['outro_text'] 		= $new_instance['outro_text'];
		$instance['customstylescript']	= $new_instance['customstylescript'];
		
		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6.0
	 */
	function form( $instance ) {
		
		// Set up the default form values
		$defaults = array(
			'title' 			=> __( 'PayPal Donation', $this->textdomain ),
			'email' 			=> '',
			'live' 				=> true,
			'fields' 			=> array(),
			'item_name' 		=> __( 'Donation for ', $this->textdomain ) . get_bloginfo( 'name' ),
			'item_number' 		=> '',
			'button_text' 		=> 'Donate',
			'toggle_active'		=> array( 0 => true, 1 => false, 2 => false, 3 => false, 4 => false ),
			'intro_text' 		=> '',
			'outro_text' 		=> '',
			'customstylescript'	=> ''
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );
		global $wp_registered_widgets, $wp_registered_sidebars;
		$sidebars_widgets = wp_get_sidebars_widgets();
		//print_r($instance );
		?>
		<div class="pluginName">Doughnut - Paypal Donation<span class="pluginVersion"><?php echo DOUGHNUT_VERSION; ?></span></div>
		
		<script type="text/javascript">
			// Tabs function
			jQuery(document).ready(function($){				
				$(".gw-container").gwSortable();
				
				// Farbtastic function
				$("#tupro-<?php echo $this->id; ?> .pickcolor").click(function() {
					$(this).next().slideToggle();					
					$(this).next().farbtastic($(this).prev());	
					return false;
				});
				$('html').click(function() { $('.farbtastic-wrapper').fadeOut(); });
				$('.farbtastic').click(function(event){ event.stopPropagation(); });
				
				// Image uploader/picker/remove
				$("#tupro-<?php echo $this->id; ?> a.addImage").totalAddImages();
				$("#tupro-<?php echo $this->id; ?> a.removeImage").totalRemoveImages();
				
				// Widget background
				$("#tupro-<?php echo $this->id; ?>").closest(".widget-inside").addClass("tuproWidgetBg");
			});
		</script>

		<div id="tupro-<?php echo $this->id ; ?>" class="tuproControls tabbable tabs-left">
			<ul class="nav nav-tabs">
				<li class="<?php if ( $instance['toggle_active'][0] ) : ?>active<?php endif; ?>">General<input type="hidden" name="<?php echo $this->get_field_name( 'toggle_active' ); ?>[]" value="<?php echo esc_attr( $instance['toggle_active'][0] ); ?>" /></li>
				<li class="<?php if ( $instance['toggle_active'][1] ) : ?>active<?php endif; ?>">Advanced<input type="hidden" name="<?php echo $this->get_field_name( 'toggle_active' ); ?>[]" value="<?php echo esc_attr( $instance['toggle_active'][1] ); ?>" /></li>				
			</ul>
			<ul class="tab-content">
				<li class="tab-pane <?php if ( $instance['toggle_active'][0] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', $this->textdomain ); ?></label>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
							<span class="controlDesc"><?php _e( 'Give the title, of leave empty for no title.', $this->textdomain ); ?></span>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'email' ); ?>"><?php _e( 'Your Email Address', $this->textdomain ); ?></label>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'email' ); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" value="<?php echo esc_attr( $instance['email'] ); ?>" />
							<span class="controlDesc"><?php _e( 'Set your email address here.', $this->textdomain ); ?></span>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'live' ); ?>">
							<input class="checkbox" type="checkbox" <?php checked( $instance['live'], true ); ?> id="<?php echo $this->get_field_id( 'live' ); ?>" name="<?php echo $this->get_field_name( 'live' ); ?>" /><?php _e( 'Live', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e( 'If checked then the PayPal live version will be apply, else uses the PayPal Sandbox.', $this->textdomain ); ?></span>
						</li>							
						<li>
							<label for="<?php echo $this->get_field_id( 'item_name' ); ?>"><?php _e( 'Item Name', $this->textdomain ); ?></label>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'item_name' ); ?>" name="<?php echo $this->get_field_name( 'item_name' ); ?>" value="<?php echo esc_attr( $instance['item_name'] ); ?>" />
							<span class="controlDesc"><?php _e( 'Description of item when checkout.', $this->textdomain ); ?></span>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'item_number' ); ?>"><?php _e( 'Item Number', $this->textdomain ); ?></label>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'item_number' ); ?>" name="<?php echo $this->get_field_name( 'item_number' ); ?>" value="<?php echo esc_attr( $instance['item_number'] ); ?>" />							
							<span class="controlDesc"><?php _e( 'Pass-through variable for you to track product or service purchased or the contribution made.', $this->textdomain ); ?></span>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'button_text' ); ?>"><?php _e( 'Button Text', $this->textdomain ); ?></label>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'button_text' ); ?>" name="<?php echo $this->get_field_name( 'button_text' ); ?>" value="<?php echo esc_attr( $instance['button_text'] ); ?>" />
							<span class="controlDesc"><?php _e( 'The form button text/value.', $this->textdomain ); ?></span>
						</li>						
						<li>
							<label for="<?php echo $this->get_field_id( 'inactive_icon' ); ?>"><?php _e( 'Custom Fields', $this->textdomain ); ?></label>
							<div class="gw-container">
								<div class="gw-add-widget">
									<span class="controlDesc" style="margin-bottom: 12px;"><?php _e( 'Add custom fields here by hit the button below.', $this->textdomain ); ?></span>	
									<a href="#" class="button"><?php _e( 'Add Field', $this->textdomain ); ?></a>
								</div>
								<?php 
									if ( ! empty( $instance['fields'] ) ) {
										foreach ( $instance['fields'] as $key => $val ) {
											$this->doughnut_field_form( $val );
										}
									}
								?>
							</div>
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][1] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id('intro_text'); ?>"><?php _e( 'Intro Text', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e( 'This option will display addtional text before the widget content and supports HTML.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'intro_text' ); ?>" id="<?php echo $this->get_field_id( 'intro_text' ); ?>" rows="2" class="widefat"><?php echo esc_textarea($instance['intro_text']); ?></textarea>							
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('outro_text'); ?>"><?php _e( 'Outro Text', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e( 'This option will display addtional text after widget and supports HTML.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'outro_text' ); ?>" id="<?php echo $this->get_field_id( 'outro_text' ); ?>" rows="2" class="widefat"><?php echo esc_textarea($instance['outro_text']); ?></textarea>
							
						</li>				
						<li>
							<label for="<?php echo $this->get_field_id('customstylescript'); ?>"><?php _e( 'Custom Script & Stylesheet', $this->textdomain ) ; ?></label>
							<span class="controlDesc"><?php _e( 'Use this box for additional widget CSS style of custom javascript. Current widget selector: ', $this->textdomain ); ?><?php echo '<i>#' . $this->id . '</i>'; ?></span>
							<textarea name="<?php echo $this->get_field_name( 'customstylescript' ); ?>" id="<?php echo $this->get_field_id( 'customstylescript' ); ?>" rows="5" class="widefat code"><?php echo htmlentities($instance['customstylescript']); ?></textarea>
						</li>
						<li>
							<label><?php _e( 'Shortcode & Function', $this->textdomain ) ; ?></label>
							<span class="controlDesc">								
								<?php _e( 'Shortcode: ', $this->textdomain ); ?><tt><?php echo '['.$this->prefix.' id="' . $this->number . '"]'; ?></tt><br />					
								<?php _e( 'Function: ', $this->textdomain ); ?><tt><?php echo '&lt;?php echo doughnut(' . $this->number . '); ?&gt;'; ?></tt>
							</span>							
						</li>
					</ul>
				</li>
			</ul>
		</div>
	<?php
	}
}

?>