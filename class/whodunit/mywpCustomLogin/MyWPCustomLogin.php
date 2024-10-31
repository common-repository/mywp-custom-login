<?php

namespace whodunit\mywpCustomLogin;

use whodunit\mywpCustomLogin\traits\PluginTraitHasOptions;

/**
 * Plugin Class
 * Do not construct this object, use as singleton "MyWPCustomLogin::get_instance();"
 * This object will handle
 *  plugin's sub entities registration
 *  triggers and behaviors
 *  options
 */
class MyWPCustomLogin extends Plugin {
	use PluginTraitHasOptions;

	/**
	 * init
	 * Plugin init setup
	 *
	 * @return void
	 **/
	public function init() {
		//set default options meta key for storage, set default options, load options from db
		//related to Plugin PluginTraitHasOptions
		$this->set_options_meta_key( 'mywp-custom-login_settings' );
		$this->set_default_options( [
			'exported_from_erident' => false, // erident export flag.
			'login_settings'        => [
				'data_left'         => 'Thank you for creating with <a href="https://wordpress.org/">WordPress</a>.',
				'data_right'        => '&copy; 2022 All Rights Reserved',
				'image_logo'        =>  $this->get_assets_dir().'img/default-ecl-logo.png',
				'image_logo_width'  => 90,
				'image_logo_height' => 90,
				'power_text'        => 'Powered by Your Website',
				'login_width'       => 320,
				'login_radius'      => 4,
				'login_border'      => 'solid',
				'border_thick'      => 2,
				'border_color'      => '#dddddd',
				'login_bg'          => '#ffffff',
				'login_bg_opacity'  => 1, // Deprecated.
				'text_color'        => '#3c434a',
				'input_text_color'  => '#2c3338',
				'label_text_size'   => 14,
				'input_text_size'   => 24,
				'link_color'        => '#50575e',
				'check_shadow'      => 0,
				'link_shadow'       => '#ffffff',
				'check_form_shadow' => 0,
				'check_lost_pass'   => 0,
				'check_backtoblog'  => 0,
				'form_shadow'       => '#CCCCCC',
				'button_color'      => '#2271b1',
				'button_text_color' => '#FFFFFF',
				'top_bg_color'      => '#f1f1f1',
				'top_bg_image'      => '',
				'top_bg_repeat'     => 'repeat',
				'top_bg_xpos'       => 'left',
				'top_bg_ypos'       => 'top',
				'login_bg_image'    => '',
				'login_bg_repeat'   => 'repeat',
				'login_bg_xpos'     => 'left',
				'login_bg_ypos'     => 'top',
				'top_bg_size'       => 'auto'
			]
		] );

		add_action( 'init', function() {
			$this->load_options();
			//set plugin behaviors
			$this->init_package('BehaviorMigrateECL', 'behavior');
			$this->init_package('BehaviorLogin', 'behavior');
			//set admin page
			$this->init_package('PageAdminSettings', 'page');
			//set rest route page
			$this->init_package('RestRouteSettings', 'rest');
			//init translation
			$this->translate();
		}, 1 );
	}

}
