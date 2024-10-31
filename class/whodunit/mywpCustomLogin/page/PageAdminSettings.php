<?php
namespace whodunit\mywpCustomLogin\page;

use whodunit\mywpCustomLogin\MyWPCustomLogin;

class PageAdminSettings extends Page {

	protected $parent_page_slug = 'options-general.php';
	protected $page_slug        = 'mywp_custom_login__settings';
	protected $page_template    = 'admin_settings';
	protected $page_cap         = 'manage_options';
	protected $page_title       = null;
	protected $page_menu_title  = null;
	protected $page_menu_pos    = 60;
	protected $page_icon        = null;

	/**
	 */
	function __construct(){
		$this->page_title      = __( 'General Settings', 'mywp-custom-login' );
		$this->page_menu_title = __( 'MyWP Custom Login', 'mywp-custom-login' );
		parent::__construct(
			$this->parent_page_slug,
			$this->page_slug,
			$this->page_template,
			$this->page_cap,
			$this->page_title,
			$this->page_menu_title,
			$this->page_menu_pos,
			$this->page_icon
		);
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue' ], 5 );
		//add_action( 'admin_init',            [ $this, 'plugin_admin_init' ], 10 );
        add_action( 'in_admin_header', [ $this, 'admin_header' ], 5 );
		//add_filter( 'admin_footer_text',     [ $this, 'admin_footer_text' ], 15, 1 );
		//add_filter( 'update_footer',         [ $this, 'admin_footer_update' ], 15, 1 );
		add_filter( 'plugin_action_links_'.MyWPCustomLogin::get_instance()->get_basename(), [ $this, 'plugin_action_links' ], 5, 1 );

	}

	/**
	 * plugin_admin_init
	 * use in hook admin_init do not call this method directly
	 * @return void
	 */
	public function plugin_admin_init(){}

	/**
	 * plugin_action_links
	 * use in hook plugin_action_links_{ plugin basename } do not call this method directly
	 * - add a setting link to the plugin list
	 * @return void
	 */
	public function plugin_action_links( $actions ) {
		$actions[] = '<a href="'. esc_url( get_admin_url( null, '/admin.php?page=mywp-custom-login__settings' ) ) .'">'.__( 'Settings', 'mywp-custom-login' ).'</a>';
		return $actions;
	}

	/**
	 * admin_footer_text
	 * use in hook admin_footer_text do not call this method directly
	 * @return void
	 */
	public function admin_footer_text( $text ){
		return $text;
	}

	/**
	 * admin_footer_update
	 * use in hook admin_footer_update do not call this method directly
	 * @return void
	 */
	public function admin_footer_update( $text ){
		return $text;
	}

	/**
	 * admin_enqueue
	 * use in hook admin_enqueue_scripts do not call this method directly
	 * - register styles, scripts and js translation in this page ( template has the enqueue call )
	 * @return void
	 */
	public function admin_enqueue(){
		if( ! is_admin() ){ return; }
		global $hook_suffix;
		if( 'settings_page_mywp_custom_login__settings' === $hook_suffix ){
			//register style
			wp_register_style(
				'mywp_custom_login__admin_style',
				MyWPCustomLogin::get_instance()->get_assets_url().'css/style_admin.min.css',
				[]
			);
			//register script
			wp_register_script(
				'mywp_custom_login__admin_setting_script',
				MyWPCustomLogin::get_instance()->get_assets_url().'js/admin_script.min.js',
				[ 'jquery', 'wp-i18n', 'wp-color-picker' ]
			);
			//register translation
			wp_set_script_translations(
				'mywp_custom_login__admin_setting_script',
				'mywp-custom-login',
				MyWPCustomLogin::get_instance()->get_languages_dir()
			);
		}
	}

	/**
	 * TODO : comment
	 */
	public function admin_header(){
		if( ! is_admin() ){ return; }
		global $hook_suffix;
		if( 'settings_page_mywp_custom_login__settings' === $hook_suffix ) {
			$this->load_part('admin_settings_header', true);
		}
	}


}