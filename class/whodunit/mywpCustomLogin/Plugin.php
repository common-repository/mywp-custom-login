<?php

namespace whodunit\mywpCustomLogin;

use whodunit\mywpCustomLogin\utility\WordPress;

/**
 * Whodunit abstract Plugin Class
 * Use inheritance, overwrite init class and provided trait for declaring a plugin quickly.
 * Use one by directory, you must respect WordPress convention and coding standard.
 */
abstract class Plugin {

	protected static $_instance;

	protected $basename;              // plugin basename
	protected $name;                  // plugin display name
	protected $version;               // exp : 1.0 ( exclude minor if 0 )
	protected $internal_version;      // exp : 1.0.1 ( complete )
	protected $display_version;       // exp : version 1.0 "definitive edition" ( decorated )
	protected $minimum_php_version;   //min php version
	protected $minimum_wp_version;    //min wp version
	protected $url;                   //plugin root url
	protected $dir;					  //plugin root dir
	protected $language_dir;          //plugin languages dir
	protected $text_domain;           //plugin text_domain //do not use form setting translation this is not WP compliant
	protected $relative_language_dir; //plugin languages relative dir ( for wp )

	/**
	 * set_instance
	 * Set an instance
	 *
	 * @param string, __FILE__ of root plugin file is needed for the filepath to be correctly defined
	 * @return void
	 **/
	public static function set_instance( $plugin_main_file_path ){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new static( $plugin_main_file_path );
		}
	}

	/**
	 * get_instance
	 * Get the instance
	 *
	 * @throws \Exception, trow an exception if not init first with set_instance
	 * @return Plugin, return an instance of himself
	 */
	public static function get_instance(){
		if( is_null( self::$_instance ) ){
			throw new \Exception( 'Please set instance first' );
		}
		return self::$_instance;
	}

	/**
	 * __construct
	 * do not call directly this is a singleton, use set_instance and get_instance instead
	 *
	 * @param $plugin_main_file_path string,  __FILE__ of root plugin file is needed for the filepath to be correctly defined
	 */
	public function __construct( $plugin_main_file_path ) {
		if ( ! function_exists( 'get_plugin_data' ) ) { require_once( ABSPATH . 'wp-admin/includes/plugin.php' ); }

		//get back plugin declaration data from WP comment
		$plugin_data = get_plugin_data( $plugin_main_file_path );

		//set plugin essential parameters
		$this->url                   = plugin_dir_url( $plugin_main_file_path );
		$this->dir                   = plugin_dir_path( $plugin_main_file_path );
		$this->file                  = $plugin_main_file_path;
		$this->basename              = plugin_basename( $this->dir );
		$this->version               = $plugin_data[ 'Version' ];
		//TODO preg_match two number version format and add a .0 if minor was exclude
		$this->internal_version      = $plugin_data[ 'Version' ];
		//TODO add some support for build code name
		$this->display_version       = __( 'version : ', 'mywp-custom-login' ).$plugin_data[ 'Version' ];
		$this->version               = $plugin_data[ 'Version' ];
		$this->name                  = $plugin_data[ 'Name' ];
		$this->author                = $plugin_data[ 'Author' ];
		$this->text_domain           = $plugin_data[ 'TextDomain' ];
		$this->minimum_wp_version    = ( ! empty( $plugin_data[ 'RequireWP' ] ) ) ? $plugin_data[ 'RequireWP' ] : '6.0';
		$this->minimum_php_version   = ( ! empty( $plugin_data[ 'RequirePHP' ] ) ) ? $plugin_data[ 'RequirePHP' ] : '7.0';
		$this->relative_language_dir = ( ! empty( $plugin_data[ 'DomainPath' ] ) ) ? $plugin_data[ 'DomainPath' ] : '/langagues';
		$this->language_dir          = preg_replace('#/+#','/', $this->dir.$this->relative_language_dir.'/' );

		//init plugin
		$this->init();
	}

	/**
	 * init
	 * init class, child can overwrite to add code to the init process
	 * TODO::maybe use "get_declared_traits" to check and autoload trait
	 *
	 * @return void
	 */
	public function init(){}

	/**
	 * init_package
	 * utility function to load functionality package like page, rest route, setting or behavior.
	 *
	 * @param string $name      Class name to init
	 * @param string $namespace Namespace to use
	 * @return mixed Class instance
	 **/
	public function init_package( $name, $namespace = null ) {
		//TODO think about namespace resolution
		$class_name = '\\whodunit\\mywpCustomLogin\\'.$namespace.'\\'.$name;
		return new $class_name();
	}

	/**
	 * translate
	 * try to load translation .mo file from text-domain and local.
	 * check in the WordPress translation dir first for public plugin translation
	 *
	 * @return void
	 */
	public function translate(){
		$mofile = $this->get_textdomaine().'-'.get_locale().'.mo';
		if( file_exists( WP_LANG_DIR.'/plugins/'.$mofile ) ) {
			load_textdomain( $this->get_textdomaine(), WP_LANG_DIR.'/plugins/'.$mofile );
		}elseif( file_exists( $this->get_dir().'languages/'.$mofile ) ){
			load_textdomain( $this->get_textdomaine(), $this->get_dir().'languages/'.$mofile );
		}
	}

	//----GETTER

	/**
	 * get_file
	 * return __FILE__ value of main plugin file '<wp_plugins_path>/<plugin_slug>.php'
	 *
	 * @return string file path
	 **/
	public function get_file(){
		return $this->file;
	}

	/**
	 * get_basename
	 * return plugin name without extension
	 *
	 * @return string file name
	 */
	public function get_basename(){
		return $this->basename;
	}

	/**
	 * get_name
	 * return plugin slugify name
	 *
	 * @return string plugin name
	 **/
	public function get_name(){
		return $this->name;
	}


	/**
	 * get_author
	 * return plugin author
	 *
	 * @return string plugin author
	 **/
	public function get_author(){
		return ( ! is_null( $this->author ) ) ? $this->author: 'Whodunit';
	}

	/**
	 * get_version
	 * TODO :: /!\ do not work properly yet ( see l67, l69 )
	 * return plugin version number, use parameter $display_mode to choose how it formatted.
	 *
	 * @param $display_mode string can be [ 'short', 'complete', 'decorated' ]
	 * - "short" is only the version number, minor version are exclude if 0. "1.0" "1.1" "1.1.1"
	 * - "complete" is only the version number too but include minor version number "1.0.0" "1.1.0" "1.1.1"
	 * - "decorated" include version label and name code. "version 1.0 release version"
	 *
	 * @return string, version number
	 **/
	public function get_version( $display_mode = 'short' ){
		switch( strtolower( $display_mode ) ){
			case 'complete'  : return $this->internal_version;
			case 'decorated' : return $this->display_version;
			default          : return $this->version;
		}
	}

	/**
	 * get_minimum_version
	 * return php or wp minimal version needed for the plugin
	 *
	 * @param $for can be [ 'WP', 'PHP' ]
	 * - "WP" will return WordPress minimal version
	 * - "PHP" will return PHP minimal version
	 * @return string minimal version needed for the plugin
	 **/
	public function get_minimum_version( $for = 'WP' ){
		switch( strtoupper( $for ) ){
			case 'PHP' : return $this->minimum_php_version;
			default    : return $this->minimum_wp_version;
		}
	}

	/**
	 * get_textdomaine
	 * return plugin text domain
	 *
	 * @return string plugin text domain
	 */
	public function get_textdomaine(){
		return $this->text_domain;
	}

	/**
	 * get_url
	 * return plugin root url
	 *
	 * @return string plugin url
	 */
	public function get_url(){
		return $this->url;
	}

	/**
	 * get_dir
	 * return plugin file path
	 *
	 * @return string plugin file path
	 */
	public function get_dir(){
		return $this->dir;
	}

	/**
	 * get_assets_url
	 * return plugin assets dir url
	 *
	 * @return string assets dir url
	 */
	public function get_assets_url(){
		return $this->url.'assets/';
	}

	/**
	 * get_assets_dir
	 * return plugin assets dir file path
	 *
	 * @return string plugin assets dir file path
	 */
	public function get_assets_dir(){
		return $this->dir.'assets/';
	}

	/**
	 * get_languages_dir
	 * return if a public translation exist return default WP plugin translation directory
	 * else return plugin languages dir file path
	 *
	 * @return string plugin languages dir file path
	 * @throws \Exception
	 */
	public function get_languages_dir(){
		if( WordPress::wordpress_dot_org_trad_exist() ){ return WP_LANG_DIR.'/plugins/'; }
		return $this->dir.'languages/';
	}

}
