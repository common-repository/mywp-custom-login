<?php
namespace whodunit\mywpCustomLogin\behavior;

use MatthiasMullie\Minify\CSS;
use whodunit\mywpCustomLogin\MyWPCustomLogin;

class BehaviorLogin{

	/**
	 */
	public function __construct(){
		//add_action( 'login_head', [ $this, 'print_login_styles' ], 20 );
		$plugin_option_meta_key = MyWPCustomLogin::get_instance()->get_options_meta_key();

		//generate css file if it's do not exist then register and enqueue login style sheets
		add_action( 'init', [ $this, 'register_login_style_sheet' ], 20 );
		add_action( 'login_enqueue_scripts', [ $this, 'enqueue_login_style_sheet' ], 20 );

		//trigger css gen when plugin save options
		add_action( 'add_option_'.$plugin_option_meta_key, [ $this, 'generate_login_style_sheet_file' ], 10 );
		add_action( 'update_option_'.$plugin_option_meta_key, [ $this, 'generate_login_style_sheet_file' ], 10 );

		//other markup modifications
		//add_filter( 'admin_footer_text', [ $this, 'left_footer_text' ], 20, 1 );
		//add_filter( 'update_footer', [ $this, 'right_footer_text' ], 20, 1 );
		add_filter( 'login_headerurl', [ $this, 'login_logo_url' ], 20, 1 );
		add_filter( 'login_headertext', [ $this, 'login_logo_title' ], 20, 1 );
	}

	/**
	 * Filters the “Thank you” text displayed in the admin footer.
	 *
	 * @param string $text The existing footer text.
	 * @return string The modified footer text.
	 */
	public function left_footer_text( $text ){
		$settings = MyWPCustomLogin::get_instance()->get_options( 'login_settings' );
		$text     = isset( $settings['data_left'] ) && ! empty( $settings['data_left'] ) ? $settings['data_left'] : $text;
		return stripslashes( $text );
	}

	/**
	 * Filters the version/update text displayed in the admin footer.
	 *
	 * @param string $content The content that will be printed.
	 */
	public function right_footer_text( $content ){
		$settings = MyWPCustomLogin::get_instance()->get_options( 'login_settings' );
		$text     = isset( $settings['data_right'] ) && ! empty( $settings['data_right'] ) ? $settings['data_right'] : $content;
		return stripslashes( $text );
	}

	/**
	 * Change login logo URL.
	 */
	public function login_logo_url( $url ){
		return get_bloginfo( 'url' );
	}

	/**
	 * Change login logo title.
	 *
	 * @param string $text The existing header text.
	 * @return string
	 */
	public function login_logo_title( $text ){
		$settings   = MyWPCustomLogin::get_instance()->get_options( 'login_settings' );
		$logo_title = isset( $settings['power_text'] ) && ! empty( $settings['power_text'] ) ? $settings['power_text'] : $text;
		return $logo_title;
	}

	public function register_login_style_sheet(){
		$file_name = 'login_style.min.css';
		$file_path = MyWPCustomLogin::get_instance()->get_assets_dir().'css/'.$file_name;
		$file_url  = MyWPCustomLogin::get_instance()->get_assets_url().'css/'.$file_name;
		if( ! file_exists( $file_path ) ){ $this->generate_login_style_sheet_file(); }
		wp_register_style( 'mywp_custom_login__login_style', $file_url, [] );
	}

	public function generate_login_style_sheet_file(){
		$file_name = 'login_style.min.css';
		$file_path = MyWPCustomLogin::get_instance()->get_assets_dir().'css/'.$file_name;
		$css_style_sheet = $this->generate_login_style_sheet();
		$minifier = new CSS( $css_style_sheet );
		$minifier->minify( $file_path );
	}

	public function enqueue_login_style_sheet(){
		wp_enqueue_style( 'mywp_custom_login__login_style' );
	}

	public function generate_login_style_sheet(){
		$settings = MyWPCustomLogin::get_instance()->get_options( 'login_settings' );

		$page_bg_color  = isset( $settings['top_bg_color'] ) && ! empty( $settings['top_bg_color'] ) ? $settings['top_bg_color'] : '';
		$page_bg_image  = isset( $settings['top_bg_image'] ) && ! empty( $settings['top_bg_image'] ) ? $settings['top_bg_image'] : '';
		$page_bg_repeat = isset( $settings['top_bg_repeat'] ) && ! empty( $settings['top_bg_repeat'] ) ? $settings['top_bg_repeat'] : '';
		$page_bg_pos_x  = isset( $settings['top_bg_xpos'] ) && ! empty( $settings['top_bg_xpos'] ) ? $settings['top_bg_xpos'] : '';
		$page_bg_pos_y  = isset( $settings['top_bg_ypos'] ) && ! empty( $settings['top_bg_ypos'] ) ? $settings['top_bg_ypos'] : '';
		$page_bg_size   = isset( $settings['top_bg_size'] ) && ! empty( $settings['top_bg_size'] ) ? $settings['top_bg_size'] : '';

		$logo_image  = isset( $settings['image_logo'] ) && ! empty( $settings['image_logo'] ) ? $settings['image_logo'] : '';
		$logo_width  = isset( $settings['image_logo_width'] ) && ! empty( $settings['image_logo_width'] ) ? $settings['image_logo_width'] : '';
		$logo_height = isset( $settings['image_logo_height'] ) && ! empty( $settings['image_logo_height'] ) ? $settings['image_logo_height'] : '';

		$enable_form_box_shadow = isset( $settings['check_form_shadow'] ) ? $settings['check_form_shadow'] : 0;
		$enable_form_box_shadow = ( 'true' === strtolower( $enable_form_box_shadow ) ) ? true : false;

		$login_form_box_shadow = '';

		if ( $enable_form_box_shadow ) {
			$login_form_box_shadow = '0 4px 10px -1px ' . $settings['form_shadow'];
		}

		$remove_register_link = isset( $settings['check_lost_pass'] ) ? $settings['check_lost_pass'] : 0;
		$remove_register_link = ( 'true' === strtolower( $remove_register_link ) ) ? true : false;

		$remove_back_to_blog_link = isset( $settings['check_backtoblog'] ) ? $settings['check_backtoblog'] : 0;
		$remove_back_to_blog_link = ( 'true' === strtolower( $remove_back_to_blog_link ) ) ? true : false;

		$btn_text_color = isset( $settings['button_text_color'] ) && ! empty( $settings['button_text_color'] ) ? $settings['button_text_color'] : '';
		$btn_bg_color   = isset( $settings['button_color'] ) && ! empty( $settings['button_color'] ) ? $settings['button_color'] : '';

		$btn_bg_color_hover = ( ! empty( $btn_bg_color ) ) ? $btn_bg_color.'90' : '';

		$form_width         = isset( $settings['login_width'] ) && ! empty( $settings['login_width'] ) ? $settings['login_width'] : '';
		$form_border_radius = isset( $settings['login_radius'] ) && ! empty( $settings['login_radius'] ) ? $settings['login_radius'] : '';
		$form_border_width  = isset( $settings['border_thick'] ) && ! empty( $settings['border_thick'] ) ? $settings['border_thick'] : '';
		$form_border_style  = isset( $settings['login_border'] ) && ! empty( $settings['login_border'] ) ? $settings['login_border'] : '';
		$form_border_color  = isset( $settings['border_color'] ) && ! empty( $settings['border_color'] ) ? $settings['border_color'] : '';

		$login_form_bg_color = '';

		if ( isset( $settings['login_bg'] ) && ! empty( $settings['login_bg'] ) ) {
			$login_form_bg_color = $settings['login_bg'];
			if ( isset( $settings['login_bg_opacity'] ) ) {
				$login_form_bg_opacity = '' !== $settings['login_bg_opacity'] ? $settings['login_bg_opacity'] : 1;
				if ( false === stripos( $login_form_bg_color, 'rgba' ) && 1 > $login_form_bg_opacity ) {
					//convert $login_form_bg_opacity to hex val
					//$login_form_bg_color = $login_form_bg_color.$login_form_bg_opacity ;
				}
			}
		}

		$login_bg_image  = isset( $settings['login_bg_image'] ) && ! empty( $settings['login_bg_image'] ) ? $settings['login_bg_image'] : '';
		$login_bg_repeat = isset( $settings['login_bg_repeat'] ) && ! empty( $settings['login_bg_repeat'] ) ? $settings['login_bg_repeat'] : '';
		$login_bg_pos_x  = isset( $settings['login_bg_xpos'] ) && ! empty( $settings['login_bg_xpos'] ) ? $settings['login_bg_xpos'] : '';
		$login_bg_pos_y  = isset( $settings['login_bg_ypos'] ) && ! empty( $settings['login_bg_ypos'] ) ? $settings['login_bg_ypos'] : '';
		$label_font_color = isset( $settings['text_color'] ) && ! empty( $settings['text_color'] ) ? $settings['text_color'] : '';
		$label_font_size  = isset( $settings['label_text_size'] ) && ! empty( $settings['label_text_size'] ) ? $settings['label_text_size'] : '';
		$input_font_color = isset( $settings['input_text_color'] ) && ! empty( $settings['input_text_color'] ) ? $settings['input_text_color'] : '';
		$input_font_size  = isset( $settings['input_text_size'] ) && ! empty( $settings['input_text_size'] ) ? $settings['input_text_size'] : '';
		$link_color = isset( $settings['link_color'] ) && ! empty( $settings['link_color'] ) ? $settings['link_color'] : '';
		$enable_link_text_shadow = isset( $settings['check_shadow'] ) ? $settings['check_shadow'] : 0;
		$enable_link_text_shadow = ( 'true' === strtolower( $enable_link_text_shadow ) ) ? true : false;
		$login_link_text_shadow  = ( $enable_link_text_shadow ) ? $settings['link_shadow'] . ' 0 1px 0' : '';

		//begin html block
		$style = "html { background: none !important; }\r";
		//end html block

		//begin html body.login block
		$style .= "html body.login {\r";
		if ( ! empty( $page_bg_color ) ){ $style .= "background-color: ".esc_html( $page_bg_color )." !important;\r"; }
		if ( ! empty( $page_bg_image ) ){
			$style .= "background-image: url( ".esc_html( $page_bg_image )." ) !important;\r";
			if ( ! empty( $page_bg_repeat ) ){ $style .= "background-repeat: ".esc_html( $page_bg_repeat )." !important;\r"; }
			if ( ! empty( $page_bg_pos_x ) && ! empty( $page_bg_pos_y ) ){ $style .= "background-position:".esc_html( $page_bg_pos_x )." ".esc_html( $page_bg_pos_y )." !important;\r"; }
			if ( ! empty( $page_bg_size ) ){ $style .= "background-size: ".esc_html( $page_bg_size )." !important;\r"; }
		}
		$style .= "}\r";
		//end html body.login block

		//begin body.login div#login h1 a block
		$style .= "body.login div#login h1 a {\r";
		$style .= "margin: 0 auto;\r";
		if ( ! empty( $logo_image ) ){ $style .= "background-image: url(".esc_html( $logo_image ).") !important;\r"; }
		if ( ! empty( $logo_width ) || ! empty( $logo_height ) ){
			if ( ! empty( $logo_width ) && ! empty( $logo_height ) ){
				$style .= "background-size: ".esc_html( $logo_width )."px ".esc_html( $logo_height )."px;\r";
			}elseif ( ! empty( $logo_width ) && empty( $logo_height ) ){
				$style .= "background-size: ".esc_html( $logo_width )."px auto;\r";
			}elseif ( empty( $logo_width ) && ! empty( $logo_height ) ){
				$style .= "background-size: auto ".esc_html( $logo_height )."px;\r";
			}
		}
		if ( ! empty( $logo_width ) ){ $style .= "width: ".esc_html( $logo_width )."px;\r"; }
		if ( ! empty( $logo_height ) ){ $style .= "height: ".esc_html( $logo_height )."px;\r"; }
		$style .= "}\r";
		//end body.login div#login h1 a block

		//begin body.login #login block
		$style .= "body.login #login {\r";
		if ( ! empty( $form_width ) ) { $style .= "width: ".esc_html( $form_width )."px;\r"; }
		$style .= "}\r";

		//begin #loginform block
		$style .= "#loginform {\r";
		if ( ! empty( $form_border_radius ) ){ $style .= "border-radius:".esc_html( $form_border_radius )."px !important;\r"; }
		if ( ! empty( $form_border_color ) ){
			$style .= "border-color: ".esc_html( $form_border_color )." !important;\r";
			if ( ! empty( $form_border_width ) ){ $style .= "border-width: ".esc_html( $form_border_width )."px !important;\r"; }
			if ( ! empty( $form_border_style ) ){ $style .= "border-style: ".esc_html( $form_border_style )." !important;\r"; }
		}

		if ( ! empty( $login_form_bg_color ) ){ $style .= "background-color: ".esc_html( $login_form_bg_color )." !important;\r"; }
		if ( ! empty( $login_bg_image ) ){
			$style .= "background-image: url(".esc_html( $login_bg_image ).") !important;\r";
			if ( ! empty( $login_bg_repeat ) ){ $style .= "background-repeat: ".esc_html( $login_bg_repeat )." !important;\r"; }
			if ( ! empty( $login_bg_pos_x ) && ! empty( $login_bg_pos_y ) ){ $style .= "background-position: ".esc_html( $login_bg_pos_x )." ".esc_html( $login_bg_pos_y )." !important;\r"; }
		}

		if ( ! empty( $login_form_box_shadow ) ){
			$style .= "-moz-box-shadow:    ".esc_html( $login_form_box_shadow )." !important;\r";
			$style .= "-webkit-box-shadow: ".esc_html( $login_form_box_shadow )." !important;\r";
			$style .= "box-shadow:         ".esc_html( $login_form_box_shadow )." !important;\r";
		}
		$style .= "}\r";
		//end body.login #login block

		//begin body.login div#login form label, p#reg_passmail block
		$style .= "body.login div#login form label,\r";
		$style .= "p#reg_passmail {\r";
		if ( ! empty( $label_font_color ) ){ $style .= "color: ".esc_html( $label_font_color )." !important;\r"; }
		if ( ! empty( $label_font_size ) ){ $style .= "font-size: ".esc_html( $label_font_size )."px !important;\r"; }
		$style .= "}\r";
		//end body.login div#login form label, p#reg_passmail block

		//begin body.login #loginform p.submit .button-primary, body.wp-core-ui .button-primary block
		$style .= "body.login #loginform p.submit .button-primary,\r";
		$style .= "body.wp-core-ui .button-primary {\r";
		$style .= "border: none !important;\r";
		if ( ! empty( $btn_text_color ) ){ $style .= "color: ".esc_html( $btn_text_color )." !important;\r"; }
		if ( ! empty( $btn_bg_color ) ){ $style .= "background: ".esc_html( $btn_bg_color )." !important;\r"; }
		if ( ! empty( $login_link_text_shadow ) ){ $style .= "text-shadow: ".esc_html( $login_link_text_shadow )." !important;\r"; }
		$style .= "}\r";
		//end body.login #loginform p.submit .button-primary, body.wp-core-ui .button-primary block

		//begin body.login #loginform p.submit .button-primary:hover, body.login #loginform p.submit .button-primary:focus, body.wp-core-ui .button-primary:hover block
		$style .= "body.login #loginform p.submit .button-primary:hover,\r";
		$style .= "body.login #loginform p.submit .button-primary:focus,\r";
		$style .= "body.wp-core-ui .button-primary:hover {\r";
		if ( ! empty( $btn_bg_color_hover ) ) { $style .= "background: ".esc_html( $btn_bg_color_hover )." !important;\r"; }
		$style .= "}\r";
		//end body.login #loginform p.submit .button-primary:hover, body.login #loginform p.submit .button-primary:focus, body.wp-core-ui .button-primary:hover block

		//begin body.login div#login form .input, .login input[type="text"] block
		$style .= "body.login div#login form .input,\r";
		$style .= ".login input[type=\"text\"] {\r";
		if ( ! empty( $input_font_color ) ){ $style .= "color: ".esc_html( $input_font_color )." !important;\r"; }
		if ( ! empty( $input_font_size ) ){ $style .= "font-size: ".esc_html( $input_font_size )."px !important;\r"; }
		$style .= "}\r";
		//end body.login div#login form .input, .login input[type="text"] block

		//begin body.login #nav a, body.login #backtoblog a block
		$style .= "body.login #nav a, body.login #backtoblog a {\r";
		if ( ! empty( $link_color ) ){ $style .= "color: ".esc_html( $link_color )." !important;\r"; }
		$style .= "}\r";
		//end body.login #nav a, body.login #backtoblog a block

		//begin body.login #nav, body.login #backtoblog block
		$style .= "body.login #nav,\r";
		$style .= "body.login #backtoblog {\r";
		if ( ! empty( $login_link_text_shadow ) ){ $style .= "text-shadow: ".esc_html( $login_link_text_shadow )." !important;\r"; }
		$style .= "}\r";
		//end body.login #nav, body.login #backtoblog block

		//begin .login form .input, login input[type=text], .wp-core-ui .button-primary:focus block
		$style .= ".login form .input,\r";
		$style .= ".login input[type=text],\r";
		$style .= ".wp-core-ui .button-primary:focus {\r";
		$style .= "box-shadow: none !important;\r";
		$style .= "}\r";
		//end .login form .input, login input[type=text], .wp-core-ui .button-primary:focus block

		//begin body.login #loginform p.submit .button-primary, body.wp-core-ui .button-primary block
		$style .= "body.login #loginform p.submit .button-primary,\r";
		$style .= "body.wp-core-ui .button-primary {\r";
		$style .= "box-shadow: none;\r";
		$style .= "}\r";
		//end body.login #loginform p.submit .button-primary, body.wp-core-ui .button-primary block

		//begin body.login p#nav block
		$style .= "body.login p#nav {\r";
		if ( $remove_register_link ){ $style .= "display: none !important;\r"; }
		$style .= "}\r";
		//end body.login p#nav block

		//begin body.login #backtoblog block
		$style .= "body.login p#backtoblog {\r";
		if ( $remove_back_to_blog_link ){ $style .= "display: none !important;\r"; }
		$style .= "}\r";
		//end body.login #backtoblog block
		
		return $style;
	}

}