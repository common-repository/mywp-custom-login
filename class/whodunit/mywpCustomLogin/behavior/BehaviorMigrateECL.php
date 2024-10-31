<?php
namespace whodunit\mywpCustomLogin\behavior;

use whodunit\mywpCustomLogin\MyWPCustomLogin;

class BehaviorMigrateECL{

	protected $options_map = [];

	/**
	 */
	public function __construct(){
		$this->options_map = [
			'dashboard_data_left'         => [ 'data_left' , null ],
			'dashboard_data_right'        => [ 'data_right' , null ],
			'dashboard_image_logo'        => [ 'image_logo' , null ],
			'dashboard_image_logo_width'  => [ 'image_logo_width' , null ],
			'dashboard_image_logo_height' => [ 'image_logo_height' , null ],
			'dashboard_power_text'        => [ 'power_text' , null ],
			'dashboard_login_width'       => [ 'login_width' , null ],
			'dashboard_login_radius'      => [ 'login_radius' , null ],
			'dashboard_login_border'      => [ 'login_border' , null ],
			'dashboard_border_thick'      => [ 'border_thick' , null ],
			'dashboard_border_color'      => [ 'border_color' , null ],
			'dashboard_login_bg'          => [ 'login_bg' , null ],
			'dashboard_text_color'        => [ 'text_color' , null ],
			'dashboard_input_text_color'  => [ 'input_text_color' , null ],
			'dashboard_label_text_size'   => [ 'label_text_size' , null ],
			'dashboard_input_text_size'   => [ 'input_text_size' , null ],
			'dashboard_link_color'        => [ 'link_color' , null ],
			'dashboard_check_shadow'      => [ 'check_shadow' , null ],
			'dashboard_link_shadow'       => [ 'link_shadow' , null ],
			'dashboard_check_form_shadow' => [ 'check_form_shadow' , null ],
			'dashboard_check_lost_pass'   => [ 'check_lost_pass' , null ],
			'dashboard_check_backtoblog'  => [ 'check_backtoblog' , null ],
			'dashboard_form_shadow'       => [ 'form_shadow' , null ],
			'dashboard_button_color'      => [ 'button_color' , null ],
			'dashboard_button_text_color' => [ 'button_text_color' , null ],
			'top_bg_color'                => [ 'top_bg_color' , null ],
			'top_bg_image'                => [ 'top_bg_image' , null ],
			'top_bg_repeat'               => [ 'top_bg_repeat' , null ],
			'top_bg_xpos'                 => [ 'top_bg_xpos' , null ],
			'top_bg_ypos'                 => [ 'top_bg_ypos' , null ],
			'login_bg_image'              => [ 'login_bg_image' , null ],
			'login_bg_repeat'             => [ 'login_bg_repeat' , null ],
			'login_bg_xpos'               => [ 'login_bg_xpos' , null ],
			'login_bg_ypos'               => [ 'login_bg_ypos' , null ],
			'top_bg_size'                 => [ 'top_bg_size' , null ],
		];

		add_action( 'init', [ $this, 'migrate_erident_custom_login_dashboard_parameters' ], 10 );
	}

	public function migrate_erident_custom_login_dashboard_parameters(){
		//update_option( 'plugin_erident_settings', $post_data );
		$plugin = MyWPCustomLogin::get_instance();
		$erident_options = get_option( 'plugin_erident_settings' );
		$exported_from_erident = boolval( $plugin->get_options( 'exported_from_erident' ) );
		if( false !== $erident_options && false === $exported_from_erident ){
			$new_options     = [];
			foreach( $erident_options as $option_key => $option_value ) {
				if( ! isset( $this->options_map[ $option_key ] ) ){ continue; }
				$new_options[ $this->options_map[ $option_key ][ 0 ] ] = ( is_callable( $this->options_map[ $option_key ][ 1 ] ) )
					? $this->options_map[ $option_key ][ 1 ]( $option_value ) : $option_value;
			}
			$plugin->save_options( [
				'exported_from_erident' => date( DATE_ATOM ),
				'login_settings'        => $new_options
			] );
		}
	}

}